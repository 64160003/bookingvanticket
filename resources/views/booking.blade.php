@extends('layout')
@section('title', 'Booking')
@section('content')

<head>
    <style>
        .booking-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        h3 {
            color: #333;
            text-align: center;
        }
        .section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #ffeaea;
        }
        .btn.selected {
            background-color: #457fa0;
            box-shadow: 0 0 5px #040404;
        }
        .custom-input {
            margin-top: 10px;
        }
        .custom-input input {
            padding: 5px;
            margin-right: 5px;
        }
        #price-display {
            font-weight: bold;
        }
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>

<div class="booking-container">
    <h3>{{ date('d F Y') }}</h3>

    @if(isset($departureTime))
        <p>Departure Time: {{ \Carbon\Carbon::parse($departureTime)->format('H:i') }}</p>
    @endif

    @if(isset($origins))
        <div id="origin-section" class="section">
            <h4>Select Origin</h4>
            @foreach($origins as $origin)
                <button class="btn origin-btn" data-origin-id="{{ $origin->RouteID }}">{{ $origin->Origin }}</button>
            @endforeach
        </div>

        <div id="destination-section" class="section" style="display:none;">
            <h4>Select Destination</h4>
            <div id="destination-buttons"></div>
        </div>
    @else
        <p>No origins available.</p>
    @endif

    <div id="seat-selection" class="section" style="display:none;">
        <h4>Select Number of Seats</h4>
        <button class="btn seat-btn" data-seats="1">1 Seat</button>
        <button class="btn seat-btn" data-seats="2">2 Seats</button>
        <button class="btn seat-btn" data-seats="3">3 Seats</button>
        <div class="custom-input">
            <label for="custom-seats">Custom quantity:</label>
            <input type="number" id="custom-seats" min="1" max="10">
            <button class="btn" id="custom-seats-btn">Select</button>
        </div>
    </div>

    <div id="price-display" class="section" style="display:none;">
        <h4>Price Information</h4>
        <p>Price per seat: <span id="price-per-seat"></span></p>
        <h3>Total price: <span id="total-price"></span></h3>
    </div>

    @if(isset($scheduleId))
        <p>Schedule ID: {{ $scheduleId }}</p>
    @endif

    <div class="navigation-buttons">
        <button id="back-btn" class="btn">Back</button>
        <button id="next-btn" class="btn">Next</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const originButtons = document.querySelectorAll('.origin-btn');
    const destinationSection = document.getElementById('destination-section');
    const destinationButtonsContainer = document.getElementById('destination-buttons');
    const seatSelection = document.getElementById('seat-selection');
    const priceDisplay = document.getElementById('price-display');
    const pricePerSeatSpan = document.getElementById('price-per-seat');
    const totalPriceSpan = document.getElementById('total-price');
    let selectedDestinationId = null;
    let pricePerSeat = 0;

    function clearSelection(buttons) {
        buttons.forEach(btn => btn.classList.remove('selected'));
    }

    originButtons.forEach(button => {
        button.addEventListener('click', function() {
            clearSelection(originButtons);
            this.classList.add('selected');
            const originId = this.getAttribute('data-origin-id');
            fetch(`/fetch-destinations/${originId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(destinations => {
                    destinationButtonsContainer.innerHTML = '';
                    if (destinations.length === 0) {
                        destinationButtonsContainer.innerHTML = '<p>No destinations available for this origin.</p>';
                    } else {
                        destinations.forEach(destination => {
                            const btn = document.createElement('button');
                            btn.className = 'btn destination-btn';
                            btn.textContent = destination.Destination;
                            btn.dataset.destinationId = destination.idRouteDown;
                            btn.dataset.price = destination.Price;
                            btn.addEventListener('click', function() {
                                clearSelection(document.querySelectorAll('.destination-btn'));
                                this.classList.add('selected');
                                selectedDestinationId = this.dataset.destinationId;
                                pricePerSeat = parseFloat(this.dataset.price);
                                pricePerSeatSpan.textContent = `$${pricePerSeat.toFixed(2)}`;
                                seatSelection.style.display = 'block';
                                priceDisplay.style.display = 'block';
                                updateTotalPrice(1); // Default to 1 seat
                            });
                            destinationButtonsContainer.appendChild(btn);
                        });
                    }
                    destinationSection.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    destinationButtonsContainer.innerHTML = `<p>Error loading destinations: ${error.message}</p>`;
                    destinationSection.style.display = 'block';
                });
        });
    });

    // Seat selection handlers
    const seatButtons = document.querySelectorAll('.seat-btn');
    seatButtons.forEach(button => {
        button.addEventListener('click', function() {
            clearSelection(seatButtons);
            this.classList.add('selected');
            updateTotalPrice(parseInt(this.dataset.seats));
        });
    });

    const customSeatsBtn = document.getElementById('custom-seats-btn');
    customSeatsBtn.addEventListener('click', function() {
        clearSelection(seatButtons);
        const customSeats = document.getElementById('custom-seats');
        updateTotalPrice(parseInt(customSeats.value));
    });

    function updateTotalPrice(seats) {
        const totalPrice = pricePerSeat * seats;
        totalPriceSpan.textContent = `${totalPrice.toFixed(2)}฿`;
    }

    const backBtn = document.getElementById('back-btn');
    const nextBtn = document.getElementById('next-btn');

    backBtn.addEventListener('click', function() {
        window.location.href = "{{ route('home') }}";
    });

    nextBtn.addEventListener('click', function() {
    const selectedOrigin = document.querySelector('.origin-btn.selected');
    const selectedDestination = document.querySelector('.destination-btn.selected');
    const selectedSeats = document.querySelector('.seat-btn.selected');
    const customSeatsInput = document.getElementById('custom-seats');

    let seats = selectedSeats ? selectedSeats.getAttribute('data-seats') : customSeatsInput.value;

    if (!selectedOrigin || !selectedDestination || !seats) {
        alert('Please select origin, destination, and number of seats before proceeding.');
        return;
    }

    const originId = selectedOrigin.getAttribute('data-origin-id');
    const destinationId = selectedDestination.getAttribute('data-destination-id');
    // const scheduleId = "{{ $scheduleId }}";
    const scheduleId = "{{ $scheduleId ?? '' }}"; // Default to empty string if $scheduleId is not set

    window.location.href = `/customer?origin_id=${originId}&destination_id=${destinationId}&seats=${seats}&schedule_id=${scheduleId}`;
});
});
</script>

@endsection