@if(isset($tableColorData))
    {{ Form::model($tableColorData, ['route' => ['admin.table-color.update',$tableColorData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmTableColor', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('id', $tableColorData->id) }}
@else
    {{ Form::model(null, ['route' => 'admin.table-color.store', 'files' => true, 'role' => 'form', 'id'=>'frmTableColor', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="modal-body">
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('time_minute',trans('backend/table_color.minutes'), ['class'=>"col-form-label text-right"]) }}
                {{ Form::text('time_minute', old('time_minute'), ["required","class"=>"form-control","placeholder"=>trans('backend/table_color.minutes'),"id"=>"time_minute","name"=>"time_minute","onkeypress"=>"return onlyNumberKey(event)"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                <div class="form-group">
                    {{ Form::label('color_code',trans('backend/table_color.color'), ['class'=>"col-form-label text-right"]) }}
                    {{ Form::text('color_code', old('color_code'), ["required","class"=>"form-control","placeholder"=>trans('backend/table_color.color'),"id"=>"color_code","name"=>"color_code"]) }}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                <label for="status">{{trans('backend/table_color.status')}}</label>
                <select name="status" id="status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($tableColorData)) {{ ($tableColorData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($tableColorData)) {{ ($tableColorData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
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
{{ Form::close()}}
