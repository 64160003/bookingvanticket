@extends('layouts/layout')
@section('title', 'หน้าหลัก')
@section('content')

<head>
    <style>
    /* กำหนดสีและฟอนต์ที่อ่านง่าย */
    body {
        font-family: 'Prompt', sans-serif;
        background-color: #f9f9f9;
        color: #333;
        line-height: 1.6;
    }

    .index-container {
        max-width: 700px;
        margin: 0 auto;
        padding: 30px;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h3 {
        color: #444;
        font-size: 1.6em;
        text-align: center;
        margin-bottom: 25px;
    }

    .departure-time {
        display: block;
        width: 90%;
        margin: 12px auto;
        padding: 12px;
        text-align: center;
        font-size: 1.2em;
        color: #ffffff;
        background-color: #4CAF50;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .departure-time:hover {
        background-color: #45a049;
    }

    .departure-time:active {
        background-color: #1c4965;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transform: translateY(3px);
    }

    .availability-status {
        font-size: 0.9em;
        color: #666;
    }

    .rounded-circle {
        border-radius: 50%;
    }

    .page-container {
        flex-direction: column;
        min-height: 83.5vh;
    }

    .page-content {
        flex: 1;
    }
    </style>
</head>

<h1 class="mt-4" style="text-align: center;">จองตั๋วคิวรถตู้ จันทบุรี-บางแสน</h1>
<br>
<div class="page-container d-flex">
    <div class="page-content">
        <div class="index-container">
            <div style="text-align: center;">
                <h3>
                    วัน{{ \Carbon\Carbon::now()->locale('th')->dayName }}
                    ที่ {{ \Carbon\Carbon::now()->locale('th')->translatedFormat('j F') }}
                    {{ \Carbon\Carbon::now()->addYears(543)->year }}
                </h3>
                <h5>กรุณาเลือกเวลาที่รถออก</h5>
                @foreach ($schedules as $schedule)
                <div class="departure-time" onclick="window.location.href='/booking/{{ $schedule->ScheduleID }}';">
                    {{ \Carbon\Carbon::parse($schedule->DepartureTime)->format('H:i') }}
                    <br>ว่าง
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Footer Section -->
    <div class="m-0 border mt-4">
        <footer class="bg-light text-center text-white">
            <div class="container p-4 pb-0">
                <section class="mb-4">
                    <span style="color:  rgba(0, 0, 0, 0.2);">ติดต่อเรา</span>
                    <a class="btn btn-primary m-1 rounded-circle" href="#!" role="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-telephone-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                        </svg>
                    </a>
                    <a class="btn btn-success m-1 rounded-circle" href="#!" role="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-line" viewBox="0 0 16 16">
                            <path
                                d="M8 0c4.411 0 8 2.912 8 6.492 0 1.433-.555 2.723-1.715 3.994-1.678 1.932-5.431 4.285-6.285 4.645-.83.35-.734-.197-.696-.413l.003-.018.114-.685c.027-.204.055-.521-.026-.723-.09-.223-.444-.339-.704-.395C2.846 12.39 0 9.701 0 6.492 0 2.912 3.59 0 8 0M5.022 7.686H3.497V4.918a.156.156 0 0 0-.155-.156H2.78a.156.156 0 0 0-.156.156v3.486c0 .041.017.08.044.107v.001l.002.002.002.002a.15.15 0 0 0 .108.043h2.242c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157m.791-2.924a.156.156 0 0 0-.156.156v3.486c0 .086.07.155.156.155h.562c.086 0 .155-.07.155-.155V4.918a.156.156 0 0 0-.155-.156zm3.863 0a.156.156 0 0 0-.156.156v2.07L7.923 4.832l-.013-.015v-.001l-.01-.01-.003-.003-.011-.009h-.001L7.88 4.79l-.003-.002-.005-.003-.008-.005h-.002l-.003-.002-.01-.004-.004-.002-.01-.003h-.002l-.003-.001-.009-.002h-.006l-.003-.001h-.004l-.002-.001h-.574a.156.156 0 0 0-.156.155v3.486c0 .086.07.155.156.155h.56c.087 0 .157-.07.157-.155v-2.07l1.6 2.16a.2.2 0 0 0 .039.038l.001.001.01.006.004.002.008.004.007.003.005.002.01.003h.003a.2.2 0 0 0 .04.006h.56c.087 0 .157-.07.157-.155V4.918a.156.156 0 0 0-.156-.156zm3.815.717v-.56a.156.156 0 0 0-.155-.157h-2.242a.16.16 0 0 0-.108.044h-.001l-.001.002-.002.003a.16.16 0 0 0-.044.107v3.486c0 .041.017.08.044.107l.002.003.002.002a.16.16 0 0 0 .108.043h2.242c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157H11.81v-.589h1.525c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157H11.81v-.589h1.525c.086 0 .155-.07.155-.156Z" />
                        </svg>
                    </a>
                    <a class="btn btn-danger m-1 rounded-circle" href="#!" role="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                        </svg>
                    </a>
                    <!-- <a class="btn btn-primary btn-floating m-1" style="background-color: #ac2bac;" href="#!" role="button">
                    <i class="fab fa-instagram"></i>
                </a>
                <a class="btn btn-primary btn-floating m-1" style="background-color: #0082ca;" href="#!" role="button">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a class="btn btn-primary btn-floating m-1" style="background-color: #333333;" href="#!" role="button">
                    <i class="fab fa-github"></i>
                </a> -->
                </section>
            </div>
            <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
                © 2024 Copyright:
                <a class="text-white" href="https://www.informatics.buu.ac.th/2020//">Faculty of Informatics, Burapha
                    University.</a>
            </div>
        </footer>
    </div>
</div>
@endsection