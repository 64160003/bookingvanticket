<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RouteUp;
use App\Models\RouteDown;
use App\Models\ScheduleHasRoute;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;
use App\Models\PaymentModel;
use App\Models\BookingModel;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $paymentController;

    public function __construct(PaymentController $paymentController)
    {
        $this->paymentController = $paymentController;
    }

    public function showBookingForm(Request $request)
    {
        $scheduleId = $request->input('schedule_id');
    
        $origins = RouteUp::where('Active', 1)
                          ->distinct()
                          ->get(['RouteID', 'Origin']);
    
        $schedules = Schedule::orderBy('DepartureTime')->get();
    
        return view('booking', [
            'origins' => $origins,
            'schedules' => $schedules,
            'scheduleId' => $scheduleId
        ]);
    }

    public function fetchDestinations($routeUpID)
    {
        try {
            Log::info('Fetching destinations for RouteUpID: ' . $routeUpID);

            $routeDownIDs = ScheduleHasRoute::where('RouteUpID', $routeUpID)
                                            ->pluck('RouteDownID')
                                            ->unique();

            $destinations = RouteDown::whereIn('idRouteDown', $routeDownIDs)
                                     ->where('Active', 1)
                                     ->distinct()
                                     ->get(['idRouteDown', 'Destination', 'Price']);

            return response()->json($destinations);
        } catch (\Exception $e) {
            Log::error('Error in fetchDestinations: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showBooking($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);

        $origins = RouteUp::where('Active', 1)
                          ->distinct()
                          ->get(['RouteID', 'Origin']);

        return view('booking', [
            'origins' => $origins,
            'scheduleId' => $scheduleId,
            'departureTime' => $schedule->DepartureTime
        ]);
    }

    public function showCustomerForm(Request $request)
    {
        $scheduleId = $request->input('schedule_id');
        $originId = $request->input('origin_id');
        $destinationId = $request->input('destination_id');
        $seats = $request->input('seats');
    
        $origin = RouteUp::find($originId);
        $destination = RouteDown::find($destinationId);
        $schedule = Schedule::find($scheduleId);
    
        if (!$schedule) {
            return redirect()->back()->with('error', 'Schedule not found. Please try again.');
        }
    
        if (!$destination) {
            return redirect()->back()->with('error', 'Destination not found. Please try again.');
        }
    
        $totalPrice = floatval($destination->Price) * intval($seats);
    
        return view('customer', [
            'origin' => $origin,
            'destination' => $destination,
            'seats' => $seats,
            'schedule' => $schedule,
            'totalPrice' => $totalPrice,
            'scheduleId' => $scheduleId
        ]);
    }

    public function showSummary(Request $request)
{
    Log::info('showSummary method called with data:', $request->all());

    try {
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'payment_method' => 'required|string|in:cash,online_qr',
            'origin_id' => 'required',
            'destination_id' => 'required',
            'seats' => 'required|integer',
            'schedule_id' => 'required',
            'total_price' => 'required|numeric',
        ]);

        $origin = RouteUp::findOrFail($validatedData['origin_id']);
        $destination = RouteDown::findOrFail($validatedData['destination_id']);
        $schedule = Schedule::findOrFail($validatedData['schedule_id']);
        $seats = $validatedData['seats'];
        $totalPrice = $validatedData['total_price'];

        $showQrCode = $validatedData['payment_method'] === 'online_qr';

        $system = Auth::check() ? 'store' : 'online';

        $booking = new BookingModel;
        $booking->Seat = $seats;
        $booking->Name = $validatedData['customer_name'];
        $booking->Phone = $validatedData['phone'];
        $booking->System = $system;
        $booking->RouteUpID = $origin->RouteID;
        $booking->RouteDownID = $destination->idRouteDown;
        $booking->ScheduleID = $schedule->ScheduleID;
        $booking->save();

        // Create a pending payment for QR Code Online
        if ($validatedData['payment_method'] === 'online_qr') {
            try {
                $payment = $this->paymentController->createPayment($booking->BookingID, 'Online QR Code', $totalPrice, 0); // 0 for pending status
            } catch (\Exception $e) {
                Log::error('Failed to create payment record:', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'An error occurred while processing your payment. Please try again.');
            }
        } elseif ($validatedData['payment_method'] === 'cash') {
            try {
                $payment = $this->paymentController->createPayment($booking->BookingID, 'Cash', $totalPrice, 1); // 1 for confirmed status
            } catch (\Exception $e) {
                Log::error('Failed to create payment record:', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'An error occurred while processing your payment. Please try again.');
            }
        }

        return view('summary', compact('validatedData', 'origin', 'destination', 'schedule', 'seats', 'totalPrice', 'showQrCode', 'booking', 'system'));
    } catch (\Exception $e) {
        Log::error('Error in showSummary:', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'An error occurred. Please try again.');
    }
}

    public function uploadSlip(Request $request)
{
    Log::info('uploadSlip method called with data:', $request->all());

    $request->validate([
        'payment_slip' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'booking_id' => 'required|exists:booking,BookingID',
        'total_price' => 'required|numeric',
    ]);

    try {
        if ($request->hasFile('payment_slip')) {
            $file = $request->file('payment_slip');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);

            $booking = BookingModel::findOrFail($request->booking_id);
            
            // Update the booking status if needed
            // $booking->status = 'paid';
            // $booking->save();

            $payment = $this->paymentController->createPayment($booking->BookingID, 'Online QR Code', $request->total_price);

            Log::info('Payment saved successfully:', ['payment' => $payment->toArray()]);

            return redirect()->route('booking.confirmation', ['id' => $booking->BookingID])->with('success', 'Payment completed successfully!');
        }
    } catch (\Exception $e) {
        Log::error('Error in uploadSlip:', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Failed to process payment. Please try again.');
    }

    return redirect()->back()->with('error', 'Failed to upload payment slip.');
}

    public function showConfirmation($id)
    {
        $booking = BookingModel::findOrFail($id);
        return view('confirmation', compact('booking'));
    }

    public function search(Request $request)
    {
        $phone = $request->input('phone');
    
        // Search for bookings where the phone number contains the input (partial search)
        $bookings = BookingModel::where('Phone', 'like', '%' . $phone . '%')
            ->with(['origin', 'destination', 'schedule'])
            ->get();
    
        if ($bookings->isNotEmpty()) {
            // Handle multiple results by returning them as a list
            $payments = PaymentModel::whereIn('BookingID', $bookings->pluck('BookingID'))->get();
            Log::info('Bookings found:', ['bookings' => $bookings->toArray(), 'payments' => $payments->toArray()]);
            return view('search', ['bookings' => $bookings, 'payments' => $payments]);
        } else {
            return view('search', ['error' => 'No bookings found for this phone number.']);
        }
    }
}