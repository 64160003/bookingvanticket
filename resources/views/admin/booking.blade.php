@extends('layouts.admin')
@section('title', 'Booking')
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
</div>
@endsection