<div class="product-list-section">
    <div class="row">
        @if(isset($productList) && count($productList) > 0)
            @foreach($productList as $key=>$value)
                <div class="product-list-col col">
                    <a href="javascript:void(0);"
                       onclick="openSetmealPopup(this,'{{$value["uuid"]}}','{{$branchSlug}}')"
                       class="product-list-box">
                        <div class="product-img-box">
                            <img class="product-img" src="{{asset($value['product_image'])}}"/>
                        </div>
                        <div class="product-desc-sec">
                            <button href="#" class="product-cart">
                                                <span>
                                                    <img src="{{asset('frontend/images/cart.png')}}">
                                                </span>
                            </button>
                            <h3 class="clr-red product-price">{{config('constants.currency').' '.$value['price']}}</h3>
                            <p class="product-name text-white text-center">{{ucfirst($value['name'])}}</p>
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
<div class="pagination-section">
    <div>
        <nav aria-label="Page navigation example">
            <ul class="custom-pagination pagination justify-content-center">
                {{$productList->links()}}
            </ul>
        </nav>
    </div>
</div>



