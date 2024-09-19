<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RouteUp;
use App\Models\RouteDown;
use App\Models\ScheduleHasRoute;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use App\Models\BookingModel;  // Add this line
use Illuminate\Support\Facades\Log;


class BookingController extends Controller
{
    public function showBookingForm(Request $request)
    {
        $scheduleId = $request->input('schedule_id'); // or $request->route('scheduleId') for route parameter
    
        $origins = RouteUp::where('Active', 1)
                          ->distinct()
                          ->get(['RouteID', 'Origin']);
    
        $schedules = Schedule::orderBy('DepartureTime')->get();
    
        return view('booking', [
            'origins' => $origins,
            'schedules' => $schedules,
            'scheduleId' => $scheduleId // Pass the scheduleId to the view
        ]);
    }

    public function fetchDestinations($routeUpID)
    {
        try {
            Log::info('Fetching destinations for RouteUpID: ' . $routeUpID);

            $routeDownIDs = ScheduleHasRoute::where('RouteUpID', $routeUpID)
                                            ->pluck('RouteDownID')
                                            ->unique();

            Log::info('RouteDownIDs found: ' . implode(', ', $routeDownIDs->toArray()));

            $destinations = RouteDown::whereIn('idRouteDown', $routeDownIDs)
                                     ->where('Active', 1)
                                     ->distinct()
                                     ->get(['idRouteDown', 'Destination', 'Price']);

            Log::info('Destinations found: ' . $destinations->toJson());

            return response()->json($destinations);
        } catch (\Exception $e) {
            Log::error('Error in fetchDestinations: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showBooking($scheduleId)
    {
        Log::info('showBooking method called with schedule_id:', ['schedule_id' => $scheduleId]);

        $schedule = Schedule::findOrFail($scheduleId);

        Log::info('Schedule found in showBooking:', [
            'id' => $schedule->ScheduleID,
            'departureTime' => $schedule->DepartureTime
        ]);

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
    
        Log::info('Received schedule_id in showCustomerForm:', ['schedule_id' => $scheduleId]);
        Log::info('Received destination_id in showCustomerForm:', ['destination_id' => $destinationId]);
    
        $origin = RouteUp::find($originId);
        $destination = RouteDown::find($destinationId);
        $schedule = Schedule::find($scheduleId);
    
        if (!$schedule) {
            Log::error('No schedule found for schedule_id:', ['schedule_id' => $scheduleId]);
            return redirect()->back()->with('error', 'Schedule not found. Please try again.');
        }
    
        if (!$destination) {
            Log::error('No destination found for destination_id:', ['destination_id' => $destinationId]);
            return redirect()->back()->with('error', 'Destination not found. Please try again.');
        }
    
        $totalPrice = floatval($destination->Price) * intval($seats);
    
        return view('customer', [
            'origin' => $origin,
            'destination' => $destination,
            'seats' => $seats,
            'schedule' => $schedule,
            'totalPrice' => $totalPrice,
            'scheduleId' => $scheduleId // Pass scheduleId to the view
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
    
            Log::info('Validation successful:', $validatedData);
    
            $origin = RouteUp::findOrFail($validatedData['origin_id']);
            $destination = RouteDown::findOrFail($validatedData['destination_id']);
            $schedule = Schedule::findOrFail($validatedData['schedule_id']);
            $seats = $validatedData['seats'];
            $totalPrice = $validatedData['total_price'];
    
            $showQrCode = $validatedData['payment_method'] === 'online_qr';
    
            // Determine the system based on login status
            $system = Auth::check() ? 'store' : 'online';
    
            // If payment method is cash, save the booking immediately
            if ($validatedData['payment_method'] === 'cash') {
                $booking = new BookingModel;
                $booking->Seat = $seats;
                $booking->Name = $validatedData['customer_name'];
                $booking->Phone = $validatedData['phone'];
                $booking->System = $system;
                $booking->save();
    
                Log::info('Cash booking saved successfully:', $booking->toArray());
    
                return view('summary', compact('validatedData', 'origin', 'destination', 'schedule', 'seats', 'totalPrice', 'showQrCode', 'booking', 'system'));
            }
    
            return view('summary', compact('validatedData', 'origin', 'destination', 'schedule', 'seats', 'totalPrice', 'showQrCode', 'system'));
    
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
            'seats' => 'required|integer',
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
        ]);
    
        try {
            if ($request->hasFile('payment_slip')) {
                $file = $request->file('payment_slip');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
    
                $system = Auth::check() ? 'store' : 'online';
    
                $booking = new BookingModel;
                $booking->Seat = $request->seats;
                $booking->Name = $request->customer_name;
                $booking->Phone = $request->phone;
                $booking->System = $system;
                $booking->save();
    
                Log::info('Booking saved successfully:', $booking->toArray());
    
                return redirect()->route('booking.confirmation', ['id' => $booking->BookingID])->with('success', 'Booking completed successfully!');
            }
        } catch (\Exception $e) {
            Log::error('Error in uploadSlip:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to process booking. Please try again.');
        }
    
        return redirect()->back()->with('error', 'Failed to upload payment slip.');
    }

    public function showConfirmation($id)
{
    $booking = BookingModel::findOrFail($id);
    return view('confirmation', compact('booking'));
}
}
