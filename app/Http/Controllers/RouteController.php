<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RouteUp;
use App\Models\ScheduleHasRoute; // Ensure this line is included
use App\Models\RouteDown;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log; // Add this line

class RouteController extends Controller
{
    public function index()
    {
        return view('admin.manageRoute');
    }
    public function getOrigins()
    {
        return RouteUp::notDeleted()->get();
    }

public function getDestinations()
{
    $destinations = RouteDown::all();
    return response()->json($destinations);
}

    public function getOrigin($id)
    {
        return RouteUp::findOrFail($id);
    }

    public function getOriginDestinations($id)
    {
        $origin = RouteUp::findOrFail($id);
        return $origin->destinations;
    }

    public function storeOrigin(Request $request)
    {
        Log::info('Test point reached in storeOrigin');
        Log::info('Request data:', $request->all());
        try {
            $validated = $request->validate([
                'Origin' => 'required|string|max:255',
                'Active' => 'required|integer|in:0,1',
                'destinations' => 'required|array|min:1',
                'destinations.*' => 'exists:route_down,idRouteDown',
            ]);
            

            DB::beginTransaction();

            $origin = RouteUp::create([
                'Origin' => $validated['Origin'],
                'Active' => $validated['Active'],
            ]);

            $schedules = Schedule::all();

            foreach ($validated['destinations'] as $destinationId) {
                foreach ($schedules as $schedule) {
                    ScheduleHasRoute::create([
                        'RouteUpID' => $origin->RouteID,
                        'RouteDownID' => $destinationId,
                        'ScheduleID' => $schedule->ScheduleID,
                    ]);
                }
            }

            DB::commit();
            return response()->json($origin, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing origin: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the origin. ' . $e->getMessage()], 500);
        }
    }


    public function updateOrigin(Request $request, $id)
{
    try {
        $origin = RouteUp::findOrFail($id);
        
        $validated = $request->validate([
            'Origin' => 'sometimes|required|string|max:255',
            'Active' => 'sometimes|required|integer|in:0,1',
            'destinations' => 'sometimes|required|array|min:1',
            'destinations.*' => 'exists:route_down,idRouteDown',
        ]);

        DB::beginTransaction();

        $origin->update($validated);

        if (isset($validated['destinations'])) {
            ScheduleHasRoute::where('RouteUpID', $origin->RouteID)->delete();

            $schedules = Schedule::all();

            foreach ($validated['destinations'] as $destinationId) {
                foreach ($schedules as $schedule) {
                    ScheduleHasRoute::create([
                        'RouteUpID' => $origin->RouteID,
                        'RouteDownID' => $destinationId,
                        'ScheduleID' => $schedule->ScheduleID,
                    ]);
                }
            }
        }

        DB::commit();
        return response()->json($origin);
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollback();
        return response()->json(['error' => $e->errors()], 422);
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error updating origin: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred while updating the origin. ' . $e->getMessage()], 500);
    }
}

    public function deleteOrigin($id)
    {
        try {
            $origin = RouteUp::findOrFail($id);

            DB::beginTransaction();

            // Soft delete the origin
            $origin->update(['Deleted' => true, 'Active' => false]);

            DB::commit();
            return response()->json(['message' => 'Origin deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function storeDestination(Request $request)
    {
        try {
            $validated = $request->validate([
                'Destination' => 'required|string|max:255',
                'Price' => 'required|numeric',
                'Active' => 'required|boolean',
            ]);

            $destination = RouteDown::create($validated);

            return response()->json($destination, 201);
        } catch (\Exception $e) {
            Log::error('Error storing destination: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the destination. ' . $e->getMessage()], 500);
        }
    }

    public function updateDestination(Request $request, $id)
    {
        try {
            $destination = RouteDown::findOrFail($id);
            
            $validated = $request->validate([
                'Destination' => 'sometimes|required|string|max:255',
                'Price' => 'sometimes|required|numeric',
                'Active' => 'sometimes|required|boolean',
            ]);

            $destination->update($validated);
            return response()->json($destination);
        } catch (\Exception $e) {
            Log::error('Error updating destination: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the destination. ' . $e->getMessage()], 500);
        }
    }

    public function deleteDestination($id)
    {
        try {
            $destination = RouteDown::findOrFail($id);
            $destination->delete();
            return response()->json(['message' => 'Destination deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting destination: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while deleting the destination. ' . $e->getMessage()], 500);
        }
    }
}