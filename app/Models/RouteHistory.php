<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'OldPrice',
        'NewPrice',
        'OldDestination',
        'NewDestination',
        'ModifiedTime',
        'RouteId',
    ];

    // Add relationships, if any, here
}
