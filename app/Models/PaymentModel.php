<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentModel extends Model
{
    use HasFactory;

    protected $table = 'payment'; // Use lowercase 'payment' for consistency with your SQL script
    protected $primaryKey = 'PaymentID'; // Specify the primary key
    public $incrementing = true; // It's auto-incrementing

    protected $fillable = [
        'PaymentID',
        'PaymentMethod',
        'Amount',
        'BookingID',
        'PaymentStatus',
    ];

    // Remove the keyType property since it's not necessary
}

