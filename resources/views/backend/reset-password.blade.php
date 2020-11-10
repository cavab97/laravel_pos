<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{env('APP_NAME')}} | Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('backend/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('backend/dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/dist/css/custom.css')}}">
    <script>
        var adminUrl = '{{url('/'.config('constants.admin'))}}';
    </script>
    <link rel="icon" href="{{asset('backend/dist/img/favicon.png')}}" type="image/png" sizes="64x64"/>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="{{asset('backend/plugins/toastr/css/toastr.min.css')}}">
    <link rel="apple-touch-icon icon" sizes="57x57" href="{{asset('backend/dist/img/favicon/favicon.ico')}}">
    <link rel="apple-touch-icon icon" sizes="16x!6" href="{{asset('backend/dist/img/favicon/favicon-16x16.png')}}">
    <link rel="apple-touch-icon icon" sizes="32x32" href="{{asset('backend/dist/img/favicon/apple-icon-32x32.png')}}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="{{url(config('constants.admin').'/')}}"><img class="img-fluid"
                                                              src="{{asset('backend/dist/img/logo.png')}}" width="200px"/></a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Reset Password</p>

            {{ Form::model(null, ['route' => ['admin.reset-password.post'], 'files' => true, 'role' => 'form', 'id'=>'frmResetPassword', 'method'=>'post']) }}
            {!! csrf_field() !!}
            <input type="hidden" name="token" value="{{$token}}" id="token">
            <div class="input-group mb-3" id="input_password">
                <input type="password" class="check-pass form-control" placeholder="*********"
                       name="password" id="password" required/>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3" id="input_confirm_password">
                <input type="password" class="check-crm-pass form-control" placeholder="*********"
                       name="confirm_password" id="confirm_password" required/>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-4">

                </div>
                <div class="col-8">
                    <button type="submit" class="btn btn-info btn-sm" id="btnSubmit"
                            data-loading-text="<i class='fa fa-spinner fa-spin'></i> loading" data-original-text="Reset Password">Reset Password
                    </button>
                </div>
                <!-- /.col -->
            </div>
            {{ Form::close() }}
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('backend/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('backend/dist/js/adminlte.min.js')}}"></script>
<script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/plugins/toastr/js/toastr.min.js')}}"></script>
<script src="{{asset('backend/dist/js/pages/forgot-password.js')}}"></script>
</body>
</html>
