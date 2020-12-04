<?php
    if(isset($branchSlug)){
        $sideBarData = \App\Models\Helper::bannerData($branchSlug);
    }
?>
<style>
    .carousel-inner{
        overflow: visible;
    }
</style>
<script>
    $(document).ready(function () {
        $('.carousel').carousel()

    });
</script>
<section>
    <div class="innerpage-banner-section">
        @if(isset($sideBarData) && !empty($sideBarData))
            {{--@foreach($sideBarData as $key=>$value)
                <div class="list-banneroflist">
                    <img src="{{asset($value['banner_for_web'])}}">
                    <div class="container">
                        <div class="list-bannerofcontent">
                            <h1 class="banner-title">{{$value['title']}}</h1>
                            <p class="banner-title-desc">{!!$value['description']  !!}</p>
                        </div>
                    </div>
                </div>
                <div class="list-banneroflist_mobile">
                    <img src="{{asset($value['banner_for_mobile'])}}">
                    <div class="container">
                        <div class="list-bannerofcontent">
                            <h1 class="banner-title">{{$value['title']}}</h1>
                            <p class="banner-title-desc">{!!$value['description']  !!}</p>
                        </div>
                    </div>
                </div>
            @endforeach--}}

            <div class="bd-example">
                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                    {{--<ol class="carousel-indicators">
                        @foreach($sideBarData as $key=>$value)
                            <li data-target="#carouselExampleCaptions" data-slide-to="{{$key}}" class="{{($key == 0) ? "active" : ""}}"></li>
                        @endforeach
                    </ol>--}}
                    <div class="carousel-inner">
                        @foreach($sideBarData as $key=>$value)
                            <div class="carousel-item list-banneroflist {{($key == 0) ? "active" : ""}}">
                                <img src="{{asset($value['banner_for_web'])}}" alt="...">
                                <div class="carousel-caption d-none d-md-block ">
                                    <h5 class="banner-title">{{$value['title']}}</h5>
                                    <p class="banner-title-desc">{!!$value['description']  !!}</p>
                                </div>
                            </div>
                            <div class="carousel-item list-banneroflist_mobile {{($key == 0) ? "active" : ""}}">
                                <img src="{{asset($value['banner_for_web'])}}" alt="...">
                                <div class="carousel-caption d-none d-md-block ">
                                    <h5 class="banner-title">{{$value['title']}}</h5>
                                    <p class="banner-title-desc">{!!$value['description']  !!}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>

        @endif
    </div>
</section>
