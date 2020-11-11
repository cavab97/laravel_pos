@extends('frontend.layouts.front')
@section('content')
    <section>
        <div class="category-section bg-gray">
            <div class="container">
                <div class="category-title-section">
                    <div class="category-title-sec text-center">
                        <h2 class="category-title text-white mb-0">Order History</h2>
                    </div>
                </div>
                <div class="product-table-section pb-3 pt-0">
                    @if(isset($orderData) && count($orderData) > 0)
                        @foreach($orderData as $key => $value)
                    <div class="row border-bottom-orange py-2">
                        <div class="col-6 pl-0 text-left">
                            <div class="product-left-data">
                                <p class="prodcut-name"><strong>#{{$value->invoice_no}}</strong></p>
                                <p class="total-product">Total Items: {{$value->order_item_count}}</p>
                            </div>
                        </div>
                        <div class="col-6 pr-0 text-right">
                            <div class="product-right-data">
                                <div>
                                    <p class="product-new"><span class="badge badge-info">NEW</span></p>
                                    <p class="product-new-total">MYR <label class="mb-0">{{$value->grand_total}}</label></p>
                                </div>
                                <div class="right-arrow">
                                    <a href="#"><i class="fa fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                        @endforeach
                    @else
                        <div class="col-md-12 text-center p-3">
                            <p>No Orders Found!</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
@endsection
