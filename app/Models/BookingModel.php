<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    use HasFactory;
    protected $table = 'Booking';
    protected $fillable = [
        'BookingID',
        'Seat',
        'BookingDate',
        'TravelDate',
        'Name',
        'Phone',
        'System',
    ];
}
