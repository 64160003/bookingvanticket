<?php
namespace App\Http\Controllers;

use App\Models\DayType;
use App\Models\ScheduleHasDayType;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // เพิ่มบรรทัดนี้

class ScheduleController extends Controller
{
    public function index()
{
    // ดึงวันที่และวันในสัปดาห์ปัจจุบัน
    $currentDate = Carbon::now()->format('d-m-Y');
    $currentDayOfWeek = Carbon::now()->format('l'); // วันในสัปดาห์ เช่น "Monday", "Tuesday", "Wednesday", etc.

    // ดึงข้อมูลตารางที่ active และตรงกับวันในสัปดาห์ปัจจุบัน
    $schedules = Schedule::where('Active', 1)
                ->whereHas('scheduleHasDayTypes', function ($query) use ($currentDayOfWeek) {
                    $query->where('Day', $currentDayOfWeek);
                })
                ->get();

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
            $schedule = Schedule::with(['scheduleHasDayTypes', 'dayTypes'])->findOrFail($id);
            
            $scheduleData = [
                'id' => $schedule->ScheduleID,
                'DepartureTime' => $schedule->DepartureTime,
                'Active' => $schedule->Active,
                'dayTypeName' => $schedule->dayTypes->first()->DayTypeName ?? '',
                'days' => $schedule->scheduleHasDayTypes->pluck('Day')->toArray(),
                'RouteID' => $schedule->scheduleHasDayTypes->first()->RouteID ?? null,
                'StartDateAt' => $schedule->scheduleHasDayTypes->first()->StartDateAt ?? null,
                'EndDateAt' => $schedule->scheduleHasDayTypes->first()->EndDateAt ?? null,
            ];

            Log::info("Schedule data fetched:", $scheduleData);
            
            return response()->json($scheduleData);
        } catch (\Exception $e) {
            Log::error("Error in ScheduleController@show: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        // ใช้ $id ที่รับมาในฟังก์ชันแทน $ScheduleID
        $schedule = Schedule::findOrFail($id);

        // Validate ข้อมูลที่รับมา
        $validatedData = $request->validate([
            'departureTime' => 'required',
            'daysOfWeek' => 'required|array',
            'dayTypeName' => 'required|string',
            'active' => 'required|boolean',
        ]);

        // อัปเดตข้อมูล schedule
        $schedule->update([
            'DepartureTime' => $validatedData['departureTime'],
            'Active' => $validatedData['active'],
        ]);

        // หา DayType ถ้าไม่มีให้สร้างใหม่
        $dayType = DayType::firstOrCreate(
            ['DayTypeName' => $validatedData['dayTypeName']],
            ['Active' => 1]
        );

        // ลบรายการ scheduleHasDayTypes ที่เชื่อมโยงกับ schedule นี้
        $schedule->scheduleHasDayTypes()->delete();

        // เพิ่มรายการ scheduleHasDayTypes ใหม่
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


    public function toggleActive(Request $request, $ScheduleID)
    {
        // หาตารางจาก ScheduleID
        $schedule = Schedule::findOrFail($ScheduleID); 
    
        // เปลี่ยนสถานะ Active ตามที่ส่งมาจาก request
        $schedule->Active = $request->input('Active') ? 1 : 0;
    
        // บันทึกการเปลี่ยนแปลง
        if ($schedule->save()) {
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update status']);
        }
    }
    
    
}