@extends('layouts.master')
@section('subtitle', 'Databases')
@section('content_header_title', 'Settings')
@section('content_header_subtitle', 'All Databases')
@push('css')
    <style>
        tr.selectable:hover {
            cursor: pointer;
            background-color: #f1f1f1;
        }

        tr.selected {
            background-color: #007bff !important;
            color: white;
        }
    </style>
@endpush
@php
    use Carbon\Carbon;
@endphp

@section('content_body')

    <div class="content">

        <div class="container-fluid">
            <div class="col-md-12">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-body d-flex flex-column align-items-center position-relative">
                            <form id="shiftForm"
                                  action="{{ route('admin.shift.add') }}"
                                  class="d-flex flex-column align-items-center"
                                  method="POST"
                                  style="width: 100%;">
                                @csrf

                                <div class="d-flex justify-content-center flex-wrap mb-4 w-100 p-3 rounded"
                                     style="gap: 50px;">

                                    <!-- Start Time -->
                                    <div class="me-3">
                                        <label class="text-primary pt-2 mb-0 mr-2">
                                            <h5>Please Insert Your Shift Start Time</h5>
                                        </label>
                                    </div>
                                    <div class="custom-file"
                                         style="max-width: 300px;">
                                        <input type="time"
                                               id="start_time"
                                               style="width:170px !important;"
                                               class="form-control @error('start_time') is-invalid @enderror"
                                               value="{{ old('start_time', isset($shift->start_time) ? $shift->start_time : '') }}"
                                               name="start_time">
                                        @error('start_time')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <small id="start_time_error"
                                               class="text-danger"></small>
                                    </div>

                                    <!-- End Time -->
                                    <div class="me-3">
                                        <label class="text-primary pt-2 mb-0 mr-2">
                                            <h5>Please Insert Your Shift End Time</h5>
                                        </label>
                                    </div>
                                    <div class="custom-file"
                                         style="max-width: 300px;">
                                        <input type="time"
                                               id="end_time"
                                               style="width:170px !important;"
                                               class="form-control @error('end_time') is-invalid @enderror"
                                               value="{{ old('end_time', isset($shift->end_time) ? $shift->end_time : '') }}"
                                               name="end_time">
                                        @error('end_time')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <small id="end_time_error"
                                               class="text-danger"></small>
                                    </div>

                                </div>

                                <div class="d-flex justify-content-center w-100 mt-4">
                                    <button type="submit"
                                            id="save-btn"
                                            class="btn btn-sm btn-primary">Update</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-body">

                        @if (isset($shift))
                            <table class="table table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>Start Time </th>
                                        <th>End Time </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <td class="fw-semibold align-middle">
                                        {{ $shift->id }}

                                    </td>
                                    <td class="fw-semibold align-middle">
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}
                                    </td>
                                    <td class="fw-semibold align-middle">
                                        {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                    </td>

                                </tbody>
                            </table>
                        @else
                            <p class="text-center mb-0">No tables found.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

{{-- Push extra CSS --}}
@push('css')
    <style>
        #save-btn {
            opacity: 0.5;
            background-color: #007bff;
            /* Original Bootstrap primary */
            color: white;
            /* Original text color */
            transition: background-color 0.3s, color 0.3s, opacity 0.3s;
        }

        #save-btn:hover {
            background-color: #28a745;
            /* New background color on hover (green) */
            color: yellow;
            /* New text color on hover */
            opacity: 1;
            /* Fully visible on hover */
        }
    </style>
@endpush

{{-- Push extra scripts --}}
@push('js')
    <!-- JavaScript for frontend validation -->
    <script>
        document.getElementById('shiftForm').addEventListener('submit', function(e) {
            // Clear previous error messages
            document.getElementById('start_time_error').textContent = '';
            document.getElementById('end_time_error').textContent = '';

            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;
            let hasError = false;

            if (!start) {
                document.getElementById('start_time_error').textContent = 'Start time is required.';
                hasError = true;
            }

            if (!end) {
                document.getElementById('end_time_error').textContent = 'End time is required.';
                hasError = true;
            }

            if (start && end && start >= end) {
                document.getElementById('start_time_error').textContent = 'Start time must be before end time.';
                document.getElementById('end_time_error').textContent = 'End time must be after start time.';
                hasError = true;
            }

            if (hasError) {
                e.preventDefault(); // Stop form submission if errors
            }
        });
    </script>
@endpush
