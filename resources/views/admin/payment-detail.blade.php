@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container">
    <a href="{{ route('admin.confirmation', ['status' => $payment->PaymentStatus]) }}" class="back-btn">
        กลับ
    </a>

    @if (isset($message))
        <div class="alert alert-warning">{{ $message }}</div>
    @else
        <div class="card payment-card">
            <div class="card-header">
                <h2 style="color: black;">หมายเลขการชำระเงิน: {{ $payment->PaymentID }}</h2>
            </div>
            <div class="card-body">
                <!-- ข้อมูลการจองเพิ่มเติม -->
                <p class="card-text"><strong>ชื่อผู้จอง:</strong> {{ $booking->Name }}</p>
                <p class="card-text"><strong>หมายเลขโทรศัพท์:</strong> {{ $booking->Phone }}</p>
                <p class="card-text"><strong>จำนวนที่นั่ง:</strong> {{ $booking->Seat }}</p>
                
                <!-- แสดงจุดขึ้นรถและจุดหมายในบรรทัดเดียวกัน -->
                <p class="card-text">
                    @if ($booking->origin)
                        <strong>จุดขึ้นรถ:</strong> {{ $booking->origin->Origin }}
                    @endif
                    @if ($booking->destination)
                        <span class="ms-3"><strong>&nbsp;&nbsp;&nbsp;จุดหมาย:</strong> {{ $booking->destination->Destination }}</span>
                    @endif
                </p>

                <!-- ตรวจสอบวิธีชำระเงินและแสดงผลตามเงื่อนไข -->
                @if ($payment->PaymentMethod === 'Cash')
                    <p><strong>วิธีชำระเงิน:</strong> เงินสด</p>
                @elseif ($payment->PaymentMethod === 'Online QR Code')
                    <p><strong>วิธีชำระเงิน:</strong> QR Code พร้อมเพย์</p>
                    
                    <!-- แสดงใบเสร็จเฉพาะเมื่อมีการชำระด้วย QR Code พร้อมเพย์และมีไฟล์ใบเสร็จ -->
                    @if ($payment->payment_slip)
                        <p><strong>ใบเสร็จ:</strong></p>
                        <img src="{{ asset('uploads/' . $payment->payment_slip) }}" alt="Payment Slip" class="slip-image">
                    @else
                        <p><strong>ใบเสร็จ:</strong> ไม่มีใบเสร็จ</p>
                    @endif
                @endif

                <p><strong>จำนวนเงิน:</strong> {{ $payment->Amount }} บาท</p>

                <!-- เช็คสถานะการจ่ายเงิน -->
                <p><strong>สถานะการจ่ายเงิน:</strong>
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

                <p><strong>วันที่ชำระเงิน:</strong> {{ $formattedDate }} น.</p>

                <div class="mt-4">
                    <form action="{{ route('admin.updatePaymentStatus', $payment->PaymentID) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="1">
                        <button type="submit" class="btn btn-success" {{ $payment->PaymentStatus == 1 ? 'disabled' : '' }}>
                            การชำระเงินถูกต้อง
                        </button>
                    </form>

                    <form action="{{ route('admin.updatePaymentStatus', $payment->PaymentID) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="2">
                        <button type="submit" class="btn btn-danger" {{ $payment->PaymentStatus == 2 ? 'disabled' : '' }}>
                            การชำระเงินไม่ถูกต้อง
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
