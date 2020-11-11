@extends('frontend.layouts.home')
@section('content')
    <section>
        <div class="category-section bg-gray">
            <div class="container">
                <div class="category-title-section">
                    <a href="{{route('home')}}" class="clr-red category-name-sec">
                        <span><i class="fas fa-arrow-left"></i></span>
                        <span class="category-name">Back</span>
                    </a>
                    <div class="category-title-sec text-center">
                        <h2 class="category-title text-white mb-0">Scan QR</h2>
                    </div>
                </div>
                <div class="product-list-section">
                    <div class="row">
                        <div class="scan-qr">
                            {!! \SimpleSoftwareIO\QrCode\Facade::size(400)->
                            backgroundColor(255, 255, 128)
                                    ->generate($link); !!}

                        </div>
                        <input type="hidden" value="{{$link}}">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
