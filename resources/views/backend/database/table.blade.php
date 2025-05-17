@extends('layouts.master')
@section('subtitle', 'Databases')
@section('content_header_title', 'Settings')
@section('content_header_subtitle', 'All Databases')


@push('css')
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }

        table,
        th,
        td {
            border: 1px solid #444;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        a.btn {
            padding: 6px 12px;
            margin-right: 6px;
            text-decoration: none;
            border: 1px solid #333;
            border-radius: 4px;
            background: #ddd;
            color: #000;
        }

        a.btn-primary {
            background-color: #007bff;
            color: #fff;
        }

        a.btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
    </style>
@endpush
@section('content_body')

    <h2 style="text-align: center;">Access Database Tables</h2>

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

                    <div class="card-body">
                       @if (count($result))
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Table Name</th>
                <th>Columns</th>
                <th style="width: 12%;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result as $key => $table)
                <tr>
                    <td>{{ $table['name'] }}</td>
                    <td>
                        @foreach ($table['columns'] as $column)
                            @if ($column != 'id')
                                <div style="display:inline-block; margin-right:10px;">
                                    <input type="checkbox"
                                           name="columns[]"
                                           value="{{ $column }}"
                                           form="form_view_{{ $key }}"
                                           id="{{ $table['name'] }}_{{ $column }}">
                                    <label for="{{ $table['name'] }}_{{ $column }}">{{ $column }}</label>
                                </div>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        {{-- View Data Form --}}
                        <form id="form_view_{{ $key }}"
                              method="GET"
                              action="{{ route('admin.table.showSelected') }}">
                            <input type="hidden"
                                   name="table"
                                   value="{{ $table['name'] }}">
                            <button type="submit"
                                    class="btn btn-sm btn-primary mb-2">View Data</button>
                        </form>

                        {{-- Send Data Form --}}
                        <form method="GET"
                              action="{{ route('admin.table.send') }}">
                            <input type="hidden"
                                   name="table"
                                   value="{{ $table['name'] }}">
                            @foreach ($table['columns'] as $column)
                                @if ($column != 'id')
                                    <input type="hidden"
                                           name="columns[]"
                                           value="{{ $column }}">
                                @endif
                            @endforeach
                            <button type="submit"
                                    class="btn btn-sm btn-warning">Send Data</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p style="text-align:center;">Table not found</p>
@endif

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
