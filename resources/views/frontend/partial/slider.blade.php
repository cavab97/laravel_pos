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
        $('.carousel').carousel({
            interval: 3000,
            loop: true
        })
    });
</script>
<section>
    <div class="innerpage-banner-section">
        @if(isset($sideBarData) && !empty($sideBarData))
            <div class="bd-example">
                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">

                        <div class="carousel-inner">
                            @foreach($sideBarData as $key=>$value)
                                <div class="carousel-item {{($key == 0) ? "active" : ""}}">
                                    <div class="list-banneroflist">
                                        <img src="{{asset($value['banner_for_web'])}}" alt="...">
                                    </div>
                                    <div class="list-banneroflist_mobile">
                                        <img src="{{asset($value['banner_for_web'])}}" alt="...">
                                    </div>
                                    <div class="carousel-caption  d-md-block ">
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
