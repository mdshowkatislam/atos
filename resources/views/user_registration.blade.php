profil<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
      <!-- select2 -->
    <link rel="stylesheet" href="{{asset('public/backend/plugins/select2/css/select2.min.css')}}">
    <!-- jQuery -->
    <script src="{{asset('public/backend/plugins/jquery/jquery.min.js')}}"></script>
  </head>
  <style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice{
      background-color: #17a2b8 !important;
      border-color: #17a2b8 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
      color: #000 !important;
    }
  </style>
  <style>
    .select2-container .select2-selection--single {
      height: 35px !important;
    }
    .select2-container{
      width: 100% !important;
    }
  </style>
  <body>
    <div class="container" style="margin-top: 60px;">
    <section class="content">
        <div class="container-fluid">
    
            <div class="card">
                <div class="card-header">
                    {{-- <a href="{{route('master_setup.municipality')}}" class="btn btn-info btn-sm"><i class="fas fa-stream"></i> View municipality</a> --}}
                    <h4>User Registration</h4>
                </div>
                <div class="card-body">
                    <form action="{{ !empty($editData)? route('master_setup.municipality.update',$editData->id) : route('master_setup.municipality.store') }}" method="post" enctype="multipart/form-data" autocomplete="off" id="myForm">
                        @csrf
                        <div class="form-row row">
                            <div class="form-group col-md-4">
                                <label for="name">@lang('Full Name') <span class="text-red">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{@$editData->name}}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">@lang('Email') <span class="text-red">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{@$editData->email}}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="phone">@lang('Phone') <span class="text-red">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{@$editData->phone}}">
                            </div>
                          <div class="form-group col-md-6" style="margin-top: 10px;">
                            <label for="designation_id">@lang('Designation') <span class="text-red">*</span></label>
                            <select id="designation_id" name="designation_id" class="form-control select2">
                              <option value="s">@lang('Select')</option>
                              <option value="0" {{(@$editData->designation_id=="0")?"selected":""}}>designation 1</option>
                              <option value="1" {{(@$editData->designation_id=="1")?"selected":""}}>designation 2</option>
                              <option value="1" {{(@$editData->designation_id=="1")?"selected":""}}>designation 3</option>
                            </select>
                          </div>
                          <div class="form-group col-md-6" style="margin-top: 10px;">
                            <label for="working_area">@lang('Working Area') <span class="text-red">*</span></label>
                            <select id="working_area" name="working_area" class="form-control select2">
                              <option value="s">@lang('Select')</option>
                              <option value="HQ" {{(@$editData->working_area=="0")?"selected":""}}>HQ</option>
                              <option value="Circle" {{(@$editData->working_area=="1")?"selected":""}}>Circle</option>
                              <option value="District" {{(@$editData->working_area=="1")?"selected":""}}>District</option>
                              <option value="CityCorporation" {{(@$editData->working_area=="1")?"selected":""}}>CityCorporation</option>
                              <option value="Municipality" {{(@$editData->working_area=="1")?"selected":""}}>Municipality</option>
                            </select>
                          </div>
                          <div id="division_div" class="form-group col-md-3" style="margin-top: 10px;display: none;">
                            <label for="division">@lang('Division') <span class="text-red">*</span></label>
                            <select name="division" class="form-control select2">
                              <option value="">@lang('Select')</option>
                              <option value="0" {{(@$editData->division=="0")?"selected":""}}>division 1</option>
                              <option value="1" {{(@$editData->division=="1")?"selected":""}}>division 2</option>
                              {{-- @foreach($divisions as $division)
                              <option value="{{ $division->id }}" {{(@$editData->division_id == $division->id)?"selected":""}}>{{ $division->name }}</option>
                              @endforeach --}}
                            </select>
                          </div>
                          <div id="circle_div" class="form-group col-md-3" style="margin-top: 10px;display: none;">
                            <label for="circle">@lang('Circle') <span class="text-red">*</span></label>
                            <select name="circle" class="form-control select2">
                              <option value="">@lang('Select')</option>
                              <option value="0" {{(@$editData->circle=="0")?"selected":""}}>circle 1</option>
                              <option value="1" {{(@$editData->circle=="1")?"selected":""}}>circle 2</option>
                            </select>
                          </div>
                          <div id="district_div" class="form-group col-md-3" style="margin-top: 10px;display: none;">
                            <label for="district">@lang('District') <span class="text-red">*</span></label>
                            <select name="district" class="form-control select2">
                              <option value="">@lang('Select')</option>
                              <option value="0" {{(@$editData->district=="0")?"selected":""}}>district 1</option>
                              <option value="1" {{(@$editData->district=="1")?"selected":""}}>district 2</option>
                            </select>
                          </div>
                          
                          <div id="municipality_div" class="form-group col-md-3" style="display: none;margin-top: 10px;">
                            <label for="municipality">@lang('Municipality') <span class="text-red">*</span></label>
                            <select name="municipality" class="form-control select2">
                              <option value="">@lang('Select')</option>
                              <option value="0" {{(@$editData->municipality=="0")?"selected":""}}>municipality 1</option>
                              <option value="1" {{(@$editData->municipality=="1")?"selected":""}}>municipality 2</option>
                            </select>
                          </div>
                          <div id="city_corporation_div" class="form-group col-md-3" style="display: none;margin-top: 10px;">
                            <label for="city_corporation">@lang('City Corporation') <span class="text-red">*</span></label>
                            <select name="city_corporation" class="form-control select2">
                              <option value="">@lang('Select')</option>
                              <option value="0" {{(@$editData->city_corporation=="0")?"selected":""}}>city corporation 1</option>
                              <option value="1" {{(@$editData->city_corporation=="1")?"selected":""}}>city corporation 2</option>
                            </select>
                          </div>
                          {{-- <div id="empty_div_for_city_corporation" class="form-group col-md-3" style="display: none;margin-top: 10px;">
                            
                          </div>
    
                          <div id="empty_div_for_circle" class=" form-group col-md-3" style="display: none;margin-top: 10px;">
    
                          </div>
                          <div id="empty_div_for_district" class=" form-group col-md-3" style="display: none;margin-top: 10px;">
                            
                          </div>
                          <div id="empty_div_for_municipality" class=" form-group col-md-3" style="display: none;margin-top: 10px;">
                            
                          </div> --}}
                          {{-- Captcha will go here --}}
                          <div class="form-group col-sm-6 pull-left" style="margin-top: 10px;">
                            <button class="btn btn-success" style=""> {{ !empty($editData)? 'Submit Button' : 'Submit Button' }}</button>
                            <a class="btn btn-secondary" style=""> {{ !empty($editData)? 'Cancel Button' : 'Cancel Button' }}</a>
                          </div>
                    </div>
                </form>
                <h4 class="form-group" style="text-align: center;">OTP has been sent to Mobile</h4>
                <div class="form-row row">
                    <div class="form-group col-md-4" style="margin-top: 10px;margin-bottom: 30px;margin-left: auto;margin-right: auto;">
                        <label for="otp">@lang('Type OTP') <span class="text-red">*</span></label>
                        <input type="text" name="otp" class="form-control" value="{{@$editData->otp}}">
                    </div>
                </div>
                <h4 class="form-group" style="text-align: center;"><span id="timer">120</span> second countdown for OTP</h4>
                {{-- <h4 class="form-group" style="text-align: center;">Registration closes in <span id="timer">05:00</span> second!</h4> --}}
                <div class="form-group col-sm-3" style="margin-top: 10px;margin-bottom: 30px;margin-left: auto;margin-right: auto;display: block;">
                    <button class="btn btn-sm btn-success"> {{ !empty($editData)? 'Submit Button' : 'Submit Button' }}</button>
                    <a class="btn btn-sm btn-secondary"> {{ !empty($editData)? 'Resend OTP Button' : 'Resend OTP Button' }}</a>
                  </div>
            </div>
            {{-- <h4>OTP has been sent to Mobile</h4> --}}
    
        </div>
        <!--/. container-fluid -->
    </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <!-- select2 -->
    <script src="{{asset('public/backend/plugins/select2/js/select2.min.js')}}"></script>
    <script>
        $(function () {
          $('.select2').select2();
        });
    </script>
    <script>
        window.onload = function() {
            //var minute = 5;
            var sec = 120;
            setInterval(function() {
                //document.getElementById("timer").innerHTML = minute + " : " + sec;
                document.getElementById("timer").innerHTML = sec;
                sec--;
                if (sec == 00) {
                // minute --;
                //sec = 60;
                sec = 120;
                // if (minute == 0) {
                //     minute = 5;
                // }
                }
            }, 1000);
        }
    </script>
    <script type="text/javascript">
        $(document).on('select change','#working_area',function(){
          var working_area = $('#working_area').val();
          console.log(working_area);
          if(working_area == "HQ")
          {
              document.getElementById("division_div").style.display = "none";
              document.getElementById("circle_div").style.display = "none";
              document.getElementById("district_div").style.display = "none";
              document.getElementById("city_corporation_div").style.display = "none";
              document.getElementById("municipality_div").style.display = "none";
      
            //   document.getElementById("empty_div_for_initial_condition_1").style.display = "none";
            //   document.getElementById("empty_div_for_initial_condition_2").style.display = "none";
          }
          else if(working_area == "Circle")
          {
              document.getElementById("division_div").style.display = "";
              document.getElementById("circle_div").style.display = "";
              document.getElementById("district_div").style.display = "";
              document.getElementById("city_corporation_div").style.display = "none";
              document.getElementById("municipality_div").style.display = "none";
      
            //   document.getElementById("empty_div_for_initial_condition_1").style.display = "none";
            //   document.getElementById("empty_div_for_initial_condition_2").style.display = "none";
          }
          else if(working_area == "District")
          {
              document.getElementById("division_div").style.display = "";
              document.getElementById("circle_div").style.display = "";
              document.getElementById("district_div").style.display = "";
              document.getElementById("city_corporation_div").style.display = "none";
              document.getElementById("municipality_div").style.display = "none";
      
            //   document.getElementById("empty_div_for_initial_condition_1").style.display = "none";
            //   document.getElementById("empty_div_for_initial_condition_2").style.display = "none";
          }
          else if(working_area == "CityCorporation")
          {
              document.getElementById("division_div").style.display = "";
              document.getElementById("circle_div").style.display = "";
              document.getElementById("district_div").style.display = "";
              document.getElementById("city_corporation_div").style.display = "";
              document.getElementById("municipality_div").style.display = "none";
      
            //   document.getElementById("empty_div_for_initial_condition_1").style.display = "none";
            //   document.getElementById("empty_div_for_initial_condition_2").style.display = "none";
          }
          else if(working_area == "Municipality")
          {
              document.getElementById("division_div").style.display = "";
              document.getElementById("circle_div").style.display = "";
              document.getElementById("district_div").style.display = "";
              document.getElementById("municipality_div").style.display = "";
              document.getElementById("city_corporation_div").style.display = "none";
      
            //   document.getElementById("empty_div_for_initial_condition_1").style.display = "none";
            //   document.getElementById("empty_div_for_initial_condition_2").style.display = "none";
          }
          else if(working_area == "s")
          {
              document.getElementById("division_div").style.display = "none";
              document.getElementById("circle_div").style.display = "none";
              document.getElementById("city_corporation_div").style.display = "none";
              document.getElementById("municipality_div").style.display = "none";
              document.getElementById("district_div").style.display = "none";
      
            //   document.getElementById("empty_div_for_initial_condition_1").style.display = "block";
            //   document.getElementById("empty_div_for_initial_condition_2").style.display = "block";
          }
        });
      </script>
  </body>
</html>