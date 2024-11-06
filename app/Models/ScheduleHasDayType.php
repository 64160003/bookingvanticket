<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleHasDayType extends Model
{
    use HasFactory;

    protected $table = 'schedule_has_day_type';
    public $timestamps = true;

    protected $fillable = [
        'ScheduleID',
        'DayTypeID',
        'Day',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'ScheduleID');
    }

    public function dayType()
    {
        return $this->belongsTo(DayType::class, 'DayTypeID');
    }

    // กำหนดความสัมพันธ์กับ BookingModel
    public function bookings()
    {
        return $this->hasMany(BookingModel::class, 'ScheduleID', 'id');
    }
}