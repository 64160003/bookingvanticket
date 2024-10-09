<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RouteUp extends Model
{
    use HasFactory;

    protected $table = 'route_up';
    protected $primaryKey = 'RouteID';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['Origin', 'Active', 'Deleted'];
    public $timestamps = true;

    /**
     * Scope a query to only include non-deleted routes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('Deleted', false);
    }

    public function scheduleRoutes()
    {
        return $this->hasMany(ScheduleHasRoute::class, 'RouteUpID');
    }

    public function destinations()
    {
        return $this->belongsToMany(RouteDown::class, 'schedule_has_route', 'RouteUpID', 'RouteDownID')
                    ->distinct();
    }
}