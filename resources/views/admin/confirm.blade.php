@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">
                        Payment Management
                    </h3>
                </div>
                <div class="card-body">
                    <div class="status-buttons">
                        <a href="{{ route('admin.confirmation', ['status' => 0]) }}" class="status-btn status-btn-waiting {{ $status == 0 ? 'active' : '' }}">
                            Waiting for Confirmation
                        </a>
                        <a href="{{ route('admin.confirmation', ['status' => 1]) }}" class="status-btn status-btn-confirmed {{ $status == 1 ? 'active' : '' }}">
                            Confirmed
                        </a>
                        <a href="{{ route('admin.confirmation', ['status' => 2]) }}" class="status-btn status-btn-notapproved {{ $status == 2 ? 'active' : '' }}">
                            Not approved
                        </a>
                    </div>

                    <div class="row">
                        @forelse($payments as $payment)
                            <div class="col-md-6 col-lg-4 mb-4">
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