<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RouteUp;
use App\Models\RouteDown;
use App\Models\ScheduleHasRoute;
use App\Models\Schedule;
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
        Log::info('showSummary method called with schedule_id:', ['schedule_id' => $request->input('schedule_id')]);
    
        try {
            // Log all incoming request data
            Log::info('Received data in showSummary:', $request->all());
            
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
    
            $origin = RouteUp::find($validatedData['origin_id']);
            $destination = RouteDown::find($validatedData['destination_id']);
            $schedule = Schedule::find($validatedData['schedule_id']);
            $seats = $validatedData['seats'];
            $totalPrice = floatval($destination->Price) * intval($seats);  // Calculate total price
    
            Log::info('Origin:', ['origin' => $origin]);
            Log::info('Destination:', ['destination' => $destination]);
            Log::info('Schedule:', ['schedule' => $schedule]);

            // Determine if the QR code should be displayed
            $showQrCode = $validatedData['payment_method'] === 'online_qr';
    
            return view('summary', compact('validatedData', 'origin', 'destination', 'schedule', 'seats', 'totalPrice', 'showQrCode'));
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('General error in showSummary:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function uploadSlip(Request $request){
    $request->validate([
        'payment_slip' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Handle file upload
    if ($request->hasFile('payment_slip')) {
        $file = $request->file('payment_slip');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $filename);

        // Here, you can save the file path to the database if needed
        // e.g., $paymentSlip->file_path = 'uploads/' . $filename;

        return redirect()->back()->with('success', 'Payment slip uploaded successfully!');
    }

    return redirect()->back()->with('error', 'Failed to upload payment slip.');
}
}
