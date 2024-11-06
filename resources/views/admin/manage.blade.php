@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <!-- Route Management -->
                        <a href="{{ route('admin.manageRoute') }}" class="btn btn-primary mr-2">เส้นทาง</a>
                        
                        <!-- Schedule Management - Changed to 'admin.manageSchedule' -->
                        <a href="{{ route('admin.manageSchedule') }}" class="btn btn-primary">ตารางเวลา</a>
                    </div>
                    <div class="card-body">
                        <h3>การจัดการเส้นทาง และเวลา</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
