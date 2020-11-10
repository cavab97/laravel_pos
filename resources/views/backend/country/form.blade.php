@if(isset($countryData))
    {{ Form::model($countryData, ['route' => ['admin.country.update',$countryData->country_id], 'files' => true, 'role' => 'form', 'id'=>'frmCountry', 'method'=>'put', 'class'=>'forms-sample']) }}
@else
    {{ Form::model(null, ['route' => 'admin.country.store', 'files' => true, 'role' => 'form', 'id'=>'frmCountry', 'method'=>'post', 'class'=>'forms-sample']) }}
@endif
<input type="hidden" id="country_id" value="0"/>
<div class="modal-body">

    <div class="row">
        <div class="col-md-12 required">
            <div class="form-group">
                {{ Form::label('name',trans('backend/country.name')) }}
                {{ Form::text('name', old('name'), ["required","class"=>"form-control","placeholder"=>trans('backend/country.enter_name'),"id"=>"name","name"=>"name","maxlength"=>100]) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 required">
            <div class="form-group">
                {{ Form::label('sortname',trans('backend/country.sortname')) }}
                {{ Form::text('sortname', old('sortname'), ["required","class"=>"form-control","placeholder"=>trans('backend/country.enter_sortname'),"id"=>"sortname","name"=>"sortname","minlength"=>"2","maxlength"=>"2"]) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 required">
            <div class="form-group">
                {{ Form::label('phoneCode',trans('backend/country.phoneCode')) }}
                {{ Form::number('phoneCode', old('phoneCode'), ["required","class"=>"form-control","placeholder"=>trans('backend/country.enter_sortname'),"id"=>"phoneCode","name"=>"phoneCode"]) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="alert display-none alert-success"></div>
            <div class="alert display-none alert-danger"></div>
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