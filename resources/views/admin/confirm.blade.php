@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center" style="color: black;">การชำระเงิน</h3>
                </div>
                <div class="card-body">
                    <div class="status-buttons mb-4">
                        <a href="{{ route('admin.confirmation', ['status' => 0]) }}"
                            class="status-btn status-btn-waiting {{ $status == 0 ? 'active' : '' }}">
                            รอยืนยัน
                        </a>
                        <a href="{{ route('admin.confirmation', ['status' => 1]) }}"
                            class="status-btn status-btn-confirmed {{ $status == 1 ? 'active' : '' }}">
                            ยืนยันแล้ว
                        </a>
                        <a href="{{ route('admin.confirmation', ['status' => 2]) }}"
                            class="status-btn status-btn-notapproved {{ $status == 2 ? 'active' : '' }}">
                            ไม่ยืนยัน
                        </a>
                    </div>

                    <div class="sort-options mb-4">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="sortDropdown">
                                <i class="fas fa-sort"></i> เรียงจาก
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-sort="calendar"><i
                                            class="far fa-calendar-alt"></i> ตามวันที่</a></li>
                                <li><a class="dropdown-item" href="#" data-sort="newest"><i
                                            class="fas fa-sort-amount-down"></i> เรียงจากใหม่สุด</a></li>
                                <li><a class="dropdown-item" href="#" data-sort="oldest"><i
                                            class="fas fa-sort-amount-up"></i> เรียงจากเก่าสุด</a></li>
                                <li><a class="dropdown-item" href="#" data-sort="all"><i class="fas fa-list"></i>
                                        แสดงทั้งหมด</a></li>
                            </ul>
                        </div>
                        <input type="date" id="datePicker" class="form-control d-none" aria-label="Select Date">
                    </div>
                    <div class="row" id="paymentsContainer">
                        @forelse($payments as $payment)
                            <div class="col-md-6 col-lg-4 mb-4 payment-card"
                                data-date="{{ $payment->created_at->format('Y-m-d') }}">
                                <a href="{{ route('admin.payment.detail', ['paymentId' => $payment->PaymentID]) }}"
                                    class="text-decoration-none">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">หมายเลขการชำระเงิน: {{ $payment->PaymentID }}</h5>
                                            <p class="card-text">หมายเลขการจอง: {{ $payment->BookingID }}</p>
                                            <p class="card-text">จำนวน: {{ $payment->Amount }}</p>
                                            <p class="card-text">วันที่ชำระ: 
                                            {{ $payment->formatted_date }}</p>
                                            <p class="card-text text-muted">กดดูรายละเอียดเพิ่มเติม</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning" role="alert">
                                    ไม่มีการชำระเงิน
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sortDropdown = document.querySelector('.dropdown-menu');
            const datePicker = document.getElementById('datePicker');
            const paymentsContainer = document.getElementById('paymentsContainer');
            const paymentCards = document.querySelectorAll('.payment-card');
            const dropdownToggle = document.querySelector('.dropdown-toggle');

            sortDropdown.addEventListener('click', function (e) {
                e.preventDefault();
                if (e.target.classList.contains('dropdown-item')) {
                    const sortType = e.target.dataset.sort;
                    sortPayments(sortType);
                    updateDropdownText(e.target.textContent.trim());
                }
            });

            datePicker.addEventListener('change', function () {
                filterPaymentsByDate(this.value);
            });

            function sortPayments(sortType) {
                const cards = Array.from(paymentCards);

                switch (sortType) {
                    case 'calendar':
                        datePicker.classList.remove('d-none');
                        return;
                    case 'newest':
                        cards.sort((a, b) => new Date(b.dataset.date) - new Date(a.dataset.date));
                        break;
                    case 'oldest':
                        cards.sort((a, b) => new Date(a.dataset.date) - new Date(b.dataset.date));
                        break;
                    case 'all':
                        datePicker.classList.add('d-none');
                        break;
                }

                paymentsContainer.innerHTML = '';
                cards.forEach(card => paymentsContainer.appendChild(card));
            }

            function filterPaymentsByDate(date) {
                paymentCards.forEach(card => {
                    if (date === '' || card.dataset.date === date) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            function updateDropdownText(text) {
                dropdownToggle.innerHTML = `<i class="fas fa-sort"></i> ${text}`;
            }
        });
    </script>
@endpush