@extends('layouts/layout')
@section('title', 'หน้าหลัก')
@section('content')

    <head>
        <style>
            .index-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                font-family: Arial, sans-serif;
            }

            h3 {
                color: #333;
                text-align: center;
                margin-bottom: 20px;
            }

            .departure-time {
                display: block;
                width: 100%;
                margin: 10px auto;
                padding: 10px;
                text-align: center;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .departure-time:hover {
                background-color: #45a049;
            }

            .departure-time:active {
                background-color: #3e8e41;
                box-shadow: 0 5px #666;
                transform: translateY(4px);
            }
        </style>
    </head>
    <div class="index-container">
        <div style="text-align: center;">
            <h3>{{ $currentDate }}</h3>
            <h3>กรุณาเลือกเวลาที่รถออก</h3>
            @if ($schedules->isEmpty())
                <p>ไม่มีตารางเวลาสำหรับวันนี้</p>
            @else
                @foreach ($schedules as $schedule)
                    <div class="departure-time" onclick="window.location.href='/booking/{{ $schedule->ScheduleID }}';">
                        {{ $schedule->formattedDepartureTime }}
                        <br>ว่าง
                    </div>
                @endforeach
            @endif
        </div>
    </div>

@endsection
