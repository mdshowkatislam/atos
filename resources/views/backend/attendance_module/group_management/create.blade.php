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
                            <div class="card-body">
                                <form id="shiftForm"
                                      action="{{ route('shift.store') }}"
                                      method="POST">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="shift_name"
                                               class="col-sm-3 col-form-label">Shift Name</label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                   name="shift_name"
                                                   id="shift_name"
                                                   class="form-control @error('shift_name') is-invalid @enderror"
                                                   value="{{ old('shift_name', $shift->shift_name ?? '') }}"
                                                   required>
                                            @error('shift_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="start_time"
                                               class="col-sm-3 col-form-label">Start Time</label>
                                        <div class="col-sm-9">
                                            <input type="time"
                                                   name="start_time"
                                                   id="start_time"
                                                   class="form-control @error('start_time') is-invalid @enderror"
                                                   value="{{ old('start_time', $shift->start_time ?? '') }}">
                                            <small id="start_time_error"
                                                   class="text-danger"></small>
                                            @error('start_time')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="end_time"
                                               class="col-sm-3 col-form-label">End Time</label>
                                        <div class="col-sm-9">
                                            <input type="time"
                                                   name="end_time"
                                                   id="end_time"
                                                   class="form-control @error('end_time') is-invalid @enderror"
                                                   value="{{ old('end_time', $shift->end_time ?? '') }}">
                                            <small id="end_time_error"
                                                   class="text-danger"></small>
                                            @error('end_time')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="description"
                                               class="col-sm-3 col-form-label">Description</label>
                                        <div class="col-sm-9">
                                            <textarea name="description"
                                                      id="description"
                                                      rows="3"
                                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $shift->description ?? '') }}</textarea>
                                            @error('description')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="status"
                                               class="col-sm-3 col-form-label">Status</label>
                                        <div class="col-sm-9">
                                            <select name="status"
                                                    class="form-control @error('status') is-invalid @enderror">
                                                <option value="1"
                                                        {{ old('status', $shift->status ?? '') == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="2"
                                                        {{ old('status', $shift->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            @error('status')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group text-center mt-4">
                                        <button type="submit"
                                                id="save-btn"
                                                class="btn btn-success px-4">
                                            Add Shift
                                        </button>
                                    </div>
                                </form>
                            </div>



                        </div>
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
            opacity: 1;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s, color 0.3s;
        }

        #save-btn:hover {
            background-color: #28a745;
            color: #fff200;
        }
    </style>
@endpush


{{-- Push extra scripts --}}
@push('js')
    <script>
document.getElementById('shiftForm')?.addEventListener('submit', function (e) {
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const startError = document.getElementById('start_time_error');
    const endError = document.getElementById('end_time_error');

    // Clear previous messages
    startError.textContent = '';
    endError.textContent = '';

    const startVal = startInput.value;
    const endVal = endInput.value;

    let hasError = false;

    if (!startVal) {
        startError.textContent = 'Start time is required.';
        hasError = true;
    }

    if (!endVal) {
        endError.textContent = 'End time is required.';
        hasError = true;
    }

    if (startVal && endVal) {
        // Convert time strings to total minutes since midnight
        const [startHour, startMin] = startVal.split(':').map(Number);
        const [endHour, endMin] = endVal.split(':').map(Number);

        const startTotalMinutes = startHour * 60 + startMin;
        const endTotalMinutes = endHour * 60 + endMin;

        // Allow overnight shifts: e.g., 10:00 PM to 6:00 AM
        if (startTotalMinutes === endTotalMinutes) {
            startError.textContent = 'Start and end times cannot be the same.';
            hasError = true;
        }
    }

    if (hasError) {
        e.preventDefault();
    }
});
</script>
@endpush
