<?php
namespace App\Http\Controllers;

use App\Models\DayType;
use App\Models\ScheduleHasDayType;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::where('Active', 1)->get();
        $currentDate = Carbon::now()->format('d-m-Y');
    
        foreach ($schedules as $schedule) {
            $schedule->formattedDepartureTime = Carbon::parse($schedule->DepartureTime)->format('H:i');
        }
        
        return view('welcome', compact('schedules', 'currentDate'));
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function manageSchedule()
    {
        $schedules = Schedule::with(['scheduleHasDayTypes', 'dayTypes'])->get();

        foreach ($schedules as $schedule) {
            $schedule->formattedDepartureTime = Carbon::parse($schedule->DepartureTime)->format('H:i');
            $schedule->days = $schedule->scheduleHasDayTypes->pluck('day')->toArray();
            $schedule->dayTypeName = $schedule->dayTypes->first()->DayTypeName ?? 'N/A';
        }
        
        return view('admin.manageSchedule', compact('schedules'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
    
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'departureTime' => 'required',
                'daysOfWeek' => 'required|array',
                'dayTypeName' => 'required|string',
                'active' => 'required|boolean',
            ]);
    
            // Create the schedule
            $schedule = Schedule::create([
                'DepartureTime' => $validatedData['departureTime'],
                'Active' => $validatedData['active'],
            ]);
    
            // Create or find the day type
            $dayType = DayType::firstOrCreate(
                ['DayTypeName' => $validatedData['dayTypeName']],
                ['Active' => 1]
            );
    
            // Create an array to store all the new ScheduleHasDayType entries
            $scheduleHasDayTypes = [];
    
            // Loop through each selected day of the week
            foreach ($validatedData['daysOfWeek'] as $day) {
                $scheduleHasDayType = ScheduleHasDayType::create([
                    'ScheduleID' => $schedule->ScheduleID,
                    'DayTypeID' => $dayType->DayTypeID,
                    'Day' => $day,
                ]);
                $scheduleHasDayTypes[] = $scheduleHasDayType;
            }
    
            DB::commit();
            return response()->json([
                'message' => 'Schedule added successfully', 
                'schedule' => $schedule,
                'scheduleDays' => $scheduleHasDayTypes
            ]);
    
        } catch (\Exception $e) {
            // Rollback the transaction in case of errors
            DB::rollBack();
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    

    public function show($id)
    {
        try {
            $schedule = Schedule::with('scheduleHasDayTypes', 'dayTypes')->findOrFail($id);
            
            return response()->json([
                'id' => $schedule->id,
                'DepartureTime' => $schedule->DepartureTime,
                'dayTypeName' => $schedule->dayTypes->first()->DayTypeName ?? '',
                'Active' => $schedule->Active,
                'days' => $schedule->scheduleHasDayTypes->pluck('Day')->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
    
        try {
            $schedule = Schedule::findOrFail($id);
    
            $validatedData = $request->validate([
                'departureTime' => 'required',
                'daysOfWeek' => 'required|array',
                'dayTypeName' => 'required|string',
                'active' => 'required|boolean',
            ]);
    
            $schedule->update([
                'DepartureTime' => $validatedData['departureTime'],
                'Active' => $validatedData['active'],
            ]);
    
            // Find or create the day type
            $dayType = DayType::firstOrCreate(
                ['DayTypeName' => $validatedData['dayTypeName']],
                ['Active' => 1]
            );
    
            // Delete existing schedule day types
            $schedule->scheduleHasDayTypes()->delete();
    
            // Create new schedule day types
            foreach ($validatedData['daysOfWeek'] as $day) {
                ScheduleHasDayType::create([
                    'ScheduleID' => $schedule->ScheduleID,
                    'DayTypeID' => $dayType->DayTypeID,
                    'Day' => $day,
                ]);
            }
    
            DB::commit();
            return response()->json(['message' => 'Schedule updated successfully', 'schedule' => $schedule]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
    
        try {
            $schedule = Schedule::findOrFail($id);
            
            // Delete associated schedule day types
            $schedule->scheduleHasDayTypes()->delete();
            
            // Delete the schedule
            $schedule->delete();
    
            DB::commit();
            return response()->json(['message' => 'Schedule deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function toggleActive($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->Active = !$schedule->Active;
            $schedule->save();
        
            return response()->json(['message' => 'Schedule status updated successfully', 'active' => $schedule->Active]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}