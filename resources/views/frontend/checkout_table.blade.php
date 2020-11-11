@php
    $userData = \Illuminate\Support\Facades\Auth::guard('fronts')->user();
@endphp
<div class="table-responsive">

    <table class="table table-hover table-light">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Product Name</th>
            <th scope="col">Quantity</th>
            <th scope="col">Addon Items</th>
            <th scope="col">Price</th>
            <th scope="col" style="text-align: right;">Total</th>
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
                                   class="input-text qty text" data-id="{{$key}}">
                            <button type="button" class="btn btn-sm btn-warning ml-1 display-none" name="btnUpdate_{{$key}}" id="btnUpdate_{{$key}}"
                                    onclick="updateCart('{{$value['cart_id']}}','{{$value['cart_detail_id']}}','{{$value['product_id']}}','{{$key}}','{{$branchSlug}}');">
                                <i class="fa fa-refresh"></i></button>
                            <button type="button" class="btn btn-sm btn-danger ml-1" id="btnTrash_{{$key}}"
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
                    <td style="text-align: right;">{{number_format($value->product_total,2)}} </td>
                </tr>
            @endforeach
        @else
            <tr class="text-center">
                <td colspan="6">Cart is Empty!</td>
            </tr>
        @endif
        </tbody>
    </table>

</div>
@if(isset($cartData) && count($cartData) > 0)
    <div class="product-price-section">
        <div class="product-price-row row justify-content-end align-items-center mb-2 mx-auto">
            <div class="col-lg-2 col-md-3 col-5">
                <div class="price-header">
                    <span>Sub-Total</span>
                </div>
            </div>
            <div class="col-lg-1 col-md-2 col-4">
                <div class="product-light-header">
                    <span>MYR</span> &nbsp;<span
                            id="sub_total">{{number_format($value->sub_total,2)}}</span>
                </div>
            </div>
        </div>
        <div class="product-price-row row justify-content-end align-items-center mb-2 mx-auto @if(isset($voucher_id) && !empty($voucher_id)) d-flex @else display-none @endif" id="discount_tag">
            <div class="col-lg-2 col-md-3 col-5">
                <div class="price-header">
                    <span>Discount</span>
                </div>
            </div>
            <div class="col-lg-1 col-md-2 col-4">
                <div class="product-light-header">
                    <span>MYR</span>&nbsp;<span
                            id="discount_value">{{number_format($discountTotal,2)}}</span>
                </div>
                <input type="hidden" data-value="{{$discountTotal}}" id="voucher_amount">
            </div>
        </div>
        <div id="tax">
        @php $taxValue = 0 ;@endphp
        @if(isset($branchTax) && !empty($branchTax))
            @foreach($branchTax as $bvalue)
                @php
                    $tax = ($value->sub_total-$discountTotal) * $bvalue->rate / 100;
                    $taxValue += $tax;
                @endphp
                <div class="product-price-row row justify-content-end align-items-center mb-2 mx-auto">
                    <div class="col-lg-2 col-md-3 col-5">
                        <div class="price-header">
                            <span>Tax({{$bvalue->code}} {{$bvalue->rate}}%)</span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-4">
                        <div class="product-light-header">
                            <span>MYR {{number_format($tax,2)}}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        </div>

        <div class="product-price-row row justify-content-end align-items-center mb-2 mx-auto">
            <div class="col-md-4 col-12 mb-md-0 mb-3 text-right">
                <div class="voucher-code">
                    <input type="text" name="voucher_code" id="voucher_code"
                           placeholder="Enter voucher code" class="" onkeyup="pressEnter(event)">
                    <button type="button" name="apply" id="btnApply" class="btn-apply btn btn-sm btn-success"
                            data-id="{{$branchSlug}}" onclick="checkVoucher(this)">Apply
                    </button>
                    <input type="hidden" id="voucher_id" data-value="{{$voucher_id}}">
                    <br>
                    <span class="text-danger display-none"
                          id="err_msg_code">Please Enter voucher code</span>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-5">
                <div class="price-header">
                    <span>Total</span>
                </div>
            </div>
            <div class="col-lg-1 col-md-2 col-4">
                <div class="product-light-header">
                    <span>MYR&nbsp;</span> &nbsp;
                    @php
                        $total = number_format($value->sub_total + $taxValue - $discountTotal,2);
                        $n = explode('.',$total);
                        $x=5;
                        //$t = round(($n[1]+$x/2)/$x)*$x;
                        $t = 5 * round($n[1] / 5);
                        if($t >= 100){
                            $grand_total = $n[0] + 1;
                        } else {
                            $grand_total = $n[0].'.'.$t;
                        }
                    @endphp
                    <span id="total"> {{number_format($grand_total,2)}}</span>
                </div>
            </div>
        </div>
        <div class="product-price-row row justify-content-end align-items-center pb-3 mx-auto">
            <div class="col-md-2 col-5">
                <div class="product-checkout">
                    <button type="button" class="btn btn-sm btn-login float-right"
                            data-loading-text="<i class='fa fa-spinner fa-spin'></i> loading"
                            data-original-text="Checkout"
                            data-id="{{$branchSlug}}" onclick="paymentOption(this)">Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
<script src="{{asset('frontend/js/pages/checkout.js')}}"></script>


