<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MCN</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
		<link rel="apple-touch-icon icon" sizes="57x57" href="{{asset('backend/dist/img/favicon/favicon.ico')}}">
		<link rel="apple-touch-icon icon" sizes="16x!6" href="{{asset('backend/dist/img/favicon/favicon-16x16.png')}}">
		<link rel="apple-touch-icon icon" sizes="32x32" href="{{asset('backend/dist/img/favicon/apple-icon-32x32.png')}}">
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body style="background-color: #434449;">
        <div class="flex-center position-ref full-height">
            <img width="300px"  src="{{asset('backend/dist/img/logo.png')}}" alt="">
        </div>
    </body>
</html>
