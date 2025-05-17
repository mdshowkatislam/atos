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
                            <form action="{{ route('admin.update_schedule') }}"
                                  class="d-flex flex-column align-items-center"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  style="width: 100%;">

                                @csrf

                                <!-- File input + Sync time in one line -->
                                <div class="d-flex justify-content-center flex-wrap mb-4 w-100 p-3 rounded"
                                     style="gap: 50px;">

                                    <!-- File upload -->
                                   
                                        <div class="me-3">
                                            <label class="text-primary pt-2 mb-0 mr-2">
                                                <h5>Please Insert Your Database Location Url</h5>
                                            </label>
                                        </div>
                                        <div class="custom-file"
                                             style="max-width: 300px;">
                                            <input type="text" style="width:270px !important;"
                                                   class="form-control"
                                                   id="customFile"
                                                   name="location" placeholder="Url">
                                           
                                        </div>
                                 

                                    <!-- Sync time -->
                                
                                        <div class="me-3">
                                            <label class="text-primary pt-2 mr-2">
                                                <h5>Please Insert Your Sync Time</h5>
                                            </label>
                                        </div>
                                        <div class="form-group mb-4"
                                             style="min-width: 150px;">
                                          <select class="form-control" 
                                                    id="syncTimeId"
                                                    name="syncTimeName">
                                                <option value="1">Every Minute </option>
                                                <option value="2">Every Thirty Minutes</option>
                                                <option value="3">Hourly</option>
                                                <option value="4">Every 2 hours</option>
                                                <option value="5">Daily </option>
                                                <option value="6">Daily at (13:00pm) </option>
                                                <option value="7">Tow Time Daily ( 1, 13 )</option>
                                                <option value="8">Between('9:00'>>>'17:00')</option>
                                               
                                              
                                            </select>
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
