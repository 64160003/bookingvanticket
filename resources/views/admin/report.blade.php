@extends('layouts.admin')
@section('title', 'Report')
@section('content')
<div class="container">
    <h1>รายงานยอดขาย</h1>

    <!-- Form เลือกช่วงวันที่สำหรับยอดขายรวม -->
    <form action="{{ route('report.totalSales') }}" method="GET">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="start_date">วันเริ่มต้น:</label>
                    <input type="text" id="start_date" name="start_date" class="form-control" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="end_date">วันสุดท้าย:</label>
                    <input type="text" id="end_date" name="end_date" class="form-control" required>
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary mt-4">ดูยอดขายรวม</button>
            </div>
        </div>
    </form>

    @isset($totalSales)
    @if(isset($startDate) && isset($endDate))
    <h2>ยอดขายรวมตั้งแต่วันที่
        {{ \Carbon\Carbon::parse($startDate)->addYears(543)->locale('th')->translatedFormat('j F Y') }} ถึง
        {{ \Carbon\Carbon::parse($endDate)->addYears(543)->locale('th')->translatedFormat('j F Y') }}:
        {{ number_format($totalSales, 2) }} บาท
    </h2>

    <button id="showDetails" class="btn btn-primary mt-3 mb-3">ดูรายละเอียดเพิ่มเติม</button>

    <div id="details" style="display: none;" class="mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <h3>ยอดขายออนไลน์</h3>
                    <canvas id="onlineSalesChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <h3>ยอดขายหน้าร้าน</h3>
                    <canvas id="storeSalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endisset

    <div class="chart-container mt-4" style="position: relative; height: 400px;">
        <h3>รายงานยอดขาย (ย้อนหลัง 12 เดือน)</h3>
        <canvas id="monthlySalesChart"></canvas>
    </div>
</div>

<!-- JavaScript สำหรับสร้างกราฟ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script>
// ตั้งค่า locale ของ moment.js เป็นภาษาไทย
moment.locale('th');

// ฟังก์ชันสำหรับแปลงวันที่เป็นรูปแบบไทย
function formatThaiDate(date) {
    const momentDate = moment(date);
    const buddhistYear = momentDate.year() + 543;
    return momentDate.format(`D MMMM ${buddhistYear}`);
}

// ฟังก์ชันสำหรับแปลงเดือนปีเป็นรูปแบบไทย
function formatThaiMonthYear(yearMonth) {
    const [year, month] = yearMonth.split('-');
    const momentDate = moment(`${year}-${month}-01`);
    const buddhistYear = parseInt(year) + 543;
    return `${momentDate.format('MMMM')} ${buddhistYear}`;
}

// ฟังก์ชันสำหรับแปลงปี ค.ศ. เป็น พ.ศ.
function convertToBuddhistYear(date) {
    const yearOffset = 543;
    date.setYear(date.getFullYear() + yearOffset);
    return date;
}

// ตั้งค่า Flatpickr สำหรับ input วันที่
const flatpickrConfig = {
    locale: "th",
    dateFormat: "d-m-Y",
    enableTime: false,
    altInput: true,
    altFormat: "j F Y",
    wrapperId: 'custom-flatpickr',
    // เพิ่มการตั้งค่าสำหรับปฏิทินภาษาไทย
    onOpen: function(selectedDates, dateStr, instance) {
        // แปลงปีในปฏิทินเป็น พ.ศ.
        const yearElements = instance.calendarContainer.querySelectorAll('.numInput.cur-year');
        yearElements.forEach(element => {
            const yearCE = parseInt(element.value);
            element.value = yearCE + 543;
        });
    },
    onChange: function(selectedDates, dateStr, instance) {
        if (selectedDates[0]) {
            const thaiDate = formatThaiDate(selectedDates[0]);
            instance.altInput.value = thaiDate;
        }
    },
    onYearChange: function(selectedDates, dateStr, instance) {
        // แปลงปีในปฏิทินเป็น พ.ศ.
        const yearElements = instance.calendarContainer.querySelectorAll('.numInput.cur-year');
        yearElements.forEach(element => {
            const yearCE = parseInt(element.value);
            if (yearCE < 2400) { // ถ้าเป็นปี ค.ศ.
                element.value = yearCE + 543;
            }
        });
    }
};

// สร้าง Flatpickr instances
flatpickr("#start_date", flatpickrConfig);
flatpickr("#end_date", flatpickrConfig);

// แสดง/ซ่อนรายละเอียด
document.getElementById("showDetails")?.addEventListener("click", function() {
    const details = document.getElementById("details");
    if (details.style.display === "none") {
        details.style.display = "block";
        this.textContent = "ซ่อนรายละเอียด";
    } else {
        details.style.display = "none";
        this.textContent = "ดูรายละเอียดเพิ่มเติม";
    }
});

// ตั้งค่าสำหรับกราฟทั้งหมด
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `${context.dataset.label}: ${number_format(context.raw, 2)} บาท`;
                }
            }
        },
        legend: {
            position: 'top',
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value) {
                    return number_format(value, 0) + ' บาท';
                }
            }
        },
        x: {
            ticks: {
                maxRotation: 45,
                minRotation: 45
            }
        }
    }
};

// ฟังก์ชันจัดรูปแบบตัวเลข
function number_format(number, decimals = 0) {
    return new Intl.NumberFormat('th-TH', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

// สร้างกราฟ Online Sales
const onlineSalesData = @json($onlineSalesByDepartureTime);
const onlineSalesCtx = document.getElementById('onlineSalesChart')?.getContext('2d');
if (onlineSalesCtx) {
    new Chart(onlineSalesCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(onlineSalesData).map(time => moment(time, 'HH:mm:ss').format('HH:mm') + ' น.'),
            datasets: [{
                label: 'ยอดขายออนไลน์',
                data: Object.values(onlineSalesData),
                backgroundColor: 'rgba(75, 192, 192, 0.7)'
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                ...chartOptions.scales,
                x: {
                    ...chartOptions.scales.x,
                    title: {
                        display: true,
                        text: 'เวลาออกเดินทาง'
                    }
                }
            }
        }
    });
}

// สร้างกราฟ Store Sales
const storeSalesData = @json($storeSalesByDepartureTime);
const storeSalesCtx = document.getElementById('storeSalesChart')?.getContext('2d');
if (storeSalesCtx) {
    new Chart(storeSalesCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(storeSalesData).map(time => moment(time, 'HH:mm:ss').format('HH:mm') + ' น.'),
            datasets: [{
                label: 'ยอดขายหน้าร้าน',
                data: Object.values(storeSalesData),
                backgroundColor: 'rgba(255, 159, 64, 0.7)'
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                ...chartOptions.scales,
                x: {
                    ...chartOptions.scales.x,
                    title: {
                        display: true,
                        text: 'เวลาออกเดินทาง'
                    }
                }
            }
        }
    });
}

// สร้างกราฟ Monthly Sales
const monthlySalesData = @json($monthlySales);
const monthlyLabels = Object.keys(monthlySalesData).map(yearMonth => formatThaiMonthYear(yearMonth));
const storeSalesValues = Object.values(monthlySalesData).map(data => data.store || 0);
const onlineSalesValues = Object.values(monthlySalesData).map(data => data.online || 0);

const monthlySalesCtx = document.getElementById('monthlySalesChart')?.getContext('2d');
if (monthlySalesCtx) {
    new Chart(monthlySalesCtx, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'ยอดขายหน้าร้าน',
                data: storeSalesValues,
                backgroundColor: 'rgba(255, 159, 64, 0.7)'
            }, {
                label: 'ยอดขายออนไลน์',
                data: onlineSalesValues,
                backgroundColor: 'rgba(75, 192, 192, 0.7)'
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                legend: {
                    position: 'top',
                }
            }
        }
    });
}
</script>

<style>
.chart-container {
    margin-bottom: 20px;
}

.flatpickr-calendar {
    font-family: 'Sarabun', sans-serif;
}

.flatpickr-current-month .flatpickr-monthDropdown-months {
    font-family: 'Sarabun', sans-serif;
}
</style>
@endsection