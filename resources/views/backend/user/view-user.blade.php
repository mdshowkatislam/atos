@extends('layouts.master')
{{-- Customize layout sections --}}
@section('subtitle', 'Databases')
@section('content_header_title', 'User Management')
@section('content_header_subtitle', 'Users List')
@section('content_body')

<div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h4 class="m-0 text-dark"> @lang('List of Users')</h4> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">@lang('Home')</a></li> --}}
                        <li class="breadcrumb-item"><a href="#">@lang('Home')</a></li> &nbsp;
                        <li class="breadcrumb-item active">@lang('User')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->
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
                        <h5 class="float-left">{{ $type == 'user' ? 'User List' : 'Club User List' }}</h5>
                        
                        <a class="btn btn-sm btn-success float-right ml-2" href="{{ route('user.add') }}"><i class="fa fa-plus-circle"></i> @lang('User') @lang('Add')</a>
                    
                        <a class="btn btn-sm btn-success float-right" href="{{ route('user') }}"><i class="fa fa-list"></i>
                            @lang('User') @lang('List')</a>
                    </div>
                    <div class="card-body">
                        <table id="dataTable" class="table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('SL')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Role')</th>
                                    {{-- <th>@lang('Status')</th> --}}
                                    <th style="width:10%">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user as $u)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $u['name'] }}</td>
                                        <td>{{ $u['email'] }}</td>
                                        <td>{{ @$u['user_roles']['role_details']['name'] }}</td>
                                        {{-- <td>
                    <input type="checkbox" data-id="{{ $u->id }}" name="status" class="js-switch" {{ $u->status == 1 ? 'checked' : '' }}>
                  </td> --}}
                                        {{-- <td><span class="badge {{ $u['status'] == 1 ? 'badge-success' : 'badge-danger' }}">{{ $u['status'] == 1 ? (session()->get('language') == 'en' ? 'Active' : 'Active') : (session()->get('language') == 'en' ? 'Inactive' : 'Inactive') }}</span></td> --}}
                                        <td>
                                            <a class="btn btn-sm btn-primary" title="Edit" href="{{ route('user.edit', $u->id) }}"><i class="fa fa-edit"></i></a>
                                            <a class="btn btn-sm btn-info text-white changeUserPassword " data-id="{{ $u->id }}" title="Reset Password" style="cursor: pointer">
                                                <i class="fa fa-key"></i>
                                            </a>
                                            {{-- <a class="btn btn-sm btn-danger" id="delete" title="Delete" data-id="{{$u->id}}" data-token="{{csrf_token()}}" href="{{route('user.delete')}}"><i class="fa fa-trash"></i></a> --}}
                                            <!-- <a class="delete btn btn-sm btn-danger" data-id="{{ $u['id'] }}" data-table="users"><i class="fa fa-trash"></i></a> -->
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).on('click', '.changeUserPassword', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Reset Password',
                html: `
                <div style="padding:20px;">
                  <input type="password" id="password" class="swal2-input" placeholder="Please Enter Password" style="margin:0; width:100%;">
                  <input type="password" id="confirm_password" class="swal2-input" placeholder="Please Enter Confirm Password" style="margin:0; margin-top:20px; width:100%;">
                  <div class="password-requirements" style="text-align:left; font-size:14px; margin-top:10px;">
                      Password must have:
                      <ul style="margin:0">
                          <li>At least 6 characters</li>
                          <li>At least one letter</li>
                          <li>At least one uppercase letter</li>
                          <li>At least one lowercase letter</li>
                          <li>At least one number</li>
                          <li>At least one special character</li>
                      </ul>
                  </div>
                </div>
            
            `,
                confirmButtonText: 'Reset Password',
                focusConfirm: false,
                showLoaderOnConfirm: true,
                showDenyButton: true,
                denyButtonText: `Don't change`,
                preConfirm: () => {
                    const password = Swal.getPopup().querySelector('#password').value
                    const confirm_password = Swal.getPopup().querySelector('#confirm_password').value
                    if (!password) {
                        Swal.showValidationMessage(`Please Enter Password`);
                    } else if (!confirm_password) {
                        Swal.showValidationMessage(`Please Enter Confirm Password`);
                    } else if (password != confirm_password) {
                        Swal.showValidationMessage(`Password not match`);
                    } else {
                        return $.ajax({
                            url: '{{ route('reset.password') }}' + '?id=' + id +
                                '&password=' + password,
                            type: 'get'
                        }).then(response => {
                            if (response.status == 'error') {
                                Swal.showValidationMessage(response.message);
                            } else {
                                return {
                                    password: password,
                                    confirm_password: confirm_password
                                }
                            }
                        })
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    // location.reload();
                    Swal.fire(
                        'Done!',
                        'Your password has been reset.',
                        'success'
                    )
                } else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info')
                }
            });
        });
    </script>

@stop

@push('js')
    <script> console.log("Hi, We are using the access to sql package!"); </script>
@endpush