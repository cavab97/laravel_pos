@if(isset($stateData))
    {{ Form::model($stateData, ['route' => ['admin.state.update',$stateData->state_id], 'files' => true, 'role' => 'form', 'id'=>'frmState', 'method'=>'put', 'class'=>'forms-sample']) }}
@else
    {{ Form::model(null, ['route' => 'admin.state.store', 'files' => true, 'role' => 'form', 'id'=>'frmState', 'method'=>'post', 'class'=>'forms-sample']) }}
@endif
<input type="hidden" id="state_id" value="0"/>
<div class="modal-body">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('country_id',trans('backend/state.select_country')) }}
                <input type="text" class="form-control" value="{{$countries->name}}" readonly/>
                <input type="hidden" name="country_id" value="{{$countries->country_id}}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 required">
            <div class="form-group">
                {{ Form::label('name',trans('backend/state.name')) }}
                {{ Form::text('name', old('name'), ["required","class"=>"form-control","placeholder"=>trans('backend/state.enter_name'),"id"=>"name","name"=>"name","maxlength"=>100]) }}
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger btn-sm"
            data-dismiss="modal">{{trans('backend/common.close')}}</button>
    <button type="submit" class="btn btn-info btn-sm" id="btnSubmit"
            data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> loading..."
            data-original-text="{{trans('backend/common.submit')}}">{{trans('backend/common.submit')}}
    </button>
</div>
{{ Form::close() }}