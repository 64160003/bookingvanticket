@extends('layouts.admin')

@section('title', 'Manage Schedules')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3>Manage Schedules</h3>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#scheduleModal">
                            Add New Schedule
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Departure Time</th>
                                    <th>Days</th>
                                    <th>Day Type Name</th>
                                    <th>Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="scheduleTableBody">
                                @foreach ($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->id }}</td>
                                        <td>{{ $schedule->formattedDepartureTime }}</td>
                                        <td>{{ implode(', ', $schedule->scheduleHasDayTypes->pluck('Day')->toArray()) }}
                                        </td>
                                        <td>{{ $schedule->dayTypes->first()->DayTypeName ?? 'N/A' }}</td>
                                        <td>
                                            <div class="toggle-switch">
                                                <input type="checkbox" id="scheduleStatus{{ $schedule->ScheduleID }}"
                                                    class="toggle-switch-checkbox schedule-status-switch"
                                                    data-id="{{ $schedule->ScheduleID }}"
                                                    {{ $schedule->Active ? 'checked' : '' }}>
                                                <label class="toggle-switch-label"
                                                    for="scheduleStatus{{ $schedule->ScheduleID }}">
                                                    <span class="toggle-switch-inner"></span>
                                                    <span class="toggle-switch-switch"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary edit-schedule"
                                                data-id="{{ $schedule->ScheduleID }}">Edit</button>
                                            <button class="btn btn-danger delete-schedule"
                                                data-id="{{ $schedule->ScheduleID }}">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding/editing schedule -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Add/Edit Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="scheduleForm">
                        @csrf
                        <input type="hidden" id="scheduleId" name="scheduleId">

                        <div class="form-group">
                            <label for="departureTime">Departure Time</label>
                            <input type="time" class="form-control" id="departureTime" name="departureTime" required>
                        </div>

                        <div class="form-group">
                            <label for="daysOfWeek">Days of the Week</label>
                            <div id="daysOfWeek">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="daysOfWeek[]" id="monday"
                                        value="Monday">
                                    <label class="form-check-label" for="monday">Monday</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="daysOfWeek[]" id="tuesday"
                                        value="Tuesday">
                                    <label class="form-check-label" for="tuesday">Tuesday</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="daysOfWeek[]" id="wednesday"
                                        value="Wednesday">
                                    <label class="form-check-label" for="tuesday">Wednesday</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="daysOfWeek[]" id="thursday"
                                        value="Thursday">
                                    <label class="form-check-label" for="tuesday">Thursday</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="daysOfWeek[]" id="friday"
                                        value="Friday">
                                    <label class="form-check-label" for="tuesday">Friday</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="daysOfWeek[]" id="saturday"
                                        value="Saturday">
                                    <label class="form-check-label" for="tuesday">Saturday</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="daysOfWeek[]" id="sunday"
                                        value="Sunday">
                                    <label class="form-check-label" for="tuesday">Sunday</label>
                                </div>
                                <!-- Add checkboxes for other days -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dayTypeName">Day Type Name</label>
                            <input type="text" class="form-control" id="dayTypeName" name="dayTypeName" required>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="active" name="active">
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            console.log('Initial schedules data:', @json($schedules));
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ready(function() {
                // Add/Edit Schedule
                $('#scheduleForm').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    var scheduleId = $('#scheduleId').val();
                    var url = scheduleId ? '/admin/schedules/' + scheduleId : '/admin/schedules';
                    var method = scheduleId ? 'POST' : 'POST';

                    if (scheduleId) {
                        formData.append('_method', 'PUT');
                    }

                    formData.append('active', $('#active').is(':checked') ? 1 : 0);

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#scheduleModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Schedule has been saved successfully!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            alert('An error occurred: ' + (xhr.responseJSON ? xhr.responseJSON
                                .message : xhr.statusText));
                        }
                    });
                });

                // Edit Schedule
                $('.edit-schedule').on('click', function() {
                    var scheduleId = $(this).data('id');
                    console.log('Edit button clicked for schedule ID:', scheduleId);

                    $.ajax({
                        url: '/admin/schedules/' + scheduleId,
                        method: 'GET',
                        success: function(data) {
                            console.log('Received data:', data);
                            if (data.id === undefined || data.id === null) {
                                console.error('Received null or undefined ID from server');
                                alert('Error: Unable to edit schedule due to missing ID');
                                return;
                            }
                            $('#scheduleId').val(data.id);
                            $('#departureTime').val(data.DepartureTime);
                            $('#dayTypeName').val(data.dayTypeName);
                            $('#active').prop('checked', data.Active == 1);
                            $('#routeId').val(data.RouteID);
                            $('#startDateAt').val(data.StartDateAt);
                            $('#endDateAt').val(data.EndDateAt);

                            $('input[name="daysOfWeek[]"]').prop('checked', false);
                            data.days.forEach(function(day) {
                                $('input[name="daysOfWeek[]"][value="' + day + '"]').prop(
                                    'checked', true);
                            });

                            $('#scheduleModalLabel').text('Edit Schedule');
                            $('#scheduleModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to load schedule data:', error);
                            console.error('Status:', status);
                            console.error('Response:', xhr.responseText);
                            try {
                                var responseJson = JSON.parse(xhr.responseText);
                                console.error('Parsed error message:', responseJson.message);
                            } catch (e) {
                                console.error('Could not parse error response');
                            }
                            alert('Failed to load schedule data. Check console for details.');
                        }
                    });
                });

                // Clear form when adding new schedule
                $('[data-target="#scheduleModal"]').on('click', function() {
                    $('#scheduleForm')[0].reset();
                    $('#scheduleId').val('');
                    $('#scheduleModalLabel').text('Add New Schedule');
                });
                // Delete Schedule
                $('.delete-schedule').on('click', function() {
                    var scheduleId = $(this).data('id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/admin/schedules/' + scheduleId,
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'Your schedule has been deleted.',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                    alert('An error occurred: ' + (xhr.responseJSON ? xhr
                                        .responseJSON.message : xhr.statusText));
                                }
                            });
                        }
                    });
                });

                // Toggle Active Status with switch button
                document.querySelectorAll('.schedule-status-switch').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function(event) {
                        const scheduleId = event.target.getAttribute('data-id');
                        const isChecked = event.target.checked;

                        fetch(`/admin/schedules/${scheduleId}/toggle-active`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    Active: isChecked
                                })
                            })
                    });
                });


            });
        </script>
        <style>
            .toggle-switch {
                position: relative;
                width: 60px;
                user-select: none;
            }

            .toggle-switch-checkbox {
                display: none;
            }

            .toggle-switch-label {
                display: block;
                overflow: hidden;
                cursor: pointer;
                border: 2px solid #999999;
                border-radius: 20px;
            }

            .toggle-switch-inner {
                display: block;
                width: 200%;
                margin-left: -100%;
                transition: margin 0.3s ease-in 0s;
            }

            .toggle-switch-inner:before,
            .toggle-switch-inner:after {
                display: block;
                float: left;
                width: 50%;
                height: 30px;
                padding: 0;
                line-height: 30px;
                font-size: 14px;
                color: white;
                font-family: Trebuchet, Arial, sans-serif;
                font-weight: bold;
                box-sizing: border-box;
            }

            .toggle-switch-inner:before {
                content: "ON";
                padding-left: 10px;
                background-color: #34A7C1;
                color: #FFFFFF;
            }

            .toggle-switch-inner:after {
                content: "OFF";
                padding-right: 10px;
                background-color: #EEEEEE;
                color: #999999;
                text-align: right;
            }

            .toggle-switch-switch {
                display: block;
                width: 18px;
                margin: 6px;
                background: #FFFFFF;
                position: absolute;
                top: 0;
                bottom: 0;
                right: 26px;
                border: 2px solid #999999;
                border-radius: 20px;
                transition: all 0.3s ease-in 0s;
            }

            .toggle-switch-checkbox:checked+.toggle-switch-label .toggle-switch-inner {
                margin-left: 0;
            }

            .toggle-switch-checkbox:checked+.toggle-switch-label .toggle-switch-switch {
                right: 0px;
            }
        </style>
    @endpush
@endsection
