<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15"> {{trans('backend/printer.delete_printer')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        {!! Form::model(null, ['route' => ['admin.printer.destroy', $uuid], 'method' => 'delete', 'files' => false, 'class' => 'validate', 'id' => 'frmPrinter', 'role' => 'form']) !!}
        <div class="modal-body">
            <div>{{trans('backend/printer.delete_printer_msg')}}</div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm"
                    data-dismiss="modal">{{trans('backend/common.close')}}</button>
            {{ Form::button(trans('backend/common.confirm'),['type'=>'submit','class'=>'btn btn-info btn-sm','id'=>'btnSubmit', 'data-text'=>'Submit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading'])}}
            &nbsp;&nbsp;
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script src="{{asset('backend/dist/js/pages/printer.js')}}"></script>
