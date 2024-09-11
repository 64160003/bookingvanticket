<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    use HasFactory;

    protected $table = 'booking';

    protected $fillable = [
        'Seat',
        'Name',
        'Phone',
        'System',
    ];

    // Do not cast BookingID if it's an auto-incrementing field
}


