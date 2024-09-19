<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    use HasFactory;

    protected $table = 'booking';

    // If your primary key is named something other than 'id', specify it here
    protected $primaryKey = 'BookingID'; // Replace 'BookingID' with your actual primary key name

    // If your primary key is not an auto-incrementing integer, set this to false
    public $incrementing = true;

    // If your primary key is not an integer, specify its type
    protected $keyType = 'int';

    protected $fillable = [
        'Seat',
        'Name',
        'Phone',
        'System',
    ];

    // If you're not using created_at and updated_at columns
    public $timestamps = true;
}