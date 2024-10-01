@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">
                        {{ $status == 0 ? 'Waiting for Confirmation' : 'Confirmed Payments' }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-4 border">
                        <a href="{{ route('admin.confirmation', ['status' => 0]) }}" class="btn btn-primary {{ $status == 0 ? 'active' : '' }} mr-2">
                            Waiting for Confirmation
                        </a>
                        <a href="{{ route('admin.confirmation', ['status' => 1]) }}" class="btn btn-success {{ $status == 1 ? 'active' : '' }}">
                            Confirmed
                        </a>
                    </div>

                    <div class="row justify-content-center">
                        @forelse($payments as $payment)
                            <div class="col-md-4 mb-4">
                                <a href="{{ route('admin.payment.detail', ['paymentId' => $payment->PaymentID]) }}" class="text-decoration-none">
                                    <div class="card payment-card">
                                        <div class="card-body">
                                            <h5 class="card-title">Payment ID: {{ $payment->PaymentID }}</h5>
                                            <p class="card-text">Booking ID: {{ $payment->BookingID }}</p>
                                            <p class="card-text">Amount: {{ $payment->Amount }}</p>
                                            <p class="card-text text-muted">Click to view more details</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning" role="alert">
                                    No payments found.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.payment-card {
    transition: all 0.3s ease;
}
.payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.card-body {
    background-color: aliceblue;
}
</style>
@endsection