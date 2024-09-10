<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteUp extends Model
{
    use HasFactory;

    protected $table = 'route_up';
    protected $primaryKey = 'RouteID';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['Origin', 'Active'];
    public $timestamps = true;

    public function scheduleRoutes()
    {
        return $this->hasMany(ScheduleHasRoute::class, 'RouteUpID');
    }
}
