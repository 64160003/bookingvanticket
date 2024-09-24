@extends('layouts/layout')

@section('title', 'Customer Information')

@section('content')
<div class="customer-container">
    <h2>Booking Summary</h2>
    <div class="receipt">
        <p><strong>Origin:</strong> {{ $origin->Origin }}</p>
        <p><strong>Destination:</strong> {{ $destination->Destination }}</p>
        @if($schedule)
            <p><strong>Departure Time:</strong> {{ date('H:i', strtotime($schedule->DepartureTime)) }}</p>
        @else
            <p><strong>Departure Time:</strong> Not available</p>
        @endif
        <p><strong>Seat Quantity:</strong> {{ $seats }}</p>
        <p><strong>Total Price:</strong> ฿{{ number_format($totalPrice, 2) }}</p>
    </div>

    <h2>Customer Information</h2>
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
            <label for="customer_name">Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>

        <div class="form-group">
            <label for="phone">Telephone Number:</label>
            <input type="tel" id="phone" name="phone" required>
        </div>

        <div class="form-group">
            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="">Select a payment method</option>
                <option value="cash">Cash</option>
                <option value="online_qr">Online QR Code</option>
            </select>
        </div>

        <input type="hidden" name="origin_id" value="{{ $origin->RouteID }}">
        <input type="hidden" name="destination_id" value="{{ $destination->idRouteDown }}">
        <input type="hidden" name="seats" value="{{ $seats }}">
        <input type="hidden" name="schedule_id" value="{{ $scheduleId }}">
        <input type="hidden" name="total_price" value="{{ $totalPrice }}">

        <div class="buttons-container">
            <button type="button" onclick="window.history.back();" class="btn back-btn">Back</button>
            <button type="submit" class="btn confirm-btn">Confirm Booking</button>
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
    .form-group input, .form-group select {
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
