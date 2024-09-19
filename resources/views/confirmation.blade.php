@extends('layout')

@section('title', 'Booking Confirmation')

@section('content')
<div class="confirmation-container">
    <h2>Booking Confirmation</h2>
    <p>Thank you for your booking!</p>
    <p><strong>Booking ID:</strong> {{ $booking->BookingID }}</p>
    <p><strong>Name:</strong> {{ $booking->Name }}</p>
    <p><strong>Phone:</strong> {{ $booking->Phone }}</p>
    <p><strong>Seats:</strong> {{ $booking->Seat }}</p>
    <p><strong>System:</strong> {{ $booking->System }}</p>
    <!-- Add more booking details as needed -->
    <a href="{{ route('home') }}" class="btn">Return to Home</a>
</div>
@endsection