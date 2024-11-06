@extends('layouts/layout')

@section('title', 'Customer Information')

@section('content')
<div class="customer-container">
    <h2 align="center">ข้อมูลการจอง</h2>
    <div class="receipt">
        <p><strong>วันที่:</strong> {{ \Carbon\Carbon::now()->locale('th')->translatedFormat('j F') }}
            {{ \Carbon\Carbon::now()->addYears(543)->year }}
        </p>
        @if($schedule)
        <p><strong>เวลาที่รถออก:</strong> {{ date('H:i', strtotime($schedule->DepartureTime)) }} น.</p>
        @else
        <p><strong>Departure Time:</strong> Not available</p>
        @endif
        <p><strong>จุดขึ้น: </strong> {{ $origin->Origin }}</p>
        <p><strong>จุดหมาย: </strong> {{ $destination->Destination }}</p>
        <p><strong>จำนวนที่นั่ง:</strong> {{ $seats }}</p>
        <p><strong>ยอดรวม:</strong> {{ number_format($totalPrice, 2) }} บาท</p>
    </div>

    <h2 align="center">ข้อมูลผู้โดยสาร</h2>
    <p style="color:red; font-size:big; text-align: center;">กรุณากรอกข้อมูลให้ครบถ้วน</p>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('booking.summary') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="customer_name">ชื่อ <span style="color:red; font-size:big;">*</span></label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>

        <div class="form-group">
            <label for="phone">หมายเลขโทรศัพท์<span style="color:red; font-size:big;">*</span></label>
            <input type="tel" id="phone" name="phone" required>
        </div>

        <div class="form-group">
            <label for="payment_method">วิธีการชำระเงิน<span style="color:red; font-size:big;">*</span></label>
            <select id="payment_method" name="payment_method" required>
                <option value="">กรุณาเลือกวิธีการชำระเงิน</option>
                <option value="cash">เงินสด</option>
                <option value="online_qr">QR Code พร้อมเพย์</option>
            </select>
        </div>

        <input type="hidden" name="origin_id" value="{{ $origin->RouteID }}">
        <input type="hidden" name="destination_id" value="{{ $destination->idRouteDown }}">
        <input type="hidden" name="seats" value="{{ $seats }}">
        <input type="hidden" name="schedule_id" value="{{ $scheduleId }}">
        <input type="hidden" name="total_price" value="{{ $totalPrice }}">

        <div class="buttons-container">
            <button type="button" onclick="window.history.back();" class="btn back-btn">กลับ</button>
            <button type="submit" class="btn confirm-btn">ยืนยัน</button>
        </div>
    </form>
</div>

<style>
.customer-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}

.receipt {
    background-color: #f9f9f9;
    padding: 10px;
    border: 1px solid #ddd;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
}

.buttons-container {
    display: flex;
    justify-content: space-between;
}

.btn {
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
}

.back-btn {
    background-color: #d3d3d3;
    color: #000;
}

.confirm-btn {
    background-color: #4CAF50;
    color: #fff;
    border: none;
}

.alert {
    padding: 10px;
    background-color: #f44336;
    color: white;
}
</style>
@endsection