<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookingModel extends Model
{
    use HasFactory;

    protected $table = 'booking';
    protected $primaryKey = 'BookingID';
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'Seat',
        'Name',
        'Phone',
        'System',
    ];

    // Add these new relationship methods

    public function freshTimestamp()
    {
        return Carbon::now('Asia/Bangkok');
    }

    public function origin()
    {
        return $this->belongsTo(RouteUp::class, 'RouteUpID', 'RouteID');
    }

    public function destination()
    {
        return $this->belongsTo(RouteDown::class, 'RouteDownID', 'idRouteDown');
    }

    public function payment()
    {
        return $this->hasOne(PaymentModel::class, 'BookingID', 'BookingID');
    }


    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'ScheduleID', 'ScheduleID');
    }

    public function scheduleHasDayType()
    {
        return $this->belongsTo(ScheduleHasDayType::class, 'ScheduleID', 'id'); // ปรับให้ตรงกับความสัมพันธ์ในฐานข้อมูล
    }
}