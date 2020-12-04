@extends('frontend.layouts.front')
@section('content')
    <section>
        <div class="category-section bg-gray">
            <div class="container">
                <div class="category-title-section">
                    <a href="{{url('/')}}" class="clr-red category-name-sec">
                        <span><i class="fas fa-arrow-left"></i></span>
                        <span class="category-name">Home </span>
                    </a>
                    <div class="category-title-sec text-center">
                        <h4 class="category-title text-white mb-0 clr-red">{{$branchName}}</h4>
                        <h2 class="category-title text-white mb-0">OUR Category</h2>
                    </div>
                </div>
                <div class="branch-page branch-sec newsroom">
                    <div class="brach-categories row">

                        @if(isset($categoryList) && count($categoryList) > 0)
                            @foreach($categoryList as $key=>$value)
                                <?php $category = $value['slug'];?>
                                <div class="item col-xl-2 col-md-3 col-6 col-sm-4">
                                    <a href="{{route('product',[$branchSlug,$category])}}">
                                        <div class="m-item bz">
                                            <img src="{{asset($value['category_icon'])}}" alt="">
                                            <span>{{$value['name']}}</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-12 text-center text-white">
                                <h3>No data available</h3>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
