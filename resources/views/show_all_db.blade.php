@extends('layouts.master')
{{-- Customize layout sections --}}
@section('subtitle', 'Databases')
@section('content_header_title', 'Settings')
@section('content_header_subtitle', 'All Databases')
{{-- Extend and customize the page content header --}}
{{-- Content body: main page content --}}

@section('content_body')

    <p>All Databases</p>






@stop
{{-- Push extra CSS --}}
@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush
{{-- Push extra scripts --}}
@push('js')
    <script> console.log("Hi, We are using the access to sql package!"); </script>
@endpush