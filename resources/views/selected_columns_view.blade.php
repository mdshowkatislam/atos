@extends('layouts.master')
@push('css')
<style>
       .content-wrapper {
        min-height: calc(100vh - 100px); /* Footer & header height combined */
    }
    .main-footer{
     display: none !important;
    }
    .mybtn:hover{
        background-color: red !important;
        color: white !important;
    }

</style>
@endpush

@section('content_body')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark">Selected Columns</h4>
            </div>
            <div class="col-sm-6">
                <ol class="float-sm-right pt-2">
                    
              
                    <div><button class="btn btn-sm btn-warning mybtn">Send This data</button></div>
            </div>

            <table class="table table-bordered p-4">
                <thead>
                    <tr>
                        @foreach ($columns as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            @foreach ($columns as $col)
                                <td>{{ $row->$col }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
           
            

        @endsection

    
