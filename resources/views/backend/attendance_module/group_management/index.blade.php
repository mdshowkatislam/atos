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
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script type="text/javascript">
                $(function() {
                    $.notify("{{ $error }}", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                });
            </script>
        @endforeach
    @endif

    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">


                        <a class="btn btn-sm btn-success float-right ml-2"
                           href="{{ route('shift.add') }}"><i class="fa fa-plus-circle"></i> @lang('Shift')
                            @lang('Add')</a>

                        {{--  <a class="btn btn-sm btn-success float-right"
                           href="#"><i class="fa fa-list"></i>
                            @lang('Shift') @lang('List')</a> --}}
                    </div>
                    <div class="card-body">

                        @if (isset($shift))
                            <table class="table table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>Shift Name </th>
                                        <th>Start Time </th>
                                        <th>End Time </th>
                                        <th>Description </th>
                                        <th>status </th>
                                        <th style="width:10%">Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shift as $s)
                                        <tr>
                                            <td class="fw-semibold align-middle">
                                                {{ $s->id }}

                                            </td>
                                            <td class="fw-semibold align-middle">
                                                {{ $s->shift_name }}

                                            </td>
                                            <td class="fw-semibold align-middle">
                                                {{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }}
                                            </td>
                                            <td class="fw-semibold align-middle">
                                                {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }}
                                            </td>
                                            <td class="fw-semibold align-middle">
                                                {{ $s->description }}
                                            </td>
                                            <td class="fw-semibold align-middle">
                                                {{ $s->status }}
                                            </td>
                                            <td class="fw-semibold align-middle">
                                                <a class="btn btn-sm btn-primary"
                                                   title="Edit"
                                                   href="{{ route('shift.edit', $s->id) }}"><i class="fa fa-edit"></i></a>

                                                <form action="{{ route('shift.destroy', $s->id) }}"
                                                      method="POST"
                                                      style="display:inline;"
                                                      onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger"
                                                            title="Delete"><i class="fa fa-trash"></i></button>
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach
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


