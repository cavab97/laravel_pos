<div class="custom-modal modal fade" id="myModel" tabindex="-1" role="dialog" aria-labelledby="pop"
     aria-hidden="true"></div>
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="custom-modal-header modal-header">
            <h5 class="custom-modal-title modal-title" id="exampleModalLongTitle">{{$productData->name}}</h5>
            <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @if((isset($productData) && count($productData->attribute)>0) || (isset($productData) && count($productData->modifier)>0))
        <div class="modal-body">
            <div class="modal-data-sec">
                @if(isset($productData)&& !empty($productData->attribute))
                    <div id="attSelect">
                    @foreach($productData->attribute as $caMainKey => $cv1)
                        @if(count($cv1->attribute) > 0)
                        <label class="modal-label" for="exampleFormControlTextarea{{$caMainKey}}">{{$cv1->name}}</label>
                        <div class="modal-type-btn-sec">
                            <div class="checkbox-toolbar">
                                @foreach($cv1->attribute as $ak1 => $av1)

                                    <input type="checkbox" name="prod_attribute[]" id="radioApple{{$av1->name}}"
                                           value="{{$av1->attribute_id}}" data-price="{{$av1->price}}">
                                    <label for="radioApple{{$av1->name}}">
                                        {{$av1->name}}
                                    </label>
                                    {{--<a href="javascrip:void(0);" class="btn-type btn btn-sm">{{$av1->name}}</a>--}}
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                    </div>
                @endif

                @if(isset($productData)&& !empty($productData->modifier))
                    <div class="mt-3">
                        <label class="modal-label" for="exampleFormControlTextarea1">Take Your Pick</label>
                        <div class="row mx-auto">
                            @foreach($productData->modifier as $key=>$value)
                                <div class="chiller_cb col-md-4">
                                    <div class="custom-checkbox-sec">
                                        <input id="myCheckbox{{$key}}" class="modifierBox" name="prod_modifier[]" type="checkbox"
                                               value="{{$value['modifier_id']}}" data-price="{{$value['price']}}">
                                        <div class="checkbox-name">
                                            <label for="myCheckbox{{$key}}">{{$value['name']}}</label>
                                        </div>
                                        <span></span>
                                        <div class="checkbox-price">
                                            <label for="myCheckbox{{$key}}"
                                                   class="clr-red">{{config('constants.currency').' '.$value['price']}}</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
        <div class="modal-footer justify-content-center">
            <div class="modal-add-btn product-modal-add-btn w-100">
                <div class="modal-footer-box row">
                    <div class="col-md-4 col-5">
                        <div class="quantity buttons_added">
                            <input type="button" value="-" class="minus" onclick="decrementValue();">

                                <input type="number" step="1" min="1" max="999" name="quantity" id="quantity"
                                         value="1" title="Qty"
                                         class="input-text qty text" size="4" pattern="" inputmode="" onKeyPress="if(this.value.length==3) return false;">
                                {{--<input type="text" maxlength="3" name="quantity" id="quantity" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                   value="1" title="Qty" class="input-text qty text">--}}

                            <input type="button" value="+" class="plus" onclick="incrementValue();">
                        </div>
                    </div>
                    <div class="col-md-4 col-7">
                        <button class="btn-add-extra btn" onclick="addToCart()" id="addToCart">
                                <span>
                                    <img src="{{asset('frontend/images/cart.png')}}" class="mw-100"
                                         style="height: 20px;"/>
                                </span>
                            {{--<i class="fa fa-shopping-cart"></i>--}} Add to cart
                        </button>
                    </div>
                    <div class="col-md-4 pt-md-0 pt-3">
                        <div class="product-subtotal text-md-right">
                            <span class="modal-label">Sub-Total:</span>
                            <label class="clr-red">MYR <span id="cart_total">{{$productData->price}}</span></label>
                        </div>
                        <input type="hidden" name="prod_price" id="prod_price" data-id="{{$productData->price}}">
                        <input type="hidden" name="branch_slug" id="branch_slug" data-id="{{$slug}}">
                        <input type="hidden" name="product_id" id="product_id" data-id="{{$productData->product_id}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('frontend/js/pages/product.js')}}"></script>
<script>
    $('#attSelect input:checkbox').on('change', function () {
    //$(document).on('change','input[name="prod_attribute[]"]',function() {
        $(this).siblings('#attSelect input:checkbox').not(this).prop('checked', false);

        $.each($("input[name='prod_attribute[]']:checked"), function(){
            var attribute_price = $(this).data("price");
            var cart_total = parseFloat($("#cart_total").text());
            cart_total = cart_total + attribute_price;
            $("#cart_total").text(cart_total);
        });
    });
</script>



