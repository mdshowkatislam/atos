@extends('layouts.master')
@section('subtitle', 'Databases')
@section('content_header_title', 'Settings')
@section('content_header_subtitle', 'All Databases')

@section('content')
@push('css')
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }
        table, th, td {
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

    @if(count($data))
        <table align="center">
            <thead>
                <tr>
                    <th>Table Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $table)
                    <tr>
                        <td>{{ $table }}</td>
                        <td>
                            <a href="{{ route('access.showTable', $table) }}" class="btn btn-primary">View Data</a>
                            <a href="{{ route('access.sql', $table) }}" class="btn btn-secondary">Generate SQL</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center;">No tables found in the uploaded database.</p>
    @endif

@endsection