@extends('frontend.layouts.front')

@section('scripts')
    <script src="{{asset('frontend/js/pages/product.js')}}"></script>
@endsection

@section('content')
    <div class="se-pre-con"></div>
    <section>
        <div class="category-section bg-gray">
            <div class="container">
                <div class="category-title-section">
                    <a href="{{url('/category/' . $branchSlug)}}" class="clr-red category-name-sec">
                        <span><i class="fas fa-arrow-left"></i></span>
                        <span class="category-name">Category </span>
                    </a>
                    <div class="category-title-sec text-center">
                        <h4 class="category-title text-white mb-0 clr-red">{{$branchName}}</h4>
                        <h2 class="category-title text-white mb-0">OUR PRODUCTS</h2>
                    </div>
                </div>
                <div id="myPostData">
                    <div class="product-list-section">
                        <div class="row">
                            @if(isset($productList) && count($productList) > 0)
                                @foreach($productList as $key=>$value)
                                    <div class="product-list-col col">
                                        @if($value['is_addon'])
                                            <a href="javascript:void(0);"
                                               onclick="openProductPopup(this,'{{$value["uuid"]}}','{{$branchSlug}}','{{$value['is_out_of_stock']}}')"
                                               class="product-list-box">
                                                <div class="product-img-box @if($value['is_out_of_stock'] == 'true') out-product-img-box @endif" data-caption="Out Of Stock">
                                                    @if(!empty($value['product_image']) && file_exists(public_path($value['product_image'])))
                                                    <img class="product-img" src="{{asset($value['product_image'])}}"/>
                                                        @else
                                                        <img class="product-img" src="{{asset(config('constants.default_product'))}}"/>
                                                    @endif
                                                </div>
                                                <div class="product-desc-sec">
                                                    @if($value['is_out_of_stock'] == 'false')
                                                        <button href="#" class="product-cart">
                                                        <span>
                                                            <img src="{{asset('frontend/images/cart.png')}}">
                                                        </span>
                                                        </button>
                                                    @endif
                                                    <h3 class="clr-red product-price">{{config('constants.currency').' '.$value['price']}}</h3>
                                                    <p class="product-name text-white text-center">{{ucfirst($value['name'])}}</p>
                                                </div>
                                            </a>
                                        @else
                                            <a href="javascript:void(0);" onclick="addToCartProd(this,'{{$value['is_out_of_stock']}}')" id="addToCart" data-id="{{$key}}"
                                               class="product-list-box">
                                                <div class="product-img-box @if($value['is_out_of_stock'] == 'true') out-product-img-box @endif" data-caption="Out Of Stock">
                                                    @if(!empty($value['product_image']) && file_exists(public_path($value['product_image'])))
                                                        <img class="product-img" src="{{asset($value['product_image'])}}"/>
                                                    @else
                                                        <img class="product-img" src="{{asset(config('constants.default_product'))}}"/>
                                                    @endif
                                                </div>
                                                <div class="product-desc-sec">
                                                    @if($value['is_out_of_stock'] == 'false')
                                                    <button href="#" class="product-cart">
                                                    <span>
                                                        <img src="{{asset('frontend/images/cart.png')}}">
                                                    </span>
                                                    </button>
                                                    @endif
                                                    <input type="hidden" name="quantity" id="sim_quantity{{$key}}" value="1">
                                                    <h3 class="clr-red product-price">{{config('constants.currency').' '.$value['price']}}</h3>
                                                    <p class="product-name text-white text-center">{{ucfirst($value['name'])}}</p>
                                                </div>
                                            </a>
                                            <input type="hidden" name="prod_price" id="sim_prod_price{{$key}}" data-id="{{$value->price}}">
                                            <input type="hidden" name="branch_slug" id="sim_branch_slug{{$key}}" data-id="{{$branchSlug}}">
                                            <input type="hidden" name="product_id" id="sim_product_id{{$key}}" data-id="{{$value->product_id}}">
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12 text-center text-white">
                                    <h3>No data available</h3>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="pagination-section">
                        <div>
                            <nav aria-label="Page navigation example">
                                <ul class="custom-pagination pagination justify-content-center">
                                    {{$productList->links()}}
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
