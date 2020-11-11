<div class="mw-400 modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="custom-modal-header modal-header">
            <h5 class="custom-modal-title modal-title" id="exampleModalLongTitle">Clear Cart</h5>
            <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body text-left">
            <div class="modal-logindata">
                {{trans('frontend/common.branch_product_exist_in_cart')}}
            </div>
        </div>
        <div class="modal-footer justify-content-end">
            <button type="button" class="btn btn-login" onclick="clearCartItem('{{$slug}}');">Yes</button>
            <button type="button" class="btn btn-login" data-dismiss="modal">No</button>
        </div>
    </div>
</div>

<script src="{{asset('frontend/js/pages/home.js')}}"></script>
