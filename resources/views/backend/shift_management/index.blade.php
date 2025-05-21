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

@section('content_body')

    <div class="content">
      
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-body d-flex flex-column align-items-center position-relative">
                            <form action="{{ route('admin.shift.add') }}"
                                  class="d-flex flex-column align-items-center"
                                  method="POST"
                                  style="width: 100%;">

                                @csrf

                                <!-- File input + Sync time in one line -->
                                <div class="d-flex justify-content-center flex-wrap mb-4 w-100 p-3 rounded"
                                     style="gap: 50px;">

                                    <!-- File upload -->

                                    <div class="me-3">
                                        <label class="text-primary pt-2 mb-0 mr-2">
                                            <h5>Please Insert Your Shift Start Time</h5>
                                        </label>
                                    </div>
                                    <div class="custom-file"
                                         style="max-width: 300px;">
                                        <input type="time"
                                               style="width:170px !important;"
                                               class="form-control"
                                               value="{{ isset($shift->start_time) ? $shift->start_time : '' }}"
                                               name="start_time"    
                                               >

                                    </div>
                                    <div class="me-3">
                                        <label class="text-primary pt-2 mb-0 mr-2">
                                            <h5>Please Insert Your Shift End Time</h5>
                                        </label>
                                    </div>
                                    <div class="custom-file"
                                         style="max-width: 300px;">
                                        <input type="time"
                                               style="width:170px !important;"
                                               class="form-control"
                                                value="{{ isset($shift->end_time) ? $shift->end_time : '' }}"
                                                    name="end_time">
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
    <script>
        $(document).ready(function() {

            $(".custom-file-input").on("change", function() {
                var fileName = $(this).val().split("\\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });
        });
    </script>
@endpush
