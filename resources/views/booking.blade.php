@extends('layouts/layout')
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
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn {
        display: inline-block;
        margin: 5px;
        padding: 10px 15px;
        background-color: #45a049;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .green-btn:hover {
        background-color: #5C9DC0;
        /* สีเมื่อ hover เป็นสีน้ำเงินเข้มขึ้น */
    }

    .green-btn.selected {
        background-color: #5C9DC0;
        /* สีเมื่อเลือกเป็นสีน้ำเงินเข้ม */
    }

    #back-btn {
        background-color: #d3d3d3;
        color: black;
    }

    #back-btn:hover {
        background-color: white;
        /* สีเมื่อ hover เป็นสีน้ำเงินเข้มขึ้น */
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
    <h3>{{ \Carbon\Carbon::now()->locale('th')->translatedFormat('j F') }}
        {{ \Carbon\Carbon::now()->addYears(543)->year }}</h3>

    @if(isset($departureTime))
    <p>เวลาที่รถออก: {{ \Carbon\Carbon::parse($departureTime)->format('H:i') }}</p>
    @endif

    @if(isset($origins))
    <div id="origin-section" class="section">
        <h4>เลือกจุดขึ้นรถ</h4>
        @foreach($origins as $origin)
        <button class="btn origin-btn green-btn" data-origin-id="{{ $origin->RouteID }}">{{ $origin->Origin }}</button>
        @endforeach
    </div>

    <div id="destination-section" class="section" style="display:none;">
        <h4>เลือกจุดหมาย</h4>
        <div id="destination-buttons"></div>
    </div>
    @else
    <p>ไม่มีจุดขึ้นเปิดบริการ</p>
    @endif

    <div id="seat-selection" class="section" style="display:none;">
        <h4>เลือกจำนวนที่นั่ง</h4>
        <button class="btn seat-btn green-btn" data-seats="1">1 ที่นั่ง</button>
        <button class="btn seat-btn green-btn" data-seats="2">2 ที่นั่ง</button>
        <button class="btn seat-btn green-btn" data-seats="3">3 ที่นั่ง</button>
        <div class="custom-input">
            <label for="custom-seats">กำหนดจำนวนที่นั่งเอง:</label>
            <input type="number" id="custom-seats" min="1" max="13">
            <button class="btn green-btn" id="custom-seats-btn">เลือก</button>
        </div>
        <p style="color:red; font-size: small;">เลือกได้สูงสุด 13 ที่นั่ง</p>
    </div>

    <div id="price-display" class="section" style="display:none;">
        <p>ราคาต่อที่นั่ง: <span id="price-per-seat"></span> บาท</p>
        <h3>ยอดรวม: <span id="total-price"></span></h3>
    </div>

    <div class="navigation-buttons">
        <button id="back-btn" class="btn">กลับ</button>
        <button id="next-btn" class="btn green-btn">ถัดไป</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const originButtons = document.querySelectorAll('.btn.origin-btn');
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
                        destinationButtonsContainer.innerHTML =
                            '<p>ไม่มีจุดหมายสำหรับจุดขึ้นนี้</p>';
                    } else {
                        destinations.forEach(destination => {
                            const btn = document.createElement('button');
                            btn.className = 'btn destination-btn green-btn';
                            btn.textContent = destination.Destination;
                            btn.dataset.destinationId = destination.idRouteDown;
                            btn.dataset.price = destination.Price;
                            btn.addEventListener('click', function() {
                                clearSelection(document.querySelectorAll(
                                    '.destination-btn'));
                                this.classList.add('selected');
                                selectedDestinationId = this.dataset
                                    .destinationId;
                                pricePerSeat = parseFloat(this.dataset
                                    .price);
                                pricePerSeatSpan.textContent =
                                    `${pricePerSeat.toFixed(2)}`;
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
                    destinationButtonsContainer.innerHTML =
                        `<p>Error loading destinations: ${error.message}</p>`;
                    destinationSection.style.display = 'block';
                });
        });
    });

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
        const scheduleId =
            "{{ $scheduleId ?? '' }}"; // Default to empty string if $scheduleId is not set

        window.location.href =
            `/customer?origin_id=${originId}&destination_id=${destinationId}&seats=${seats}&schedule_id=${scheduleId}`;
    });
});
</script>

@endsection