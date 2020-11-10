@if(isset($customerData))
    {{ Form::model($customerData, ['route' => ['admin.customer.update',$customerData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmCustomer', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('customer_id', $customerData->customer_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.customer.store', 'files' => true, 'role' => 'form', 'id'=>'frmCustomer', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif

<div class="form-sec p-3">

    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('name',trans('backend/customer.name')) }}
                {{ Form::text('name', old('name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/customer.name'),"id"=>"name","name"=>"name"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('username',trans('backend/customer.username')) }}
                {{ Form::text('username', old('username'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/customer.username'),"id"=>"username","name"=>"username"]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('email',trans('backend/common.email')) }}
                {{ Form::email('email', old('email'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/common.email'),"id"=>"email","name"=>"email"]) }}
            </div>

        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('mobile',trans('backend/customer.mobile')) }}
                {{ Form::tel('mobile', old('mobile'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/customer.mobile'),"id"=>"mobile","name"=>"mobile","onkeypress"=>"return onlyNumberKey(event)","minlength"=>9,"maxlength"=>12]) }}
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('password',trans('backend/customer.password')) }}
                {{ Form::password('password', ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/customer.password'),"id"=>"password","name"=>"password","maxlength" => "8","minlength" => "8",$required]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('confirm_password',trans('backend/customer.confirm_password')) }}
                {{ Form::password('confirm_password', ["class"=>"form-control form-control-sm", "placeholder"=>trans('backend/customer.confirm_password'), "id"=>"confirm_password","maxlength" => "8","minlength" => "8", $required]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            {{ Form::label('profile',trans('backend/customer.profile')) }}
            {{ Form::file('profile', ["accept"=>"image/*", "onChange"=>"previewImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"profile","name"=>"profile"]) }}
            <div class="col-3 mt-2" id="icon_uploaded">
                @if(!empty($customerData->profile))
                    <img src="{{asset($customerData->profile)}}" width="80px" height="80px">
                @endif
            </div>
            <div class="col-3 mt-2" id="icon_preview" style="display:none">
                <img src="" id="profile_preview" width="80px" height="80px"/>
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                <label for="role_status">{{trans('backend/customer.status')}}</label>
                <select name="status" id="status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($customerData)) {{ ($customerData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($customerData)) {{ ($customerData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="display_message" class="col-md-12 display-none">
    <div class="alert display-none alert-success"></div>
    <div class="alert display-none alert-danger"></div>
</div>
<div class="card-header ui-sortable-handle" style="cursor: move;">
    <div class="card-tools">
        <div class="form-group ">
            {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading', 'data-original-text'=>trans('backend/common.submit')])}}
            &nbsp;&nbsp;
            <a href="{{ route('admin.customer.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
