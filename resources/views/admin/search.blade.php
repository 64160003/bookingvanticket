@extends('layouts/admin')
@section('title', 'ค้นหา')
@section('content')

<div class="container">
    <h2 align="center" class="my-4">ค้นหาการจอง</h2>
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <form action="{{ route('adminsearch') }}" method="GET" class="card card-sm">
                <div class="card-body row no-gutters align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-search h4 text-body"></i>
                    </div>
                    <div class="col">
                        <input class="form-control form-control-lg form-control-borderless" type="search" name="phone"
                            placeholder="ค้นหาด้วยชื่อและเบอร์โทร" required>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-lg btn-success" type="submit">ค้นหา</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (isset($bookings) && $bookings->isNotEmpty())
    <h2 class="my-4">ผลลัพธ์การค้นหา</h2>
    <div class="row">
        @foreach ($bookings as $booking)
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">หมายเลขการจอง: {{ $booking->BookingID }}</h5>
                    <p class="card-text"><strong>วันที่จอง:</strong>
                            {{ $booking->created_at->locale('th')->translatedFormat('j F') }}
                            {{ $booking->created_at->format('Y') + 543 }}
                    </p>
                    <p class="card-text"><strong>ชื่อผู้จอง:</strong> {{ $booking->Name }}</p>
                    <p class="card-text"><strong>เบอร์โทรศัพท์:</strong> {{ $booking->Phone }}</p>
                    <p class="card-text"><strong>จำนวนที่นั่ง:</strong> {{ $booking->Seat }}</p>
                    @if ($booking->origin)
                    <p class="card-text"><strong>จุดขึ้นรถ:</strong> {{ $booking->origin->Origin }}</p>
                    @endif
                    @if ($booking->destination)
                    <p class="card-text"><strong>จุดหมาย:</strong> {{ $booking->destination->Destination }}</p>
                    @endif
                    @if ($booking->schedule)
                    <p class="card-text"><strong>เวลารถออก:</strong> {{ \Carbon\Carbon::parse($booking->schedule->DepartureTime)->format('H:i') }} น.</p>
                    @endif
                    @php
                    $payment = $payments->where('BookingID', $booking->BookingID)->first();
                    @endphp
                    @if ($payment)
                    <p class="card-text"><strong>สถานะการจ่ายเงิน:</strong>
                        @if ($payment->PaymentStatus === 0)
                        <span class="text-warning">รอการยืนยัน</span>
                        @elseif($payment->PaymentStatus === 1)
                        <span class="text-success">ชำระแล้ว</span>
                        @elseif($payment->PaymentStatus === 2)
                        <span class="text-danger">การชำระเงินไม่ถูกต้อง</span>
                        @else
                        <span class="text-muted">Unknown</span>
                        @endif
                    </p>
                    <p class="card-text"><strong>ยอดรวม:</strong> ฿{{ number_format($payment->Amount, 2) }} บาท</p>
                    @else
                    <p class="card-text"><strong>สถานะการจ่ายเงิน:</strong> <span
                            class="text-danger">ไม่มีการชำระเงิน</span></p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p>{{ $error ?? 'ไม่พบการจองค่ะ.' }}</p>
    @endif
</div>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
    integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

<style>
body {
    background: #ddd;
}

.form-control-borderless {
    border: none;
}

.form-control-borderless:hover,
.form-control-borderless:active,
.form-control-borderless:focus {
    border: none;
    outline: none;
    box-shadow: none;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.form-inline .form-group {
    flex: 1;
}

.form-inline .form-control {
    width: 100%;
}
</style>

@endsection