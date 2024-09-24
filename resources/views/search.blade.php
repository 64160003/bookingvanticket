@extends('layouts/layout')
@section('title', 'ค้นหา')
@section('content')

<div class="container">
    <h2>Search Booking</h2>
    <form action="{{ route('search.booking') }}" method="GET">
        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    @if(isset($bookings) && $bookings->isNotEmpty())
        <h2>Booking Results</h2>
        @foreach($bookings as $booking)
            <div class="booking-result">
                <p><strong>Booking ID:</strong> {{ $booking->BookingID }}</p>
                <p><strong>Name:</strong> {{ $booking->Name }}</p>
                <p><strong>Phone:</strong> {{ $booking->Phone }}</p>
                <p><strong>Seats:</strong> {{ $booking->Seat }}</p>
                
                @if($booking->origin)
                    <p><strong>Origin:</strong> {{ $booking->origin->Origin }}</p>
                @endif
                
                @if($booking->destination)
                    <p><strong>Destination:</strong> {{ $booking->destination->Destination }}</p>
                @endif
                
                @if($booking->schedule)
                    <p><strong>Departure Time:</strong> {{ $booking->schedule->DepartureTime }}</p>
                @endif

                @php
                    $payment = $payments->where('BookingID', $booking->BookingID)->first();
                @endphp

                @if($payment)
                    <p><strong>Payment Status:</strong> 
                        @if($payment->PaymentStatus === 0)
                            <span style="color: orange;">Waiting for confirmation</span>
                        @elseif($payment->PaymentStatus === 1)
                            <span style="color: green;">Confirmed</span>
                        @else
                            <span style="color: gray;">Unknown</span>
                        @endif
                    </p>
                    <p><strong>Amount:</strong> ฿{{ number_format($payment->Amount, 2) }}</p>
                @else
                    <p><strong>Payment Status:</strong> <span style="color: red;">Not available</span></p>
                @endif
            </div>
            <hr>
        @endforeach
    @else
        <p>{{ $error ?? 'No bookings found.' }}</p>
    @endif
</div>
@endsection
