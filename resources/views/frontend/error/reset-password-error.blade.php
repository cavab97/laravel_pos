@extends('frontend.layouts.home')
@section('content')
    <section>
        <div class="category-section bg-gray">
            <div class="container">
<div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12" >

                    <div class="logo text-center CustomLogs"></div>
                    <div class="ErrorBoxFour text-center" style="margin-top: 0%">
                        <div class="accessErrorTitle" style="font-size: 150px;color: #FFF;font-weight: bold;font-family: cursive;text-shadow: 1px 1px 6px #efefef;letter-spacing: 12px;">4<span style="color:#ff5a01;">0</span>4</div>


                        @if($Timeup && $Timeup == 1)
                            <div class="accessErrorDesc" style="text-align: center;color: #FFF;">Link has been Expired already. Please try again...!</div>
                        @else
                            <div class="accessErrorDesc" style="text-align: center;color: #FFF;">Oops...!</div>
                            <div class="accessErrorDesctwo" style="text-align: center;color: #FFF;">You requested the page that is no longer there.</div>
                        @endif
                        <div class="col-md-12 mt-2">
                        <a href="{{route('home')}}" class="btn btn-login">Back To Home</a>
                        </div>
                    </div>

                </div>
</div>
                <div class="stars"></div>
                <div class="twinkling"></div>
            </div>
        </div>
    </section>
@endsection
