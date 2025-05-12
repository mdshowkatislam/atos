
@extends('backend.layouts.app')
@section('content')
<script src="{{asset('public/backend/js/amcharts4/core.js')}}"></script>
<script src="{{asset('public/backend/js/amcharts4/charts.js')}}"></script>
<script src="{{asset('public/backend/js/amcharts4/themes/kelly.js')}}"></script>
<script src="{{asset('public/backend/js/amcharts4/themes/animated.js')}}"></script>

<style type="text/css">
  h4{
    padding-top:10px;
  }
  .card-body{
    border-radius: 10px;
    text-align: center;
    padding: 0px !important;
    min-height: 350px;
  }



  .card-body img{
    width:100%;
    vertical-align: 0%;
    margin:-6px;
  }

  .card-body > ul{
    list-style: none;
    margin: 0px;
  }
  .card-body > ul > li{
    text-align:center;
  }
  .card-body > h5{
    font-size:15px;
    text-align: center;
    padding: 0px;
    padding:7px 0px;
    margin: 0px;
    color:white;

    /* color:white */
  }
  .card-clock{
    background: transparent;
    border:none;
  }
</style>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            {{-- <h1 class="m-0 text-dark">@lang('Dashboard')</h1> --}}
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('dashboard')}}">@lang('Home')</a></li>
              <li class="breadcrumb-item active">@lang('Dashboard')</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

  <style>
    .content-wrapper {


            background-image: url(public/dashboard_background2.jpg)!important;
            background-repeat: no-repeat!important;
            background-size: 100% 100%!important;
            overflow: hidden;
            opacity: 1;

    }
  </style>
  <section class="content">
    <div class="container-fluid">
     
      <div class="row">
        <form method="POST" action="{{ route('access.upload') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="access_file" required>
            <button type="submit">Upload</button>
        </form>
      </div>
    </div>
  </section>
    <script type="text/javascript">
      $(function(){
        function showTime(){
          var date = new Date();
          var h = date.getHours();
          var m = date.getMinutes();
          var s = date.getSeconds();
          var session = "AM";
          if(h == 0){
            h = 12;
          }
          if(h > 12){
            h = h - 12;
            session = "PM";
          }
          h = (h < 10) ? "0" + h : h;
          m = (m < 10) ? "0" + m : m;
          s = (s < 10) ? "0" + s : s;

          var time = h + ":" + m + ":" + s + " " + session;
          document.getElementById("clock").innerText = time;
          setTimeout(showTime, 1000);
        }

        showTime();
      })
    </script>
  @endsection



