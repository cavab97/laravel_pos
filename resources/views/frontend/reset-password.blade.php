<!DOCTYPE html>
<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{!! csrf_token() !!}"/>
    <link rel="stylesheet" href="{{asset('frontend/css/bootstrap.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('frontend/css/style.css')}}" type="text/css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('frontend/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/css/plugin.css')}}" type="text/css">

    <link rel="stylesheet" href="{{asset('frontend/css/footer.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('frontend/css/responsive.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('frontend/css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/css/owl.carousel.min.css')}}" type="text/css">

    <link rel="stylesheet" href="{{asset('frontend/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/toastr/css/toastr.min.css')}}">
    @yield('style')
    <script>
        var baseUrl = "{{url('/')}}";
    </script>
    <title>Branch</title>
</head>

<body>
<main class="scroll-body cd-main">
    <div class="cd-main__content">
        <header>
            <nav class="custom-navbar navbar navbar-expand-lg navbar-light bg-gray">
                <div class="container">
                    <div class="custom-header align-items-center justify-content-center">
                        <div class="menu-logo-sec d-flex">
                            <button class="d-none menu-toggler border-0 navbar-toggler" type="button"
                                    data-toggle="collapse" data-target="#navbarTogglerDemo03"
                                    aria-controls="navbarTogglerDemo03" aria-expanded="false"
                                    aria-label="Toggle navigation">

                                <div class="menu-btn">
                                    <img src="{{asset('frontend/images/toggle-menu.png')}}"/>
                                </div>
                            </button>
                            <a class="header-logo navbar-brand" href="{{route('index')}}">
                                <img src="{{asset('frontend/images/logo.png')}}" alt="" class="mw-100">
                            </a>
                        </div>
                    </div>

                </div>
            </nav>
        </header>
        <section>
        <div class="category-section bg-gray">
            <div class="container">
                <div class="category-title-section">
                    <div class="category-title-sec text-center">
                        <h2 class="category-title text-white mb-0">Reset Password</h2>
                    </div>
                </div>
                <div class="product-list-section">
                    <div class="card card-dark" style="margin: 0 500px;">

                        <div class="card-body login-card-body">

                            {{ Form::model(null, ['route' => ['reset-password.update',$token], 'files' => true, 'role' => 'form', 'id'=>'frmResetPassword', 'method'=>'post']) }}

                            <div class="custom-input-box form-group">
                                <i class="input-icon fas fa-lock  prefix grey-text"></i>
                                <input type="password" name="new_password" id="new_password" class="custom-input form-control check-pass" placeholder="New Password" required>
                            </div>
                            <div class="custom-input-box form-group">
                                <i class="input-icon fas fa-lock  prefix grey-text"></i>
                                <input type="password" name="confirm_password" id="confirm_password" class="custom-input form-control check-pass" placeholder="Confirm Password" required>
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-6">
                                    <button type="submit" id="btnSubmit" data-text="Submit"
                                            data-loading-text="<span class='spinner-border spinner-border-sm'></span> Loading"
                                            class="btn btn-login btn-block">Submit
                                    </button>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
        <footer>
            <div class="footer">
                <div class="bottom-footer">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-12 d-lg-flex align-items-center justify-content-between">
                                <div class="copyright mb-lg-0 mb-3">
                                    <p>
                                        © Copyright {{date('Y')}} · All rights reserved - <a href="#"> mcnpos</a>
                                    </p>
                                </div>
                                <div class="footer-link-sec mb-lg-0 mb-3">
                                    <ul class="footer-menulist">
                                        <li class="footer-menulink">
                                            <a href="#" class="footer-link">
                                                About Us
                                            </a>
                                        </li>
                                        <li class="footer-menulink">
                                            <a href="#" class="footer-link">
                                                Plans
                                            </a>
                                        </li>
                                        <li class="footer-menulink">
                                            <a href="#" class="footer-link">
                                                Contact us
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="footer-social mb-lg-0 mb-2">
                                    <div class="social-sec">
                                        <ul>
                                            <li>
                                                <a href="#" class="social-link social-twitter">
                                                        <span class="social-icon">
                                                            <img src="{{asset('frontend/images/social-twitter.png')}}"/>
                                                        </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="social-link socail-youtube">
                                                        <span class="social-icon">
                                                            <img src="{{asset('frontend/images/social-youtube.png')}}"/>
                                                        </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="social-link social-fb">
                                                        <span class="social-icon">
                                                            <img src="{{asset('frontend/images/social-fb.png')}}"/>
                                                        </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="social-link social-linkdin">
                                                        <span class="social-icon">
                                                            <img src="{{asset('frontend/images/social-lin.png')}}"/>
                                                        </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</main>
<script src="{{asset('frontend/js/jquery.min.js')}}"></script>
<script src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
<script src="{{asset('frontend/js/plugin.js')}}"></script>
<script src="{{asset('frontend/js/custom.js')}}"></script>
<script src="{{asset('frontend/js/owl.carousel.min.js')}}"></script>
<script src="{{asset('backend/plugins/toastr/js/toastr.min.js')}}"></script>
<script src="{{asset('frontend/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('frontend/js/pages/reset-password.js')}}"></script>
</body>
</html>



