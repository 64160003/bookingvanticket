<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedule';
    protected $primaryKey = 'ScheduleID';
    public $incrementing = true;

    protected $fillable = [
        'DepartureTime',
        'Active',
    ];

    public $timestamps = true;

    public function dayTypes()
    {
        return $this->belongsToMany(DayType::class, 'schedule_has_day_type', 'ScheduleID', 'DayTypeID');
    }

    public function scheduleHasDayTypes()
    {
        return $this->hasMany(ScheduleHasDayType::class, 'ScheduleID');
    }
}