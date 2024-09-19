@extends('layout')

@section('title', 'Booking Summary')

@section('content')
    <div class="summary-container">
        <h2>Booking Summary</h2>
        <div class="receipt">
            <p><strong>Origin:</strong> {{ $origin->Origin }}</p>
            <p><strong>Destination:</strong> {{ $destination->Destination }}</p>
            @if ($schedule)
                <p><strong>Departure Time:</strong> {{ date('H:i', strtotime($schedule->DepartureTime)) }}</p>
            @else
                <p><strong>Departure Time:</strong> Not available</p>
            @endif
            <p><strong>Seat Quantity:</strong> {{ $seats }}</p>
            <p><strong>Total Price:</strong> ฿{{ number_format($totalPrice, 2) }}</p>
        </div>

        <h2>Customer Information</h2>
        <p><strong>System:</strong> {{ ucfirst($system) }}</p>
        <p><strong>Name:</strong> {{ $validatedData['customer_name'] }}</p>
        <p><strong>Phone:</strong> {{ $validatedData['phone'] }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($validatedData['payment_method']) }}</p>

        @if ($validatedData['payment_method'] === 'online_qr')
            <div class="qr-code-section">
                <div class="qr-code-container">
                    <h3>Scan the QR Code to Pay:</h3>
                    <img src="{{ asset('QR-Code/qr_code.jpg') }}" alt="QR Code" class="qr-code">
                </div>

                <h3>Upload Payment Slip (Online Payment)</h3>
                <p>Please pay before time runs out. Time left: <span id="countdown">20</span> seconds</p>
                <form action="{{ route('booking.uploadSlip') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="payment_slip" id="payment_slip" accept="image/*" required>
                    <input type="hidden" name="seats" value="{{ $seats }}">
                    <input type="hidden" name="customer_name" value="{{ $validatedData['customer_name'] }}">
                    <input type="hidden" name="phone" value="{{ $validatedData['phone'] }}">
                    <br><br>
                    <!-- Image preview section -->
                    <div id="image-preview-container" style="display: none;">
                        <img id="image-preview" src="" alt="Image Preview" style="max-width: 100%; height: auto;">
                    </div>
                    <br>
                    <button type="submit" class="btn upload-btn">Upload Slip</button>
                </form>
            </div>
        @else
            <div class="cash-payment-section">
                <h3>Cash Payment</h3>
                <p>Your booking has been confirmed. Please pay at the counter before departure.</p>
                @if (isset($booking))
                    <p><strong>Booking ID:</strong> {{ $booking->BookingID }}</p>
                @endif
            </div>
        @endif
        <div class="buttons-container">
            <a href="{{ route('customer') }}?schedule_id={{ $schedule->ScheduleID }}&origin_id={{ $origin->RouteID }}&destination_id={{ $destination->idRouteDown }}&seats={{ $seats }}"
                class="btn back-btn">Back to Customer Info</a>
            <a href="{{ route('home') }}" class="btn home-btn">Return to Home</a>
            {{-- <a href="#" class="btn confirm-btn">Confirm Purchase</a> --}}
        </div>

        <!-- Modal for cancellation -->
        <div id="cancel-modal"
            style="display: none; background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
            <div style="background-color: white; padding: 20px; border-radius: 10px; text-align: center;">
                <p>This reservation has been cancelled.</p>
                <a href="{{ route('home') }}" class="btn">Go to Home</a>
            </div>
        </div>

    </div>
    <style>
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

        .payment-instruction {
            margin-top: 20px;
            font-size: 18px;
            color: #d9534f;
            text-align: center;
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

        .confirm-btn {
            background-color: #5cb85c;
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

    <script>
        // Countdown timer
        let countdownElement = document.getElementById('countdown');
        let countdown = 20;
        let timer = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(timer);
                // Display the cancellation modal
                document.getElementById('cancel-modal').style.display = 'flex';
                // Disable the upload form
                document.getElementById('upload-form').style.display = 'none';
            }
        }, 1000);

        // JavaScript to preview the selected image
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

@endsection
