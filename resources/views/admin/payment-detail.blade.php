@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container">
    <a href="{{ route('admin.confirmation', ['status' => $payment->PaymentStatus]) }}" class="back-btn">
        🔙 Back
    </a>

    @if (isset($message))
        <div class="alert alert-warning">{{ $message }}</div>
    @else
        <div class="card payment-card">
            <div class="card-header">
                <h2>Payment Details for ID: {{ $payment->PaymentID }}</h2>
            </div>
            <div class="card-body">
                <p><strong>Method:</strong> {{ $payment->PaymentMethod }}</p>
                <p><strong>Amount:</strong> {{ $payment->Amount }}</p>
                <p><strong>Booking ID:</strong> {{ $payment->BookingID }}</p>
                <p><strong>Status:</strong> {{ $payment->PaymentStatus }}</p>
                <p><strong>Created At:</strong> {{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
                
                @if ($payment->payment_slip)
                    <p><strong>Payment Slip:</strong></p>
                    <img src="{{ asset('uploads/' . $payment->payment_slip) }}" alt="Payment Slip" class="slip-image">
                @else
                    <p><strong>Payment Slip:</strong> Not available</p>
                @endif

                <div class="mt-4">
                    <form action="{{ route('admin.updatePaymentStatus', $payment->PaymentID) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="1">
                        <button type="submit" class="btn btn-success" {{ $payment->PaymentStatus == 1 ? 'disabled' : '' }}>
                            Confirm Payment
                        </button>
                    </form>

                    <form action="{{ route('admin.updatePaymentStatus', $payment->PaymentID) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="2">
                        <button type="submit" class="btn btn-danger" {{ $payment->PaymentStatus == 2 ? 'disabled' : '' }}>
                            Cancel Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection