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
        <!-- Database Dropdown -->


        <!-- Result Table -->
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-body d-flex flex-column align-items-center position-relative">
                            <form action="#"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  style="width: 100%;">

                                @csrf

                                <!-- File input + Sync time in one line -->
                                <div class="d-flex justify-content-center flex-wrap mb-4 w-100"
                                     style="gap: 50px;">

                                    <!-- File upload -->
                                    <div class="d-flex align-items-center"
                                         style="white-space: nowrap;">
                                        <div class="me-3">
                                            <label class="text-primary pt-2 mb-0 mr-2">
                                                <h5>Please Select Your Database Location</h5>
                                            </label>
                                        </div>
                                        <div class="custom-file"
                                             style="max-width: 300px;">
                                            <input type="file"
                                                   class="custom-file-input"
                                                   id="customFile"
                                                   name="database_file">
                                            <label class="custom-file-label"
                                                   for="customFile">Choose file</label>
                                        </div>
                                    </div>

                                    <!-- Sync time -->
                                    <div class="d-flex align-items-center"
                                         style="white-space: nowrap;">
                                        <div class="me-3">
                                            <label class="text-primary pt-2 mr-2">
                                                <h5>Please Insert Your Sync Time</h5>
                                            </label>
                                        </div>
                                        <div class="form-group mb-4"
                                             style="min-width: 150px;">
                                            <select class="form-control"
                                                    id="syncTimeId"
                                                    name="sync_time">
                                                <option>Every Minute </option>
                                                <option>Every Thirty Minutes</option>
                                                <option>Hourly</option>
                                                <option>Every 2 hours</option>
                                                <option>Daily </option>
                                                <option>Daily at (13:00pm) </option>
                                                <option>Tow Time Daily ( 1, 13 )</option>
                                                <option>Between('9:00'>>>'17:00')</option>
                                               
                                              
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <!-- Change button centered below -->
                                <div class="d-flex justify-content-center w-100 mt-4">
                                    <button type="submit"
                                            id="save-btn"
                                            class="btn btn-sm btn-primary">Change</button>
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
