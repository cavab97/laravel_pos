<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <style id="__web-inspector-hide-shortcut-style__" type="text/css">
        body {
            font-family: gotham-medium;
        }

        @media only screen and (max-width: 600px) {
            .font18 {
                font-size: 18px !important;
            }

            #copyright {
                width: 100% !important;
                float: left;
            }
        }
    </style>
</head>
<body>
<table width="100%" style="margin: 100px auto; background: #f1f1f1;max-width:800px;" border="0" cellpadding="0"
       cellspacing="0">
    <tbody>
    <tr>
        <td style="height: 7px;background: #36baef;line-height: 7px;float: left; width: 100%;padding: 0;border-top: 1px #ffffff solid;">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td style="text-align: center; padding: 50px 0; background: #000000;">
            <a href="#">
                <img class="img-responsive" src="{{asset('backend/dist/img/logo.png')}}" width="200px;"/>
            </a>
        </td>
    </tr>
    @yield('content')
    <tr>
        <td style="background: #f1f1f1;padding: 0 50px;font-size: 18px;font-family: gotham-medium;line-height: 26px; padding-bottom: 30px;"></td>
    </tr>
    <tr>
        <td style="background: #f1f1f1;padding: 0 50px;font-size: 18px;font-family: gotham-medium;line-height: 26px; padding-bottom: 30px;">
            Regards,<br>{{env('APP_NAME')}} Support Team
        </td>
    </tr>
    <tr>
        {{--<td class="font18"
            style="background: #f1f1f1; padding:15px 15px; text-align: center; font-size: 12px; font-family: gotham-medium;color:#666666;">
            <span id="copyright">Copyright © All Rights Reserved. &nbsp;&nbsp;</span>
            <a class="font18"
               style="font-size: 12px; font-family: gotham-medium;color:#000; text-decoration: none;color:#666666;"
               href="{{route('terms-conditions')}}">Terms & Conditions</a> |
            <a class="font18"
               style="font-size: 12px; font-family: gotham-medium;color:#000; text-decoration: none;color:#666666;"
               href="{{route('privacy-policy')}}">Privacy Policy</a>
        </td>--}}
    </tr>

    <tr>
        {{--<td style="background: #f1f1f1; text-align: center;padding: 0 0 20px 0;">
            <a href="{{config('constants.facebook_url')}}" style="display: inline-block;">
                <img src="{{asset('frontend/img/facebook.png')}}"/>
            </a>
            <a href="{{config('constants.twitter_url')}}" style="display: inline-block;">
                <img src="{{asset('frontend/img/twitter.png')}}"/>
            </a>
            <a href="{{config('constants.instagram_url')}}" style="display: inline-block;">
                <img src="{{asset('frontend/img/instagram.png')}}"/>
            </a>
        </td>--}}
    </tr>
    </tbody>
</table>
</body>
</html>
