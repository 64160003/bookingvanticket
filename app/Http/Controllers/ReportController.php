<?php

namespace App\Http\Controllers;

use App\Models\PaymentModel;
use App\Models\BookingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ฟังก์ชันเพื่อดึงข้อมูลยอดขายรวมตามช่วงวันที่
    public function totalSales(Request $request)
    {
        // แปลงวันที่ให้ครอบคลุมทั้งวัน
        $startDate = Carbon::parse($request->input('start_date'))
            ->startOfDay();

        $endDate = Carbon::parse($request->input('end_date'))
            ->endOfDay();
        // ยอดขายรวมทั้งหมด
        $totalSales = BookingModel::with(['payment', 'schedule'])
            ->whereHas('payment', function ($query) {
                $query->where('PaymentStatus', 1);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->sum(function ($booking) {
                return $booking->payment->Amount;
            });

        // ปรับ query สำหรับ online sales
        $onlineSalesByDepartureTime = BookingModel::with(['payment', 'schedule'])
            ->whereHas('payment', function ($query) {
                $query->where('PaymentStatus', 1);
            })
            ->where('System', 'online')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($booking) {
                return $booking->schedule->DepartureTime;
            })
            ->map(function ($group) {
                return $group->sum(function ($booking) {
                    return $booking->payment->Amount;
                });
            })
            ->toArray();

        // ทำแบบเดียวกันสำหรับ store sales
        $storeSalesByDepartureTime = BookingModel::with(['payment', 'schedule'])
            ->whereHas('payment', function ($query) {
                $query->where('PaymentStatus', 1);
            })
            ->where('System', 'store')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($booking) {
                return $booking->schedule->DepartureTime;
            })
            ->map(function ($group) {
                return $group->sum(function ($booking) {
                    return $booking->payment->Amount;
                });
            })
            ->toArray();
        // เรียงข้อมูลตามเวลา
        ksort($onlineSalesByDepartureTime);
        ksort($storeSalesByDepartureTime);

        // เรียกข้อมูล $monthlySales
        $monthlySales = $this->getMonthlySalesData();

        return view('admin.report', compact('totalSales', 'startDate', 'endDate', 'onlineSalesByDepartureTime', 'storeSalesByDepartureTime', 'monthlySales'));
    }

    // ฟังก์ชันเพื่อดึงข้อมูลยอดขายรายวันตามประเภท System (store, online)
    public function dailySales(Request $request)
    {
        $date = Carbon::parse($request->input('date'));

        $sales = BookingModel::with('payment')
            ->whereHas('payment', function ($query) {
                $query->where('PaymentStatus', 1);
            })
            ->whereDate('created_at', $date)
            ->get()
            ->groupBy('System')
            ->map(function ($row) {
                return $row->sum(function ($booking) {
                    return $booking->payment->Amount;
                });
            });

        $storeSales = $sales['store'] ?? 0;
        $onlineSales = $sales['online'] ?? 0;
        $totalSales = $storeSales + $onlineSales;


        $monthlySales = $this->getMonthlySalesData();

        return view('admin.report', compact('storeSales', 'onlineSales', 'totalSales', 'date', 'monthlySales'));
    }

    // ฟังก์ชันเพื่อดึงข้อมูล $monthlySales สำหรับเปรียบเทียบย้อนหลัง 12 เดือน
    private function getMonthlySalesData()
    {
        $currentDate = Carbon::now();
        $startDate = $currentDate->copy()->subYear();

        $monthlySales = BookingModel::with('payment')
            ->whereHas('payment', function ($query) {
                $query->where('PaymentStatus', 1);
            })
            ->whereBetween('created_at', [$startDate, $currentDate])
            ->get()
            ->groupBy(function ($booking) {
                return Carbon::parse($booking->created_at)->format('Y-m');
            })
            ->map(function ($row) {
                return [
                    'store' => $row->where('System', 'store')->sum('payment.Amount'),
                    'online' => $row->where('System', 'online')->sum('payment.Amount')
                ];
            });

        return $monthlySales;
    }
}