@extends('layouts.admin')
@section('title', 'Manage Routes')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="routeDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Route
                            </button>
                            <div class="dropdown-menu" aria-labelledby="routeDropdown">
                                <a class="dropdown-item" href="#" id="originBtn">Origin</a>
                                <a class="dropdown-item" href="#" id="destinationBtn">Destination</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="originSection" style="display: none;">
                            <h3>Origins</h3>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Origin</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="originTableBody">
                                    <!-- Origins will be populated here -->
                                </tbody>
                            </table>
                            <button class="btn btn-primary" id="addOriginBtn">Add Origin</button>
                        </div>
                        <div id="destinationSection" style="display: none;">
                            <h3>Destinations</h3>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Destination</th>
                                        <th>Price</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="destinationTableBody">
                                    <!-- Destinations will be populated here -->
                                </tbody>
                            </table>
                            <button class="btn btn-primary" id="addDestinationBtn">Add Destination</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for adding/editing origins and destinations -->
    <div class="modal fade" id="originModal" tabindex="-1" role="dialog" aria-labelledby="originModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="originModalLabel">Add/Edit Origin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="originForm">
                        <input type="hidden" id="originId">
                        <div class="form-group">
                            <label for="originName">Origin Name</label>
                            <input type="text" class="form-control" id="originName" required>
                        </div>
                        <div class="form-group">
                            <label for="originActive">Active</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="originActive" class="toggle-switch-checkbox">
                                <label class="toggle-switch-label" for="originActive">
                                    <span class="toggle-switch-inner"></span>
                                    <span class="toggle-switch-switch"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Available Destinations</label>
                            <div id="destinationCheckboxes">
                                <!-- Destination checkboxes will be populated here -->
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveOriginBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // CSRF Token Setup for AJAX Requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Event listeners for dropdown items
            $('#originBtn').click(function() {
                $('#originSection').show();
                $('#destinationSection').hide();
                loadOrigins();
            });

            $('#destinationBtn').click(function() {
                $('#destinationSection').show();
                $('#originSection').hide();
                loadDestinations();
            });

            // Load origins
            function loadOrigins() {
                $.get('/api/origins', function(data) {
                    let html = '';
                    data.forEach(function(origin) {
                        html += `
                    <tr>
                        <td>${origin.RouteID}</td>
                        <td>${origin.Origin}</td>
                        <td>
                            <div class="toggle-switch">
                                <input type="checkbox" id="originStatus${origin.RouteID}" 
                                       class="toggle-switch-checkbox origin-status-switch" 
                                       data-id="${origin.RouteID}" 
                                       ${origin.Active ? 'checked' : ''}>
                                <label class="toggle-switch-label" for="originStatus${origin.RouteID}">
                                    <span class="toggle-switch-inner"></span>
                                    <span class="toggle-switch-switch"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-origin" data-id="${origin.RouteID}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-origin" data-id="${origin.RouteID}">Delete</button>
                        </td>
                    </tr>
                `;
                    });
                    $('#originTableBody').html(html);
                });
            }

            function loadDestinations() {
                // AJAX call to fetch destinations
                $.get('/api/destinations', function(destinations) {
                    let html = '';
                    destinations.forEach(function(destination) {
                        html += `
            <div class="form-check">
                <input class="form-check-input destination-checkbox" type="checkbox" value="${destination.idRouteDown}" id="destination${destination.idRouteDown}">
                <label class="form-check-label" for="destination${destination.idRouteDown}">
                    ${destination.Destination}
                </label>
            </div>
        `;
                    });
                    $('#destinationCheckboxes').html(html);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error loading destinations:', textStatus, errorThrown);
                    $('#destinationCheckboxes').html('<p>Error loading destinations.</p>');
                });
            }

            // Function to load destinations for checkboxes
            // Function to load destinations for checkboxes
            function loadDestinationsForCheckboxes() {
                $.get('/api/destinations', function(destinations) {
                    let html = '';
                    destinations.forEach(function(destination) {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input destination-checkbox" type="checkbox" value="${destination.idRouteDown}" id="destination${destination.idRouteDown}">
                                <label class="form-check-label" for="destination${destination.idRouteDown}">
                                    ${destination.Destination}
                                </label>
                            </div>
                        `;
                    });
                    $('#destinationCheckboxes').html(html);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error loading destinations:', textStatus, errorThrown);
                    $('#destinationCheckboxes').html('<p>Error loading destinations.</p>');
                });
            }

            // Load destinations when adding a new origin
            $('#addOriginBtn').click(function() {
                $('#originId').val('');
                $('#originName').val('');
                $('#originActive').val('1');
                loadDestinationsForCheckboxes();
                $('#originModal').modal('show');
            });

            // Load destinations and set switch when editing an origin
            $(document).on('click', '.edit-origin', function() {
                let originId = $(this).data('id');
                $.get(`/api/origins/${originId}`, function(origin) {
                    $('#originId').val(origin.RouteID);
                    $('#originName').val(origin.Origin);
                    $('#originActive').prop('checked', origin.Active === 1);
                    loadDestinationsForCheckboxes();

                    // Set checkboxes for associated destinations
                    $.get(`/api/origins/${originId}/destinations`, function(destinations) {
                        destinations.forEach(function(destination) {
                            $(`#destination${destination.idRouteDown}`).prop(
                                'checked', true);
                        });
                    });

                    $('#originModal').modal('show');
                });
            });

            //delete
            $(document).on('click', '.delete-origin', function() {
                let originId = $(this).data('id');

                // Show confirmation popup with SweetAlert2
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
                            url: `/api/origins/${originId}`,
                            method: 'DELETE',
                            success: function(response) {
                                // Show success popup
                                Swal.fire(
                                    'Deleted!',
                                    'Origin has been deleted.',
                                    'success'
                                );
                                loadOrigins(); // Refresh the table after deletion
                            },
                            error: function(xhr, status, error) {
                                // Show error popup
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete origin. Please try again.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Save origin (create or update)
            $('#saveOriginBtn').click(function() {
                let originId = $('#originId').val();
                let originData = {
                    Origin: $('#originName').val(),
                    Active: $('#originActive').is(':checked') ? 1 : 0,
                    destinations: $('.destination-checkbox:checked').map(function() {
                        return this.value;
                    }).get()
                };

                let url = originId ? `/api/origins/${originId}` : '/api/origins';
                let method = originId ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: originData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Origin saved successfully.',
                        });
                        $('#originModal').modal('hide');
                        loadOrigins();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to save origin. Please try again.',
                        });
                    }
                });
            });

            // Handle status switch change
            $(document).on('change', '.origin-status-switch', function() {
                let originId = $(this).data('id');
                let newStatus = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: `/api/origins/${originId}`,
                    method: 'PUT',
                    data: {
                        Active: newStatus
                    },
                    success: function(response) {
                        console.log('Origin status updated successfully');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating origin status:', xhr.responseText);
                        alert('Error updating origin status. Please try again.');
                        // Revert the switch if the update failed
                        $(this).prop('checked', !newStatus);
                    }
                });
            });
            Swal.fire({
                title: 'Deleted!',
                text: 'Your file has been deleted.',
                icon: 'success',
                confirmButtonText: 'Cool',
                confirmButtonColor: '#34c38f', // Custom button color
                background: '#f5f5f5', // Custom background color
                iconColor: '#f46a6a', // Custom icon color
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
@endsection
