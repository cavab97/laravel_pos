@extends('frontend.layouts.home')
@section('scripts')
    <script src="{{asset('frontend/js/pages/home.js')}}"></script>
@endsection
@section('content')
    <section>
        <div class="category-section bg-gray">
            <div class="container">
                <div class="category-title-section">
                    <div class="category-title-sec text-center d-block w-100">
                        <h2 class="category-title text-white mb-0">OUR Branch</h2>
                    </div>
                </div>
                <div class="branch-page branch-sec newsroom">
                    <div class="brach-categories row">
                        @if(isset($branchList) && !empty($branchList))
                            @foreach($branchList as $key=>$value)
                                <div class="item col-xl-2 col-md-3 col-6 col-sm-4">

                                        {{--<a href="{{url('/category/' . $value['slug'])}}">
                                            <div class="m-item bz">
                                                <img src="{{asset($value['branch_banner'])}}" alt="">
                                                <span>{{$value['name']}}</span>
                                            </div>
                                        </a>--}}
                                    <a href="javascript:void(0);" onclick="checkCart('{{$value['slug']}}');">
                                        <div class="m-item bz">
                                            <img src="{{asset($value['branch_banner'])}}" alt="">
                                            <span>{{$value['name']}}</span>
                                        </div>
                                    </a>

                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
