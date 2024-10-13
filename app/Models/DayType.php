<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayType extends Model
{
    use HasFactory;

    protected $table = 'day_type';
    protected $primaryKey = 'DayTypeID';
    public $incrementing = true;
    protected $keyType = 'int'; // Change this to 'int' if your database uses INT, or keep as 'bigint' if it uses BIGINT

    public $timestamps = true;

    protected $fillable = [
        'DayTypeName',
        'Active',
    ];

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_has_day_type', 'DayTypeID', 'ScheduleID');
    }
}