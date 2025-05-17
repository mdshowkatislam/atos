@extends('layouts.master')
{{-- Customize layout sections --}}
@section('subtitle', 'Dashboard')
@section('content_header_title', 'Dashboard')
@section('content_header_subtitle', 'Welcome')
{{-- Content body: main page content --}}
@section('content_body')
    <p>Code Shotcut, Welcome to this Access to Mysql db project............................</p>
@stop
{{-- Push extra CSS --}}
@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush
{{-- Push extra scripts --}}
@push('js')
    <script> console.log("Hi, this is out Finger print Access db to Mysql db convertion project"); </script>
@endpush


