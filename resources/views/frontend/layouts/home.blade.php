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
    <link rel="apple-touch-icon icon" sizes="57x57" href="{{asset('frontend/images/favicon/favicon.ico')}}">
    <link rel="apple-touch-icon icon" sizes="16x!6" href="{{asset('frontend/images/favicon/favicon-16x16.png')}}">
    <link rel="apple-touch-icon icon" sizes="32x32" href="{{asset('frontend/images/favicon/favicon-32x32.png')}}">
    @yield('style')
    <script>
        var baseUrl = "{{url('/')}}";
    </script>
    <title>MCN</title>
</head>

<body>
<main class="scroll-body cd-main">
    <div class="cd-main__content">

        @include('frontend.partial.header')
        @yield('content')
        @include('frontend.partial.footer')
    </div>
</main>

<!--Javascripts-->
<div class="custom-modal modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="pop"
     aria-hidden="true"></div>
<script src="{{asset('frontend/js/jquery.min.js')}}"></script>
<script src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
<script src="{{asset('frontend/js/plugin.js')}}"></script>
<script src="{{asset('frontend/js/custom.js')}}"></script>
<script src="{{asset('frontend/js/owl.carousel.min.js')}}"></script>
<script src="{{asset('backend/plugins/toastr/js/toastr.min.js')}}"></script>
@include('frontend.partial.toastr')
<script>
    var message = localStorage.getItem('message');
    localStorage.removeItem('message');
    if (message) {
        toastr.success(message);
    }
</script>
<script>
    const $menuBtn = document.querySelector(".menu-toggler");
    let isMenuOpen = false;
    $menuBtn.addEventListener("click", () => {
        if (!isMenuOpen) {
            $menuBtn.classList.add("open");
        } else {
            $menuBtn.classList.remove("open");
        }

        isMenuOpen = !isMenuOpen;
    });

    function openSearch() {
        document.getElementById("myOverlay").style.display = "block";
    }

    function closeSearch() {
        document.getElementById("myOverlay").style.display = "none";
    }

    $(document).ready(function () {
        $('.menu-toggler').click(function () {
            $('.menu-listing').slideToggle("slow");
        });
    });
</script>
@yield('scripts')
</body>
</html>
