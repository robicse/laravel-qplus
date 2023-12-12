<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title') | </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    {{-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> --}}
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('backend/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- select2 -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('backend/dist/css/adminlte.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/summernote/summernote-bs4.css') }}">
    <!-- Google Font: Source Sans Pro -->
    {{-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    @stack('css')
    <style>
        .preloader {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-image: url('{{ asset('backend/preloader/animation-10.gif') }}');
            background-repeat: no-repeat;
            background-color: #FFF;
            background-position: center;
        }

        /* body{
            font-family: 'Camber'  !important;
        } */

        ul.nav.nav-treeview {
            padding-left: 15px;
        }

        .navbar-success {
            /* background: rgb(230,34,56); */
            background: linear-gradient(90deg, rgba(6, 57, 88) 0%, rgba(55, 112, 164, 1) 38%, rgba(230, 34, 56) 100%);
        }

        .navbar-light .navbar-nav .nav-link {
            color: #fff !important;
        }

        aside.main-sidebar.sidebar-dark-primary.elevation-4 {
            background-color: #063958 !important;
        }

        .nav-sidebar .nav-item>.nav-link {
            color: #fff !important;
        }

        .nav-sidebar .nav-link p {
            color: #fff !important;
        }

        .nav-sidebar .nav-treeview>.nav-item>.nav-link>.nav-icon {
            color: #5298D9 !important;
        }

        .sidebar a {
            color: #fff !important;
        }

        .brand-link {
            color: #fff !important;
        }

        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
        .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #5298D9;
            color: #fff;
        }

        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active,
        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:focus,
        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:hover {
            background-color: #5298D9;
            color: #343a40;
        }

        .customcontent{
            font-size: 12px !important;
            background-color: rgba(0,0,0,.03);
        }
        .customcontent .select2 input{
            font-size: 12px !important;
        }
   #itemlist td {
    padding: 0.5rem  !important;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed " style="font-weight: bold">
    <div class="wrapper">

        <!-- top navigation -->
        @include('backend.includes.header')
        <!-- /top navigation -->

        @include('backend.includes.sidebar')


        <!-- /sidebar menu -->

        <!-- page content -->
        <div class="content-wrapper">
            <div class="preloader"></div>
            @yield('content')
        </div>
        <!-- /page content -->

        <!-- footer content -->
        {{-- @include('backend.includes.footer') --}}
        <!-- /footer content -->


        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark ">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('backend/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('backend/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('backend/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('backend/plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('backend/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('backend/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('backend/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('backend/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- calx -->
    @yield('calx')

    <!-- select2 -->
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('backend/dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    {{-- <script src="{{asset('backend/dist/js/pages/dashboard.js')}}"></script> --}}
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('backend/dist/js/demo.js') }}"></script>
    <script src="{{ asset('backend/keyboard/js/keyboard.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    {!! @Toastr::message() !!}
    @stack('js')

    <script type="text/javascript">
        $(".preloader").fadeOut("slow");
        var url = "{{ URL::to('/') }}";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {

            $("#seennotify").hover(function() {
                $.ajax({
                    type: "get",
                    url: url + '/seennotification'
                });
            });
            $("#clearall").click(function() {
                $.ajax({
                    type: "tget",
                    url: url + '/deletenotification',
                });
            });
        });
    </script>

    <script type="text/javascript">
        document.onkeyup = function keybordshortcut(event) {
            var APP_URL = "{{ URL(Request::segment(1) . '/') }}"
            if (event.altKey && event.code === 'KeyS') {
                window.location.href = APP_URL + '/' + 'suppliers/create';
            } else if (event.altKey && event.code === 'KeyC') {
                window.location.href = APP_URL + '/' + 'customers/create';
            } else if (event.altKey && event.code === 'KeyP') {
                window.location.href = APP_URL + '/' + 'purchases/create';
            } else if (event.altKey && event.code === 'KeyV') {
                window.location.href = APP_URL + '/' + 'sales/create';
            }
        };
    </script>
</body>

</html>
