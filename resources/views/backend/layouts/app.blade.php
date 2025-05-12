<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Admin Panel</title>
    <link rel="icon" href="{{ asset('public/images/logo/favicon.ico') }}" />
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('public/backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('public/backend/css/adminlte.min.css') }}">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('public/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- summernote -->
    {{-- <link rel="stylesheet" href="{{asset('public/backend/plugins/summernote/summernote-bs4.css')}}"> --}}
    <!-- select2 -->
    <link rel="stylesheet" href="{{ asset('public/backend/plugins/select2/css/select2.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('public/backend/plugins/toastr/toastr.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('public/backend/plugins/datatables/dataTables.bootstrap4.css') }}">
    <!-- Css for tree view -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/plugins/jstree/style.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('public/backend') }}/plugins/daterangepicker/daterangepicker.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="{{ asset('public/backend/plugins/boostrap-select/bootstrap-select.min.css') }}">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('public/backend') }}/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Sweet alert -->
    <script src="{{ asset('public/backend') }}/sweetalert/sweetalert.js"></script>
    <link href="{{ asset('public/backend') }}/sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
    <!-- Switchery -->
    {{--
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css"> --}}
    <link href="{{ asset('public/lou-multi-select/css/multi-select.css') }}" media="screen" rel="stylesheet"
        type="text/css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <style type="text/css">
        .nav-tabs .nav-item {
            margin-bottom: 0px;
        }

        input[type="file"] {
            padding: 0.175rem 0.75rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #000 !important;
        }

        .bg-gradient-success {
            background: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }

        table :where(thead th) {
            vertical-align: middle !important;
        }
    </style>
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
        }
    </style>

    <!-- jQuery -->
    <script src="{{ asset('public/backend/plugins/jquery/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            @include('backend.layouts.navbar')
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('backend.layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer" style="font-size: 14px;">
            <strong>@lang('Copyright') &copy; {{ date('Y') }} <span
                    style="color:#17a2b8">@lang('BUP')</span></strong>
            {{-- <strong style="margin-left:25%">Development Completion on <span style="color:#17a2b8">April
                    2022</span></strong> --}}
            <div class="float-right d-none d-sm-inline-block">
                <b>@lang('Developed by:') </b> <a target="_blank" href="http://www.nanoit.biz"> <span
                        style="color:#17a2b8;font-weight:bold">@lang('Nanosoft')</span></a>
            </div>
        </footer>
    </div>

    <script src="{{ asset('public/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('public/backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('public/backend/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('public/backend/plugins/datatables/dataTables.bootstrap4.js') }}"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset('public/backend/js/adminlte.js') }}"></script>
   

    <script src="{{ asset('public/backend/js/validate.min.js') }}"></script>
    <script src="{{ asset('public/backend/js/additional-methods.js') }}"></script>
    <!-- Handle bar -->
    <script src="{{ asset('public/backend/js/handlebars-v4.0.12.js') }}"></script>
    <!-- OPTIONAL SCRIPTS -->
    <script src="{{ asset('public/backend/js/demo.js') }}"></script>
    <script src="{{ asset('public/backend') }}/plugins/moment/moment.min.js"></script>
    <script src="{{ asset('public/backend') }}/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('public/backend') }}/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js">
    </script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="{{ asset('public/backend/plugins/boostrap-select/bootstrap-select.min.js') }}"></script>
 
    <!-- select2 -->
    <script src="{{ asset('public/backend/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Js for tree view -->
    <script src="{{ asset('public/backend/plugins/jstree/jstree.js') }}"></script>
    <!-- Validate js -->
    <script src="{{ asset('public/backend/js/validate.min.js') }}"></script>
    <script src="{{ asset('public/backend/js/additional-methods.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('public/backend/plugins/toastr/toastr.min.js') }}"></script>
    <!-- Notifyjs -->
    <script src="{{ asset('public/backend/js/notify.js') }}"></script>
    <script src="{{ asset('public/backend/js/nestable.js') }}"></script>
    <script src="{{ asset('public/backend/ckeditor/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/backend/ckeditor/ckfinder/ckfinder.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            var ck = CKEDITOR.replaceAll('textarea');
            CKFinder.setupCKEditor(ck, '/ckfinder/');
        })
    </script>
    <script type="text/javascript">
        $(function() {
            CKEDITOR.editorConfig = function(config) {
                config.toolbarGroups = [{
                        name: 'document',
                        groups: ['mode', 'document', 'doctools']
                    },
                    {
                        name: 'clipboard',
                        groups: ['clipboard', 'undo']
                    },
                    {
                        name: 'editing',
                        groups: ['find', 'selection', 'spellchecker', 'editing']
                    },
                    {
                        name: 'forms',
                        groups: ['forms']
                    },
                    '/',
                    {
                        name: 'basicstyles',
                        groups: ['basicstyles', 'cleanup']
                    },
                    {
                        name: 'paragraph',
                        groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
                    },
                    {
                        name: 'links',
                        groups: ['links']
                    },
                    {
                        name: 'insert',
                        groups: ['insert']
                    },
                    '/',
                    {
                        name: 'styles',
                        groups: ['styles']
                    },
                    {
                        name: 'colors',
                        groups: ['colors']
                    },
                    {
                        name: 'tools',
                        groups: ['tools']
                    },
                    {
                        name: 'others',
                        groups: ['others']
                    },
                    {
                        name: 'about',
                        groups: ['about']
                    }
                ];
            };
        })
    </script>
    <script src="{{ asset('public/lou-multi-select/js/jquery.multi-select.js') }}"></script>
    <script>
        $('.lou-multi-select').multiSelect({
            keepOrder: true
        });
    </script>
  

    <script>
        $(document).ready(function() {
            $(document).on('click', '.js-switch', function() {
                let status = $(this).prop('checked') === true ? 1 : 0;
                let sliderId = $(this).data('id');
                //let sliderId = $(this).closest('td').data('id')
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '{{ route('user.status.change') }}',
                    data: {
                        'status': status,
                        'id': sliderId
                    },
                    success: function(data) {
                        toastr.options.closeButton = true;
                        toastr.options.closeMethod = 'fadeOut';
                        toastr.options.closeDuration = 100;
                        toastr.success(data.success);
                    }
                });

            });
        });
    </script>

    <script>
        $(function() {
            $('.select2').select2();
          
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        });

        $('#dataTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": true,

        });

        $(".main-table").clone(true).appendTo('#table-scroll').addClass('clone');
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '#delete', function() {
                var actionTo = $(this).attr('data-route');
                var token = $(this).attr('data-token');
                var id = $(this).attr('data-id');
                swal({
                        title: "Are you sure?",
                        type: "success",
                        showCancelButton: true,
                        confirmButtonClass: 'btn-success',
                        confirmButtonText: 'Yes',
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: actionTo,
                                type: 'post',
                                data: {
                                    id: id,
                                    _token: token
                                },
                                success: function(data) {
                                    swal({
                                            title: "Deleted!",
                                            type: "success"
                                        },
                                        function(isConfirm) {
                                            if (isConfirm) {
                                                location.reload();
                                            }
                                        });
                                }
                            });
                        } else {
                            swal("Cancelled", "", "error");
                        }
                    });
                return false;
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $(document).on('click', '.statuschange', function() {
                var btnThis = $(this);
                var routeName = ($(this).data("route"));
                var id = ($(this).data("id"));
                var action = ($(this).data("action"));
                var tabName = $(this).data("tabname");
                var _token = $(this).data("token");
                if (action == '1') {
                    var status = 1;
                } else {
                    var status = 0;
                }
                //console.log(status);
                $.ajax({
               
                    url: routeName,
                    type: 'post',
                    data: {
                        'id': id,
                        'status': status,
                        'tablename': tabName,
                        '_token': _token
                    },
                    success: function(data) {
                        $('.notifyjs-corner').html('');
                        $('#toast-container').html('');
                        if (data.status == '1') {
                            // $.notify(data.message, {
                            //     globalPosition: 'top right',
                            //     className: 'success'
                            // });

                            toastr.success("", data.message);
                            $(btnThis).removeClass('btn-success').addClass('btn-danger');
                            $(btnThis).html(
                                '<i class="fa fa-ban" aria-hidden="true"></i> Inactive')
                            $(btnThis).attr('data-action', '0');
                            $(btnThis).data('action', '0');
                            $(btnThis).attr('title', 'Inactive');
                        } else {
                            // $.notify(data.message, {
                            //     globalPosition: 'top right',
                            //     className: 'error'
                            // });
                            toastr.error("", data.message);
                            $(btnThis).removeClass('btn-danger').addClass('btn-success');
                            $(btnThis).html(
                                '<i class="fa fa-check" aria-hidden="true"></i> Active')
                            $(btnThis).attr('data-action', '1');
                            $(btnThis).data('action', '1');
                            $(btnThis).attr('title', 'Active');
                        }
                        // $('#status' + id).val(status);
                    }
                });
            });
        });
    </script>

    <!-- Start Java Script for Date time Picker -->
    <script type="text/javascript">
        $(function() {
            $('.singledatepicker').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoUpdateInput: false,
                    // drops: "up",
                    autoApply: true,
                    locale: {
                        format: 'DD-MM-YYYY',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        firstDay: 0
                    },
                    minDate: '01/01/1930',
                },
                function(start) {
                    this.element.val(start.format('DD-MM-YYYY'));
                    this.element.parent().parent().removeClass('has-error');
                },
                function(chosen_date) {
                    this.element.val(chosen_date.format('DD-MM-YYYY'));
                });

            $('.singledatepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY'));
            });
        });
    </script>
    <!-- End Java Script for Data time Picker -->

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-body" style="padding: 0;">
                    <div id="accordion" role="tablist">
                        <div class="card" style="margin-bottom: 0px !important;">

                            <div class="card-header" role="tab" id="headingHomeLink" style="padding: 0 0;">
                                <h5 class="mb-0">
                                    <a style="display: block;padding: .75rem 1.25rem;"
                                        class="collapsed plusminuscollapse" data-toggle="collapse"
                                        href="#collapseHomeLink" aria-expanded="false" aria-controls="collapseHomeLink">
                                        Home list link<i class="fa fa-plus plusminusbutton" style="float:right"></i>
                                    </a>
                                </h5>
                            </div>
                     

                            <div class="card-header" role="tab" id="headingPostList" style="padding: 0 0;">
                                <h5 class="mb-0">
                                    <a style="display: block;padding: .75rem 1.25rem;"
                                        class="collapsed plusminuscollapse" data-toggle="collapse"
                                        href="#collapsePostList" aria-expanded="false"
                                        aria-controls="collapsePostList">
                                        Post List<i class="fa fa-plus plusminusbutton" style="float:right"></i>
                                    </a>
                                </h5>
                            </div>
                           

                            <div class="card-header" role="tab" id="headingExternalList" style="padding: 0 0;">
                                <h5 class="mb-0">
                                    <a style="display: block;padding: .75rem 1.25rem;"
                                        class="collapsed plusminuscollapse" data-toggle="collapse"
                                        href="#collapseExternalList" aria-expanded="false"
                                        aria-controls="collapseExternalList">
                                        Others List<i class="fa fa-plus plusminusbutton" style="float:right"></i>
                                    </a>
                                </h5>
                            </div>
                       
                            <li class="linkdata" data-url_type="5" data-id="#"
                                style="background: #2682f5;color: white;cursor: pointer;padding: .75rem;vertical-align: top;border-top: 1px solid #dee2e6;list-style: none;font-weight: bold;">
                                No Link</li>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(".linkdata").click(function() {
            var id = $(this).attr('data-id');
            var url_type = $(this).attr('data-url_type');
            $(".url_link").val(id);
            $(".url_link_type").val(url_type);
            $('#myModal').modal('toggle');

        });
    </script>
    <script type="text/javascript">
        $(".external_link_data").click(function() {
            var id = $('.external_link').val();
            var url_type = $('.external_link_url_type').val();
            if (validURL(id)) {
                $(".url_link").val(id);
                $(".url_link_type").val(url_type);
                $('#myModal').modal('toggle');
            } else {
                $('.notifyjs-corner').html('');
                $(function() {
                    $.notify("External Link URL is not correct", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                });
            }

        });
    </script>
    <script type="text/javascript">
        function validURL(str) {
            var pattern = new RegExp(
                '(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[A-Z0-9+&@#\/%=~_|$])',
                'i');
            return !!pattern.test(str);
        }
    </script>
    @yield('script')
  

</body>

</html>
