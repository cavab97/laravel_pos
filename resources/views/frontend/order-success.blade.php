@extends('frontend.layouts.home')
@section('content')
    <section>
        <div class="category-section bg-gray web-order-success-page">
            <div class="container">
                <div class="category-title-section">
                    <a href="{{route('home')}}" class="clr-red category-name-sec">
                        <span><i class="fas fa-arrow-left"></i></span>
                        <span class="category-name">Branch </span>
                    </a>
                </div>
                <div class="web-order-success-section">
                    <div class="row align-items-center justify-content-center text-center">
                        <div class="col-md-8">
                            <div class="web-order-success">
                                <div class="check-circle-icon">
                                    <span><i class="far fa-check-circle"></i></span>
                                </div>
                                <h2 class="order-success-title">
                                    Thank You For Your Order
                                </h2>
                                <p class="web-order-name-number">
                                    Your order number is:
                                    <span class="web-order-number">{{$order_number}}</span>
                                </p>
                                <p class="web-order-desc">
                                    We'll email you an order confirmation with details and tracking info
                                </p>
                                <div class="web-order-continue">
                                    <a href="{{route('home')}}" class="btn-web-order-continue btn">
                                                <span>
                                                    <img src="{{asset('frontend/images/cart.png')}}" class="mw-100" style="height: 20px;">
                                                </span>
                                        Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
