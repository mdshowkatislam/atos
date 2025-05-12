@extends('backend.layouts.app')
@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        {{-- <h1 class="m-0 text-dark"> @lang('List of Roles')</h1> --}}
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{route('dashboard')}}">@lang('Home')</a></li>
          <li class="breadcrumb-item active">@lang('Role')</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<style>
  .select2-container .select2-selection--single {
    height: 35px !important;
  }
  .select2-container{
    width: 100% !important;
  }
</style>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h4 class="m-0 text-dark float-left"> @lang('List of Roles')</h4>
            <a id="demo-btn-addrow" class="btn btn-success btn-sm text-white float-right" data-toggle="modal" data-target="#myModal"><i class="fas fa-plus"></i> @lang('Role') @lang('Add')</a>
          </div>
          <div class="card-body">
            <table id="dataTable" class="table table-sm table-bordered table-striped">
              <thead>
                  <tr>
                    <th class="min-width">@lang('SL')</th>
                    <th>@lang('Role') @lang('Name')</th>
                    <th>@lang('Role Details')</th>
                    {{-- <th>@lang('Working Area')</th> --}}
                    <th class="text-center" style="width: 8%">@lang('Action')</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach($roles as $role)
                <tr>
                  <td> {{$loop->iteration}}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->description }}</td>
                    {{-- <td>{{ $role->working_area }}</td> --}}
                    <td class="text-center">
                      <a class="editRole btn btn-primary btn-flat btn-sm edit" id="" data-id="{{$role->id}}"  data-type="" data-id="" data-table=""><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            @if(session()->get('language') =='en')
              <h4 class="modal-title">Role Add</h4>
            @else
              <h4 class="modal-title">@lang('Role') @lang('Add')</h4>
             @endif
          <button type="button" class="close" onclick="myFunction1()" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <form id="menuForm" action="{{ route('user.role.store') }}" method="post" >
            {{ csrf_field()}}
            <div class="card-body">
              <div class="col-sm-12">
                <div class="form-group">
                  <label class="control-label">@lang('Role') @lang('Name') <span class="text-red">*</span></label>
                  <input type="text" value="{{ old('name') }}" id="addRollErr" name="name" class="form-control {{$errors->has('name') ? 'is-invalid' : '' }} InputErr" placeholder="@lang('Role') @lang('Name')">
                  @if ($errors->has('name'))
                  <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                  </span>
                  @endif
                </div>
                <div class="form-group">
                  <label class="control-label">@lang('Role Details')</label>
                  <textarea id="" value="{{ old('description') }}" name="description" class="form-control" placeholder="@lang('Role Details')" rows="6"></textarea>
                </div>
                {{-- <div class="form-group" style="">
                  <label for="working_area">@lang('Working Area')</label>
                  <select name="working_area" class="form-control">
                    <option disabled selected value="">@lang('Select')</option>
                    <option value="HQ" {{(@$roleData->working_area=="HQ")?"selected":""}}>HQ</option>
                    <option value="Circle" {{(@$roleData->working_area=="Circle")?"selected":""}}>Circle</option>
                    <option value="District" {{(@$roleData->working_area=="District")?"selected":""}}>District</option>
                    <option value="City Corporation" {{(@$roleData->working_area=="City Corporation")?"selected":""}}>City Corporation</option>
                    <option value="Pourashava" {{(@$roleData->working_area=="Pourashava")?"selected":""}}>Pourashava</option>
                  </select>
                </div> --}}
              </div>
            </div>
          </div>
          <div class="card-footer text-right">

          @if(session()->get('language') =='en')
             <button class="btn btn-success" type="submit">Save</button>
          @else
             <button class="btn btn-success" type="submit">@lang('Save')</button>
          @endif

          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            @if(session()->get('language') =='en')
                <h4 class="modal-title">Role Update</h4>
            @else
                <h4 class="modal-title">@lang('Role') @lang('Update')</h4>
            @endif
          <button type="button" class="close" onclick="myFunction()" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <form id="menuForm2" action="" method="post" >
            {{ csrf_field()}}
            <div class="card-body">
              <div class="col-sm-12">
                <div class="form-group">
                  <label class="control-label">@lang('Role') @lang('Name') <span class="text-red">*</span></label>
                  <input type="text" id="name" value="{{ old('name') }}" name="name_update" class="form-control {{$errors->has('name_update') ? 'is-invalid' : '' }}" placeholder="@lang('Role') @lang('Name')">
                  @if ($errors->has('name_update'))
                  <span class="invalid-feedback">
                    <strong>{{ $errors->first('name_update') }}</strong>
                  </span>
                  @endif
                </div>
                <div class="form-group">
                  <label class="control-label">@lang('Role Details')</label>
                  <textarea id="description" value="{{ old('description') }}" name="description" class="form-control" placeholder="@lang('Role Details')" rows="6"></textarea>
                </div>
                {{-- <div class="form-group" style="">
                  <label for="working_area">@lang('Working Area')</label>
                  <select id="working_area" name="working_area" class="form-control">
                    <option disabled selected value="">@lang('Select')</option>
                    <option value="HQ" {{(@$roleData->working_area=="HQ")?"selected":""}}>HQ</option>
                    <option value="Circle" {{(@$roleData->working_area=="Circle")?"selected":""}}>Circle</option>
                    <option value="District" {{(@$roleData->working_area=="District")?"selected":""}}>District</option>
                    <option value="City Corporation" {{(@$roleData->working_area=="City Corporation")?"selected":""}}>City Corporation</option>
                    <option value="Pourashava" {{(@$roleData->working_area=="Pourashava")?"selected":""}}>Pourashava</option>
                  </select>
                </div> --}}
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            @if(session()->get('language') =='en')
                <button class="btn btn-success" type="submit">Update</button>
            @else
                <button class="btn btn-success" type="submit">@lang('Update')</button>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {

    $("#demo-btn-addrow").click(function(e){
      $("input[type=text],select,input[type=number]").val("");
    });

    var err='{{count($errors->all())}}';
    if(err>0){
        if('{{ $errors->has("name") }}'){
            $('#myModal').modal('show');
      }
    }
  });



  $(document).ready(function(){
    var err='{{count($errors->all())}}';
    console.log(err);
      $(".editRole").click(function(){
      var roleid = $(this).attr('data-id');
        $.ajax({
          url: "{{ route('user.role.edit') }}",
          type: "GET",
          data: {'id' : roleid},
          success: function(data){
            var actionUrl = '{{route("user.role.update", "/")}}'+'/'+data.id;
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#description').val(data.description);
            // $('#working_area').val(data.working_area);
            //console.log(data.working_area);
            // $('#working_area option[value="'+data.working_area+'"]').prop('selected', true);
            $("#working_area").val(data.working_area).attr('selected','selected');
            $('#menuForm2').attr('action', actionUrl);
            $('#myModal2').modal('show');

          }

        });


    });
    if(err>0){
        if('{{ $errors->has("name_update") }}'){
          $('#myModal2').modal('show');
        }
      }
  });




function myFunction1() {
  var element = document.getElementById('addRollErr')
  console.log(element.classList);
  element.classList.remove("is-invalid");

}

function myFunction() {
  var element = document.getElementById("name");
  element.classList.remove("is-invalid");

}

</script>
<script type="text/javascript">
  $(document).ready(function() {
      $('textarea').each(function() {
          $(this).val($(this).val().trim());
      });

      $('#menuForm').validate({
          ignore: [],
          debug: false,
          errorClass: 'text-danger',
          validClass: 'text-success',
          rules: {
              name: {
                  required: true,
              }
          },
          messages: {
              name: {
                  required: "Name is required",
              }
          },
          errorElement: 'span',
          errorPlacement: function(error, element) {
              error.addClass('text-danger');
              element.closest('.form-group').append(error);
          },
          highlight: function(element, errorClass, validClass) {
              $(element).addClass('is-invalid');
          },
          unhighlight: function(element, errorClass, validClass) {
              $(element).removeClass('is-invalid');
          }
      });
  });
</script>
<script type="text/javascript">
  $(document).ready(function() {
      $('textarea').each(function() {
          $(this).val($(this).val().trim());
      });

      $('#menuForm2').validate({
          ignore: [],
          debug: false,
          errorClass: 'text-danger',
          validClass: 'text-success',
          rules: {
            name_update: {
                  required: true,
              }
          },
          messages: {
            name_update: {
                  required: "Name is required",
              }
          },
          errorElement: 'span',
          errorPlacement: function(error, element) {
              error.addClass('text-danger');
              element.closest('.form-group').append(error);
          },
          highlight: function(element, errorClass, validClass) {
              $(element).addClass('is-invalid');
          },
          unhighlight: function(element, errorClass, validClass) {
              $(element).removeClass('is-invalid');
          }
      });
  });
</script>
@endsection
