<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15"> {{trans('backend/product.del_product')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
            {!! Form::model(null, ['route' => ['admin.product.destroy', $uuid], 'method' => 'delete', 'files' => false, 'class' => 'validate', 'id' => 'frmProduct', 'role' => 'form']) !!}
            <div class="modal-body">
                <div>{{trans('backend/product.delete_product')}}</div>
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
<script src="{{asset('backend/dist/js/pages/product.js')}}"></script>
