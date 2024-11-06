@extends('layouts/layout')

@section('title', 'Booking Summary')

@section('content')
<div class="summary-container">
    <h2 align="center">ข้อมูลการจอง</h2>
    <div class="receipt">
        <p><strong>วันที่:</strong> {{ \Carbon\Carbon::now()->locale('th')->translatedFormat('j F Y') }}</p>
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
    <p><strong>ระบบ:</strong> {{ ucfirst($system) }}</p>
    <p><strong>ชื่อ:</strong> {{ $validatedData['customer_name'] }}</p>
    <p><strong>หมายเลขโทรศัพท์:</strong> {{ $validatedData['phone'] }}</p>
    <!-- <p><strong>วิธีการชำระเงิน:</strong> {{ ucfirst($validatedData['payment_method']) }}</p> -->

    @if ($validatedData['payment_method'] === 'online_qr')
    <div class="qr-code-section">
        <div class="qr-code-container">
            <h3>สแกน QR Code เพื่อชำระเงิน:</h3>
            <img src="{{ asset('QR-Code/qr_code.jpg') }}" alt="QR Code" class="qr-code">
        </div>

        @if (!$isAdmin)
        <h3>อัปโหลดรูปภาพสลิปโอนเงิน</h3>
        <!-- Countdown Timer -->
        <div>
            <h6>กรุณาชำระเงินภายใน <span id="countdown" style="color: #d9534f">20</span> วินาที</h6>
        </div>

        <form id="upload-form" action="{{ route('booking.uploadSlip') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="payment_slip" id="payment_slip" accept="image/*" required>
            <input type="hidden" name="booking_id" value="{{ $booking->BookingID }}">
            <input type="hidden" name="total_price" value="{{ $totalPrice }}">
            <br><br>
            <div id="image-preview-container" style="display: none;">
                <img id="image-preview" src="" alt="Image Preview" style="max-width: 100%; height: auto;">
            </div>
            <div style="text-align: center;">
                <button type="submit" class="btn upload-btn">อัปโหลดรูปภาพสลิป</button>
            </div>
        </form>
        @else
        <div class="admin-payment-confirmation">
            <p>การชำระเงินถูกบันทึกเรียบร้อยแล้ว</p>
            <p><strong>ID การจองที่ :</strong> {{ $booking->BookingID }}</p>
        </div>
        @endif
    </div>
    @else
    <div class="cash-payment-section">
        <p>ชำระด้วยเงินสด</p>
        @if (!$isAdmin)
        <h4 style="color:red">กรุณาชำระเงินสดที่คิวรถก่อนถึงเวลารถออกอย่างน้อย 20 นาที</h4>
        @else
        <p>การชำระเงินถูกบันทึกเรียบร้อยแล้ว</p>
        @endif
        <p><strong>ID การจองที่ :</strong> {{ $booking->BookingID }}</p>
    </div>
    @endif

    <div class="buttons-container">
        <a href="{{ route('customer') }}?schedule_id={{ $schedule->ScheduleID }}&origin_id={{ $origin->RouteID }}&destination_id={{ $destination->idRouteDown }}&seats={{ $seats }}"
            class="btn back-btn">กลับ</a>
        <a href="{{ route('home') }}" class="btn home-btn">หน้าหลัก</a>
    </div>

    <!-- Modal for Cancellation -->
    <div id="cancel-modal"
        style="display: none; background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
        <div style="background-color: white; padding: 20px; border-radius: 10px; text-align: center;">
            <p>หมดเวลาชำระเงิน การจองถูกยกเลิก</p>
            <a href="{{ route('home') }}" class="btn">หน้าหลัก</a>
        </div>
    </div>
</div>

<!-- Only include countdown script for non-admin users -->
@if (!$isAdmin && $validatedData['payment_method'] === 'online_qr')
<script>
let countdownElement = document.getElementById('countdown');
let countdown = 20;
let timer = setInterval(function() {
    countdown--;
    countdownElement.textContent = countdown;
    if (countdown <= 0) {
        clearInterval(timer);
        document.getElementById('cancel-modal').style.display = 'flex';
        document.getElementById('upload-form').style.display = 'none';
    }
}, 1000);

document.getElementById('payment_slip').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endif

<style>
/* Your styles */
.summary-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}

.receipt {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.qr-code-container {
    margin-top: 20px;
    text-align: center;
}

.qr-code {
    width: 200px;
    height: 200px;
}

.buttons-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    color: white;
}

.back-btn {
    background-color: #6c757d;
}

.home-btn {
    background-color: #4f8ec5;
}

.btn:hover {
    opacity: 0.9;
}

.qr-code-section {
    margin-top: 20px;
    padding: 10px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
    border-radius: 5px;
}

.upload-btn {
    background-color: #5cb85c;
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 5px;
}

#image-preview-container {
    margin-top: 15px;
}

#cancel-modal a {
    background-color: #d9534f;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
}
</style>
@endsection