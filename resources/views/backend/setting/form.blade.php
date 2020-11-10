@if(isset($settingData))
    {{ Form::model($settingData, ['route' => ['admin.setting.update',$settingData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmSetting', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('id', $settingData->system_setting_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.setting.store', 'files' => true, 'role' => 'form', 'id'=>'frmSetting', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="modal-body">
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('display_name',trans('backend/setting.display_name'), ['class'=>"col-form-label text-right"]) }}
                @if(isset($settingData) && !empty($settingData->display_name))
                    {{ Form::text('display_name', old('display_name'), ["required","class"=>"form-control","placeholder"=>trans('backend/setting.display_name'),"id"=>"display_name","name"=>"display_name","readonly"]) }}
                @else
                    {{ Form::text('display_name', old('display_name'), ["required","class"=>"form-control","placeholder"=>trans('backend/setting.display_name'),"id"=>"display_name","name"=>"display_name"]) }}
                @endif
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('Namespace',trans('backend/setting.namespace'), ['class'=>"col-form-label text-right"]) }}
                @if(isset($settingData) && $settingData->is_system_setting == 1)
                    {{ Form::text('namespace', old('namespace'), ["required","class"=>"form-control","placeholder"=>trans('backend/setting.namespace'),"id"=>"namespace","name"=>"namespace","readonly"]) }}
                @else
                    {{ Form::text('namespace', old('namespace'), ["required","class"=>"form-control","placeholder"=>trans('backend/setting.namespace'),"id"=>"namespace","name"=>"namespace"]) }}
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('key',trans('backend/setting.key'), ['class'=>"col-form-label text-right"]) }}
                @if(isset($settingData))
                    {{ Form::text('key', old('key'), ["required","class"=>"form-control","placeholder"=>trans('backend/setting.key'),"id"=>"key","name"=>"key","readonly"]) }}
                @else
                    {{ Form::text('key', old('key'), ["required","class"=>"form-control","placeholder"=>trans('backend/setting.key'),"id"=>"key","name"=>"key"]) }}
                @endif
            </div>
        </div>

        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('type',trans('backend/setting.type'), ['class'=>"col-form-label text-right"]) }}
                @if(isset($settingData))
                    <select name="selected_type" id="selected_type" class="form-control" required disabled>
                        <option value="">{{trans('backend/setting.select_type')}}</option>
                        <option value="1" @if(isset($settingData)) {{ ($settingData->type == 1) ? 'selected':''  }} @endif>{{trans('backend/setting.string')}}</option>
                        <option value="2" @if(isset($settingData)) {{ ($settingData->type == 2) ? 'selected':''  }} @endif>{{trans('backend/setting.integer')}}</option>
                        <option value="3" @if(isset($settingData)) {{ ($settingData->type == 3) ? 'selected':''  }} @endif>{{trans('backend/setting.float')}}</option>
                        <option value="4" @if(isset($settingData)) {{ ($settingData->type == 4) ? 'selected':''  }} @endif>{{trans('backend/setting.boolean')}}</option>
                        <option value="5" @if(isset($settingData)) {{ ($settingData->type == 5) ? 'selected':''  }} @endif>{{trans('backend/setting.color')}}</option>
                        <option value="6" @if(isset($settingData)) {{ ($settingData->type == 6) ? 'selected':''  }} @endif>{{trans('backend/setting.minutes')}}</option>
                    </select>
					<input type="hidden" name="type" id="type" value="{{$settingData->type}}">
                @else
                    <select name="type" id="type" class="form-control" required>
                        <option value="">{{trans('backend/setting.select_type')}}</option>
                        <option value="1" @if(isset($settingData)) {{ ($settingData->type == 1) ? 'selected':''  }} @endif>{{trans('backend/setting.string')}}</option>
                        <option value="2" @if(isset($settingData)) {{ ($settingData->type == 2) ? 'selected':''  }} @endif>{{trans('backend/setting.integer')}}</option>
                        <option value="3" @if(isset($settingData)) {{ ($settingData->type == 3) ? 'selected':''  }} @endif>{{trans('backend/setting.float')}}</option>
                        <option value="4" @if(isset($settingData)) {{ ($settingData->type == 4) ? 'selected':''  }} @endif>{{trans('backend/setting.boolean')}}</option>
                        <option value="5" @if(isset($settingData)) {{ ($settingData->type == 5) ? 'selected':''  }} @endif>{{trans('backend/setting.color')}}</option>
                        <option value="6" @if(isset($settingData)) {{ ($settingData->type == 6) ? 'selected':''  }} @endif>{{trans('backend/setting.minutes')}}</option>
                    </select>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            @if(isset($settingData) && $settingData->type == 4)
                <div class="form-group" id="textBoxId" style="display: none;">
                    {{ Form::label('value',trans('backend/setting.value'), ['class'=>"col-form-label text-right"]) }}
                    {{ Form::text('value', old('value'), ["required","class"=>"form-control","placeholder"=>trans('backend/setting.value'),"id"=>"value","name"=>"value"]) }}
                </div>
                <div class="form-group" id="selectBoxId">
                    {{ Form::label('value',trans('backend/setting.value'), ['class'=>"col-form-label text-right"]) }}
                    <select name="booleanvalue" id="booleanvalue" class="form-control" required>
                        <option value="">{{trans('backend/setting.select_value')}}</option>
                        <option value="true" @if(isset($settingData)) {{ ($settingData->value == 'true') ? 'selected':''  }} @endif>{{trans('backend/setting.true')}}</option>
                        <option value="false" @if(isset($settingData)) {{ ($settingData->value == 'false') ? 'selected':''  }} @endif>{{trans('backend/setting.false')}}</option>
                    </select>
                </div>
            @else
                <div class="form-group" id="textBoxId">
                    {{ Form::label('value',trans('backend/setting.value'), ['class'=>"col-form-label text-right"]) }}
                    {{ Form::text('value', old('value'), ["required","class"=>"form-control","placeholder"=>trans('backend/setting.value'),"id"=>"value","name"=>"value"]) }}
                </div>
                <div class="form-group" id="selectBoxId" style="display: none;">
                    {{ Form::label('value',trans('backend/setting.value'), ['class'=>"col-form-label text-right"]) }}
                    <select name="booleanvalue" id="booleanvalue" class="form-control" required>
                        <option value="">{{trans('backend/setting.select_value')}}</option>
                        <option value="true">{{trans('backend/setting.true')}}</option>
                        <option value="false">{{trans('backend/setting.false')}}</option>
                    </select>
                </div>
            @endif
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
