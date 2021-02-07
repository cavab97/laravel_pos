<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{config('app.name')}}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{asset('backend/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <link rel="stylesheet"
          href="{{asset('backend/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/dist/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('backend/dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link rel="stylesheet" type="text/plain" href="{{asset('backend/dist/css/_loader.scss')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/toastr/css/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/dist/css/bootstrap-datetimepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/select2/css/select2.min.css')}}">
    <link rel="apple-touch-icon icon" sizes="57x57" href="{{asset('backend/dist/img/favicon/favicon.ico')}}">
    <link rel="apple-touch-icon icon" sizes="16x!6" href="{{asset('backend/dist/img/favicon/favicon-16x16.png')}}">
    <link rel="apple-touch-icon icon" sizes="32x32" href="{{asset('backend/dist/img/favicon/apple-icon-32x32.png')}}">

    @yield('styles')
    <script>
        var adminUrl = '{{url('/'.config('constants.admin'))}}';
        var baseUrl = '<?php echo e(url('/')); ?>';
    </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div id="preloader">
    <div id="prestatus">
        <div class="spinner"></div>
    </div>
</div>
<div class="wrapper">
    @include('backend.partials.header')

    @include('backend.partials.sidebar')

    @yield('content')
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog"></div>

<script src="{{asset('backend/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('backend/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="https://use.fontawesome.com/298cad8fec.js"></script>
<script src="{{asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('backend/plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<!-- <script defer src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script> -->
<script src="{{asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{asset('backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<script src="{{asset('backend/plugins/toastr/js/toastr.min.js')}}"></script>
<script src="{{asset('backend/dist/js/adminlte.js')}}"></script>
<script src="{{asset('backend/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('backend/dist/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('backend/plugins/ckeditor/ckeditor.js')}}"></script>
<script>
    var message = localStorage.getItem('message');
    localStorage.removeItem('message');
    if (message) {
        toastr.success(message);
    }
    showHideLoader('hide');

    function showHideLoader(type) {
        if (type == 'hide') {
            $('#prestatus').fadeOut();
            $('#preloader').delay(100).fadeOut('slow');
            $('body').delay(100).css({
                'overflow': 'visible'
            });
        } else {
            $('#prestatus').fadeIn();
            $('#preloader').delay(100).fadeIn('slow');
            $('body').delay(100).css({
                'overflow': 'visible'
            });
        }
    }
</script>

@yield('scripts')
</body>
</html>
