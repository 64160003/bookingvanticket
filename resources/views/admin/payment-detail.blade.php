@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container">
    @if (isset($message))
        <div class="alert alert-warning">{{ $message }}</div>
    @else
        <div class="card">
            <div class="card-header">
                <h2>Payment Details for ID: {{ $payment->PaymentID }}</h2>
            </div>
            <div class="card-body">
                <p><strong>Method:</strong> {{ $payment->PaymentMethod }}</p>
                <p><strong>Amount:</strong> {{ $payment->Amount }}</p>
                <p><strong>Booking ID:</strong> {{ $payment->BookingID }}</p>
                <p><strong>Status:</strong> {{ $payment->PaymentStatus }}</p>
                <p><strong>Created At:</strong> {{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    @endif
</div>
@endsection