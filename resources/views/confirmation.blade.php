@extends('layouts/layout')

@section('title', 'Booking Confirmation')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0 text-muted">การจองสำเร็จ</h2>
                        @if ($booking->Payment->PaymentStatus === 0)
                        <span class="badge bg-warning font-size-14">รอตรวจสอบ</span>
                        @elseif($booking->Payment->PaymentStatus === 1)
                        <span class="badge bg-success font-size-14">ชำระแล้ว</span>
                        @elseif($booking->Payment->PaymentStatus === 2)
                        <span class="badge bg-danger font-size-14">การชำระไม่ถูกต้อง</span>
                        @else
                        <span class="text-muted">Unknown</span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <p class="mb-1"><strong>หมายเลขการจอง:</strong> {{ $booking->BookingID }}</p>
                        <p class="mb-1"><strong>ชื่อ:</strong> {{ $booking->Name }}</p>
                        <p class="mb-1"><strong>เบอร์โทรศัพท์:</strong> {{ $booking->Phone }}</p>
                        <p class="mb-1"><strong>จำนวนที่นั่ง:</strong> {{ $booking->Seat }}</p>
                        <p class="mb-1"><strong>เวลาออกเดินทาง:</strong>
                            {{ date('H:i', strtotime($booking->Schedule->DepartureTime)) }} น.
                        </p>
                        <p class="mb-1"><strong>วันที่จอง:</strong>
                            {{ $booking->created_at->locale('th')->translatedFormat('j F') }}
                            {{ $booking->created_at->format('Y') + 543 }}
                        </p>
                        <p class="mb-1"><strong>สถานะการจ่ายเงิน:</strong>
                            @if ($booking->Payment->PaymentStatus === 0)
                            รอตรวจสอบ
                            @elseif($booking->Payment->PaymentStatus === 1)
                            ชำระแล้ว
                            @elseif($booking->Payment->PaymentStatus === 2)
                            การชำระไม่ถูกต้อง
                            @else
                            <span class="text-muted">Unknown</span>
                            @endif
                        </p>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('home') }}" class="btn btn-primary">กลับหน้าหลัก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection