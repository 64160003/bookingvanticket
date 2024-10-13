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
                                                <input type="checkbox" id="scheduleStatus{{ $schedule->id }}"
                                                    class="toggle-switch-checkbox schedule-status-switch"
                                                    data-id="{{ $schedule->id }}" {{ $schedule->Active ? 'checked' : '' }}>
                                                <label class="toggle-switch-label" for="scheduleStatus{{ $schedule->id }}">
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
                    var method = scheduleId ? 'PUT' : 'POST';

                    formData.append('active', $('#active').is(':checked') ? 1 : 0);

                    $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#scheduleModal').modal('hide');
                            location.reload();
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
                    $.get('/admin/schedules/' + scheduleId, function(data) {
                        $('#scheduleId').val(data.id);
                        $('#departureTime').val(data.DepartureTime);
                        $('#dayTypeName').val(data.dayTypeName);
                        $('#active').prop('checked', data.Active == 1);

                        $('input[name="daysOfWeek[]"]').prop('checked', false);
                        data.days.forEach(function(day) {
                            $('input[name="daysOfWeek[]"][value="' + day + '"]').prop('checked',
                                true);
                        });

                        $('#scheduleModalLabel').text('Edit Schedule');
                        $('#scheduleModal').modal('show');
                    }).fail(function(xhr) {
                        console.log(xhr.responseText);
                        alert('Failed to load schedule data');
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
                    if (confirm('Are you sure you want to delete this schedule?')) {
                        $.ajax({
                            url: '/admin/schedules/' + scheduleId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                alert(response.message);
                                location.reload();
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                alert('An error occurred: ' + (xhr.responseJSON ? xhr.responseJSON
                                    .message : xhr.statusText));
                            }
                        });
                    }
                });

                // Toggle Active Status
                $('.schedule-status-switch').on('change', function() {
                    var scheduleId = $(this).data('id');
                    $.ajax({
                        url: '/admin/schedules/' + scheduleId + '/toggle-active',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            alert('Status updated successfully');
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            alert('An error occurred: ' + (xhr.responseJSON ? xhr.responseJSON
                                .message : xhr.statusText));
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
