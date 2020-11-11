@extends('frontend.layouts.home')
@section('content')
    <section>
        <div class="category-section bg-gray about-us-content pt-0">
            <div class="innerpage-banner-section">
                <div class="list-banneroflist">
                    <img src="{{asset('frontend/images/banner-category.png')}}">
                    <div class="container">
                        <div class="list-bannerofcontent">
                            <h1 class="banner-title">About US</h1>
                            <p class="banner-title-desc">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. has been the industry's standard dummy text ever since the 1500s,
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="about-us-content my-5 about">
                    <div class="row text-center">
                        <div class="col-lg-4 col-sm-6">
                            <a href="#" class="about-item">
                                <span class="fas fa-hamburger"></span>
                                <h2>Service 1</h2>
                                <p class="lead">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled</p>
                            </a>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <a href="#" class="about-item">
                                <span class="fas fa-ice-cream"></span>
                                <h2>Service 2 </h2>
                                <p class="lead">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum </p>
                            </a>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <a href="#" class="about-item">
                                <span class="fas fa-pizza-slice"></span>
                                <h2>Service 3</h2>
                                <p class="lead">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled</p>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
