<div class="custom-modal modal fade" id="myModel" tabindex="-1" role="dialog" aria-labelledby="pop"
     aria-hidden="true"></div>
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="custom-modal-header modal-header">
            <h5 class="custom-modal-title modal-title" id="exampleModalLongTitle">{{$setmealData->name}}</h5>
            <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @if((isset($setmealData) && count($setmealData->product)>0))
        <div class="modal-body">
            <div class="modal-data-sec">

                @if(isset($setmealData)&& !empty($setmealData->product))
                    <div class="mt-1">
                        <label class="modal-label" for="exampleFormControlTextarea1">Take Your Pick</label>
                        <div class="row mx-auto">
                            @foreach($setmealData->product as $key=>$value)
                                <div class="chiller_cb col-md-6">
                                    <div class="custom-checkbox-sec">
                                        <input id="myCheckbox{{$key}}" class="modifierBox" name="prod_setmeal[]" type="checkbox"
                                               value="{{$value['product_id']}}" data-qty="{{$value['quantity']}}" checked>
                                        <div class="checkbox-name">
                                            <label for="myCheckbox{{$key}}">{{$value['product_name']}}</label>
                                        </div>
                                        <span></span>
                                        <div class="checkbox-price">
                                            <label for="myCheckbox{{$key}}"
                                                   class="clr-red">QTY {{$value['quantity']}}</label>
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

                            <input type="button" value="+" class="plus" onclick="incrementValue();">
                        </div>
                    </div>
                    <div class="col-md-4 col-7">
                        <button class="btn-add-extra btn" onclick="addToCart()" id="addToCart">
                                <span>
                                    <img src="{{asset('frontend/images/cart.png')}}" class="mw-100"
                                         style="height: 20px;"/>
                                </span>
                                Add to cart
                        </button>
                    </div>
                    <div class="col-md-4 pt-md-0 pt-3">
                        <div class="product-subtotal text-md-right">
                            <span class="modal-label">Sub-Total:</span>
                            <label class="clr-red">MYR <span id="cart_total">{{$setmealData->price}}</span></label>
                        </div>
                        <input type="hidden" name="prod_price" id="prod_price" data-id="{{$setmealData->price}}">
                        <input type="hidden" name="branch_slug" id="branch_slug" data-id="{{$slug}}">
                        <input type="hidden" name="setmeal_id" id="setmeal_id" data-id="{{$setmealData->setmeal_id}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('frontend/js/pages/setmeal-product.js')}}"></script>
<script>
    $('#attSelect input:checkbox').on('change', function () {
        $(this).siblings('#attSelect input:checkbox').not(this).prop('checked', false);

        $.each($("input[name='prod_attribute[]']:checked"), function(){
            var attribute_price = $(this).data("price");
            var cart_total = parseFloat($("#cart_total").text());
            cart_total = cart_total + attribute_price;
            $("#cart_total").text(cart_total);
        });
    });
</script>



