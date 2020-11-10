@php
    $lang = \App\Models\Languages::getBackLang();

@endphp
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15"> {{trans('backend/city.delete')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>

        {!! Form::model(null, ['route' => ['admin.city.destroy', $id], 'method' => 'delete', 'files' => false, 'class' => 'validate', 'id' => 'frmCity', 'role' => 'form']) !!}
        <div class="modal-body">
            <div>{{trans('backend/city.delete_confirm')}}</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert display-none alert-success"></div>
                    <div class="alert display-none alert-danger"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            {{ Form::button(trans('backend/common.confirm'),['type'=>'submit','class'=>'btn btn-info btn-sm','id'=>'btnSubmit', 'data-text'=>'Submit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading...','data-original-text'=>trans('backend/common.confirm')])}}
            &nbsp;&nbsp;
            <a href="javascript:void(0);" class="btn btn-danger btn-sm" data-dismiss="modal">{{trans('backend/common.close')}}</a>
        </div>
        {!! Form::close() !!}

    </div>
</div>
<script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/dist/js/pages/cities.js')}}"></script>