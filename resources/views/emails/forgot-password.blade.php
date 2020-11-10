@extends('emails.layout')

@section('content')
    <tr>
        <td style="background: #f1f1f1; text-align: center; font-size: 34px; font-weight: 700; font-family:gotham-medium;color:#53457a;padding: 60px 0 50px;">
            Reset My Password
        </td>
    </tr>
    <tr>
        <td style="background: #f1f1f1;padding: 0 50px;font-size: 18px;font-family: gotham-light; font-weight: 400;line-height: 22px;">
            <p>Hello {{$data['name']}},</p>
            <p>You are receiving this email because we received a password reset request for your account.</p>
            <p style="text-align:center;margin: 20px 0;display: inline-block;width: 100%;">
                <a id="resetPassword" class="font18"
                   style="background: #000000;color: #ffffff;padding: 10px 40px;border-radius: 40px;font-size: 24px;font-family: gotham-medium;text-decoration: none;"
                   href="{{$data['url']}}">Reset my password</a>
            </p>
            <p>If you did not request a password reset, no further action is required.</p>
        </td>
    </tr>
@endsection
