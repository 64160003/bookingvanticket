<?php
namespace App\Http\Controllers;

use App\Models\Schedule; // Ensure this is Schedule, not ScheduleModel
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        // Fetch all schedules from the database
        $schedules = Schedule::all(); // Use the correct model class

        // Format DepartureTime using Carbon
        foreach ($schedules as $schedule) {
            $schedule->formattedDepartureTime = Carbon::parse($schedule->DepartureTime)->format('H:i');
        }
        
        // Pass the schedules data to the view
        $currentDate = Carbon::now()->format('d F Y'); // Get current date in desired format
        return view('welcome', compact('schedules', 'currentDate'));
    }    

}
