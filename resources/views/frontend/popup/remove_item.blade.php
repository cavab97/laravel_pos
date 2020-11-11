<div class="mw-400 modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="custom-modal-header modal-header">
            <h5 class="custom-modal-title modal-title" id="exampleModalLongTitle">Remove Item</h5>
            <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body text-left">
            <div>{{trans('frontend/common.delete_cart_item')}}</div>
        </div>
        <div class="modal-footer justify-content-end">
            <button type="button" class="btn btn-login" onclick="removeCartItem('{{$cart_detail_id}}','{{$branchSlug}}');">Yes</button>
            <button type="button" class="btn btn-login" data-dismiss="modal">No</button>
        </div>


    </div>
</div>

<script src="{{asset('frontend/js/pages/checkout.js')}}"></script>
