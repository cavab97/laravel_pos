<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15"> {{trans('backend/inventory.delete_inventory')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
            {!! Form::model(null, ['route' => ['admin.product_inventory.destroy', $uuid], 'method' => 'delete', 'files' => false, 'class' => 'validate', 'id' => 'frmInventory', 'role' => 'form']) !!}
            <div class="modal-body">
                <div>{{trans('backend/inventory.delete_inventory_msg')}}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm"
                        data-dismiss="modal">{{trans('backend/common.close')}}</button>
                {{ Form::button(trans('backend/common.confirm'),['type'=>'submit','class'=>'btn btn-info btn-sm','id'=>'btnSubmit', 'data-text'=>'Submit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading'])}}
                &nbsp;&nbsp;
            </div>
            {!! Form::close() !!}
        {{--@endif--}}
    </div>
</div>
<script src="{{asset('backend/dist/js/pages/product_inventory.js')}}"></script>
