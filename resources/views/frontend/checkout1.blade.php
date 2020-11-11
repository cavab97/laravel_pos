@extends('frontend.layouts.front')
@php
    $userData = \Illuminate\Support\Facades\Auth::guard('fronts')->user();
@endphp
@section('scripts')
    <script src="{{asset('frontend/js/pages/checkout.js')}}"></script>
@endsection

@section('content')
    <section>
        <div class="category-section bg-gray">
            <div class="container">
                <div class="category-title-section">
                    <a href="{{url('/category/' . $branchSlug)}}" class="clr-red category-name-sec">
                        <span><i class="fas fa-arrow-left"></i></span>
                        <span class="category-name">Category </span>
                    </a>
                    <div class="category-title-sec text-center">
                        <h2 class="category-title text-white mb-0">CHECKOUT</h2>
                    </div>
                </div>
                <div class="product-list-section">
                    <div class="row" id="checkoutTable">

                        <table class="table table-hover table-light">
                            <thead>
                            <tr>

                                @if($userData)
                                    <td><input type="email" class="form-control form-control-sm" name="cust_email"
                                               id="cust_email" placeholder="Enter email" value="{{$userData->email}}">
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm" name="cust_mobile"
                                               id="cust_mobile" value="{{$userData->mobile}}"
                                               placeholder="Enter mobile" onkeypress="return onlyNumberKey(event)"
                                               maxlength="10" required></td>
                                @else
                                    <td><input type="email" class="form-control form-control-sm" name="cust_email"
                                               id="cust_email" placeholder="Enter email">
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm" name="cust_mobile"
                                               id="cust_mobile"
                                               placeholder="Enter mobile" onkeypress="return onlyNumberKey(event)"
                                               maxlength="10" required></td>
                                @endif

                            </tr>

                            <tr>
                                <th scope="col">#</th>
                                {{--<th scope="col">Product Image</th>--}}
                                <th scope="col">Product Name</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Addon Items</th>
                                <th scope="col">Price</th>
                                <th scope="col">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($cartData) && count($cartData) > 0)
                                @php $attributePrice = 0; $modifierPrice = 0; @endphp
                                @foreach($cartData as $key=>$value)
                                    <tr class="productcart">
                                        <th scope="row">{{$key + 1}}</th>
                                        <td>{{$value->product_name}}</td>
                                        <td>
                                            <div class="quantity buttons_added">
                                                <input type="number" step="1" min="1" name="quantity[{{$key}}]"
                                                       max="999" onKeyPress="if(this.value.length==3) return false;"
                                                       id="quantity{{$key}}" value="{{$value->product_qty}}"
                                                       class="input-text qty text">
                                                <button type="button" class="btn btn-sm btn-warning ml-1"
                                                        onclick="updateCart('{{$value['cart_id']}}','{{$value['cart_detail_id']}}','{{$value['product_id']}}','{{$key}}','{{$branchSlug}}');">
                                                    <i class="fa fa-refresh"></i></button>
                                                <button type="button" class="btn btn-sm btn-danger ml-1"
                                                        onclick="removeCartItemConfirm('{{$value['cart_detail_id']}}','{{$branchSlug}}');">
                                                    <i
                                                            class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                        <td>
                                            @if(!empty($value->cart_addon))
                                                {{$value->cart_addon}}
                                            @endif
                                        </td>
                                        <td>{{number_format($value->product_price,2)}}</td>
                                        <td>{{number_format($value->product_total,2)}} </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>

                                    <th>Sub-Total</th>
                                    <td>MYR <span id="sub_total">{{number_format($value->sub_total,2)}}</span></td>
                                </tr>
                                <tr id="discount_tag" class="@if($discountTotal == 0) display-none @endif">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>

                                    <th>Discount</th>
                                    <td>MYR <span id="discount_value">{{number_format($discountTotal,2)}}</span></td>
                                    <input type="hidden" data-value="{{$discountTotal}}" id="voucher_amount">
                                </tr>

                                @php $taxValue = 0 ;@endphp
                                @if(isset($branchTax) && !empty($branchTax))
                                    @foreach($branchTax as $bvalue)
                                        @php
                                            $tax = ($value->sub_total-$discountTotal) * $bvalue->rate / 100;
                                            $taxValue += $tax;
                                        @endphp
                                        <tr id="tax">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>

                                            <th>Tax({{$bvalue->code}} {{$bvalue->rate}}%)</th>
                                            <td>MYR {{$tax}}</td>
                                        </tr>

                                    @endforeach
                                @endif

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td align="right">

                                        <input type="text" name="voucher_code" id="voucher_code"
                                               placeholder="Enter voucher code" class="">
                                        <button type="button" name="apply" class="btn btn-sm btn-success"
                                                data-id="{{$branchSlug}}" onclick="checkVoucher(this)">Apply
                                        </button>
                                        <input type="hidden" id="voucher_id" data-value="">
                                        <br>
                                        <span class="text-danger display-none" id="err_msg_code">Please Enter voucher code</span>

                                    </td>
                                    <th>Total</th>
                                    <td>MYR <span
                                                id="total">{{number_format($value->sub_total + $taxValue - $discountTotal,2)}}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="float-left">
                                        <button type="button" class="btn btn-sm btn-login float-right"
                                                data-loading-text="<i class='fa fa-spinner fa-spin'></i> loading"
                                                data-original-text="Checkout"
                                                data-id="{{$branchSlug}}" onclick="paymentOption(this)">Checkout
                                        </button>
                                    </td>
                                </tr>
                            @else
                                <tr class="text-center">
                                    <td colspan="6">Cart is Empty!</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
