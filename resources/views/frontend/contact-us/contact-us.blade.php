@extends('frontend.layouts.home')
@section('scripts')
    <script src="{{asset('frontend/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('frontend/js/pages/contact-us.js')}}"></script>
@endsection
@section('content')
    <section>
        <div class="category-section bg-gray about-us-content pt-0">
            <div class="innerpage-banner-section">
                <div class="list-banneroflist">
                    <img src="{{asset('frontend/images/banner-category.png')}}">
                    <div class="container">
                        <div class="list-bannerofcontent">
                            <h1 class="banner-title">Contact US</h1>
                            <p class="banner-title-desc">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. has been the industry's standard dummy text ever since the 1500s,
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="contact-us-content my-5">
                    {{--<div class="text-center">--}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="banner-contact-left">
                                   <h6 class="contact-title">Get In Touch</h6>
                                   <p class="contact-desc">
                                       Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book
                                   </p>
                                   <a href="#" class="address-detail"> 
                                    <span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <span>Jalan Nilam 1/3, Taman Perindustrian Technologi Tinggi, Malaysia</span>
                                   </a>
                                   <a href="tel:03-5636-6606" class="contact-call">
                                    <span class="contact-icon"><i class="fas fa-phone-alt"></i></span>
                                       <span>03-5636-6606</span>
                                   </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contact-form pt-md-0 pt-4">
                                    {{ Form::model(null, ['route' => ['contactUs.post'], 'files' => true, 'role' => 'form', 'id'=>'frmContactUs', 'method'=>'post']) }}
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control form-group" placeholder="Name" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control  form-group" placeholder="Email" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="subject" class="form-control  form-group" placeholder="Subject" required>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control form-group" rows="4" placeholder="Message" required></textarea>
                                    </div>
                                        <button type="submit" id="btnSubmit" class="btn btn-contact btn-block btn-lg" data-original-text="Send" data-loading-text="<span class='spinner-border spinner-border-sm'></span> Loading">Send</button>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    {{--</div>--}}
                </div>
            </div>
        </div>
    </section>
@endsection
