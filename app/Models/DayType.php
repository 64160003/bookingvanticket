<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'DaytypeName',
    ];

    // Add relationships, if any, here
}
