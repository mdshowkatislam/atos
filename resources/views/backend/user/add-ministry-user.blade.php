 @extends('backend.layouts.app')
@section('content')
<style type="text/css">
  .i-style{
        display: inline-block;
        padding: 10px;
        width: 2em;
        text-align: center;
        font-size: 2em;
        vertical-align: middle;
        color: #444;
  }
  .demo-icon{cursor: pointer; }
</style>
<div class="col-xl-12">
	<div class="breadcrumb-holder">
		<h3 class="main-title float-left" style="margin-top:10px; margin-left:10px;">
			@lang('Ministry') @lang('User')
		</h3>
		<ol class="breadcrumb float-right">
			<li class="breadcrumb-item"><a href="{{route('dashboard')}}"><strong>@lang('Home')</strong></a></li>
			<li class="breadcrumb-item active">@lang('User')</li>
		</ol>
		<div class="clearfix"></div>
	</div>
</div>

<div class="container-fluid">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<h5>
					@if(isset($editData))
					@lang('User') @lang('Update')
					@else
					@lang('User') @lang('Add')
					@endif
					<a class="btn btn-sm btn-success float-right" href="{{route('user.ministry')}}"><i class="fa fa-list"></i> @lang('User') @lang('List')</a></h5>
			</div>
			<!-- Form Start-->
			<form method="post" action="{{!empty($editData->id) ? route('user.ministry.update',$editData->id) : route('user.ministry.store')}}" id="myForm">
				{{csrf_field()}}
				<div class="card-body">
					<div class="show_module_more_event">
						<div class="form-row">
							<div class="form-group col-md-6">
								<label class="control-label">@lang('User') <span class="text-red">*</span></label>
								{{-- @php
										$ministryUsers = DB::table('users')->first();
										dd($ministryUsers->user_ministry);
									@endphp --}}
									{{-- @php dd($user_name); @endphp
									@foreach($user_name as $key => $user)
									@php dd($user); @endphp
											@foreach ($user as $u)
											@php dd($u->user_ministry->user_id); @endphp
											@php dd(@$u->user_roles->role_id); @endphp
											@endforeach
										@endforeach  --}}
								<select name="user_id" id="role_id" class="form-control form-control-sm selectpicker" @if(!isset($editData)) data-live-search="true" @endif>
									
									@if(!isset($editData))
										<option value="">@lang('Select')</option>
										@foreach($user_name as $key => $user)
											@foreach ($user as $u)
											@if(@$u->user_ministry == null)
											<option value="{{$u['id']}}" {{(@$editData->user_id == $u['id']) ? 'selected' : ''}}>{{ $u['name'] }}</option>
											@else
											@endif
											@endforeach
										@endforeach
									@else
										<option selected value="{{@$editData->user_id}}">{{ @$editData->user_name->name }}</option>
									@endif
								</select>
							</div>
							<div class="form-group col-md-6">
								<label class="control-label">@lang('Ministry') <span class="text-red">*</span></label>
								<select name="ministry_id" id="role_id" class="form-control form-control-sm selectpicker" data-live-search="true">
					                <option value="">@lang('Select')</option>
									@foreach($ministrys as $ministry)
									@if(session()->get('language') =='en')
					                <option value="{{ $ministry->id }}"  {{(@$editData->ministry_id == $ministry->id) ? "selected" : ""}}>{{ $ministry->name_en }}</option>
					                @else
					                <option value="{{ $ministry->id }}"  {{(@$editData->ministry_id == $ministry->id) ? "selected" : ""}}>{{ $ministry->name_bn }}</option>
                                     @endif
									@endforeach
								</select>
							</div>
							<div class="form-group col-md-6">
								<label class="control-label">@lang('Designation') <span class="text-red">*</span></label>
								<input type="text" name="designation" id="password" class="form-control form-control-sm" value="{{@$editData->designation}}" placeholder="@lang('Designation')">
							</div>
							<div class="form-group col-md-6">
								<label class="control-label">@lang('Department') <span class="text-red">*</span></label>
								<input type="text" name="department" class="form-control form-control-sm" value="{{@$editData->department}}" placeholder="@lang('Department')">
							</div>
						</div>
					</div>

					<button type="submit" class="btn btn-success btn-sm">
						@if(isset($editData))
						@lang('Update')
						@else
						@lang('Save')
						@endif
					</button>
				</div>
			</form>
			<!--Form End-->
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
      $('#myForm').validate({
        rules: {
			user_id: {
            required: true,
          },
          ministry_id: {
            required: true,
          },
          designation: {
            required: true,
          },
          department : {
            required : true,
          },
         
        },
        messages: {
          
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    });
  </script>

@endsection