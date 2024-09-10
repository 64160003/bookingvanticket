<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleHasRoute extends Model
{
    use HasFactory;
    protected $table = 'schedule_has_route';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ScheduleID', 'RouteUpID', 'RouteDownID'];
    public $timestamps = true;

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'ScheduleID');
    }

    public function routeUp()
    {
        return $this->belongsTo(RouteUp::class, 'RouteUpID');
    }

    public function routeDown()
    {
        return $this->belongsTo(RouteDown::class, 'RouteDownID');
    }

}
