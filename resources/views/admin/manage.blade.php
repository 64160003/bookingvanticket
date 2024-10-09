@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('admin.manageRoute') }}" class="btn btn-primary mr-2">Route</a>
                        <a href="{{ route('admin.manageSchedule') }}" class="btn btn-primary">Schedule</a>
                    </div>
                    <div class="card-body">
                        <h3>Route and Schedule Management</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection