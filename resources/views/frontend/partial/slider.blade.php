<?php
    if(isset($branchSlug)){
        $sideBarData = \App\Models\Helper::bannerData($branchSlug);
    }
?>
<section>
    <div class="innerpage-banner-section">
        @if(isset($sideBarData) && !empty($sideBarData))
            @foreach($sideBarData as $key=>$value)
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
            @endforeach
        @endif
    </div>
</section>
