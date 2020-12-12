<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15"> {{trans('backend/unit_type.delete_price_type')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        @if($productCount != 0)
            <div class="modal-body">
                <div>{{trans('backend/unit_type.not_delete_price_type_msg')}}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm"
                        data-dismiss="modal">{{trans('backend/common.close')}}</button>
            </div>
        @else
        {!! Form::model(null, ['route' => ['admin.price_type.destroy', $uuid], 'method' => 'delete', 'files' => false, 'class' => 'validate', 'id' => 'frmPricetype', 'role' => 'form']) !!}
        <div class="modal-body">
            <div>{{trans('backend/unit_type.delete_price_type_msg')}}</div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm"
                    data-dismiss="modal">{{trans('backend/common.close')}}</button>
            {{ Form::button(trans('backend/common.confirm'),['type'=>'submit','class'=>'btn btn-info btn-sm','id'=>'btnSubmit', 'data-text'=>'Submit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading'])}}
            &nbsp;&nbsp;
        </div>
        {!! Form::close() !!}
        @endif
    </div>
</div>
<script src="{{asset('backend/dist/js/pages/price_type.js')}}"></script>
