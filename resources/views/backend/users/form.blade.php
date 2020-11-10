@php
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp
@if(isset($userData))
    {{ Form::model($userData, ['route' => ['admin.users.update',$userData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmUser', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('user_id', $userData->id) }}
@else
    {{ Form::model(null, ['route' => 'admin.users.store', 'files' => true, 'role' => 'form', 'id'=>'frmUser', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif

<div class="form-sec">

    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('name',trans('backend/users.name')) }}
                {{ Form::text('name', old('name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.name'),"id"=>"name","name"=>"name"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('username',trans('backend/users.username')) }}
                {{ Form::text('username', old('username'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/users.username'),"id"=>"username","name"=>"username"]) }}
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
                {{ Form::label('country_code',trans('backend/users.dialling_code')) }}
                {{ Form::text('country_code', old('country_code'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/users.dialling_code'),"id"=>"country_code","name"=>"country_code","onkeypress"=>"return onlyNumberKey(event)","maxlength"=>4]) }}
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('mobile',trans('backend/users.mobile')) }}
                {{ Form::tel('mobile', old('mobile'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/users.mobile'),"id"=>"mobile","name"=>"mobile","onkeypress"=>"return onlyNumberKey(event)","minlength"=>6,"maxlength"=>12]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('role_id',trans('backend/roles.role')) }}
                <select name="role_id" id="role_id" class="form-control form-control-sm" required>
                    <option value="" hidden>{{trans('backend/roles.select_role')}}</option>
                    @foreach($roleList as $value)
                        @php
                            $selected = '';
                            if(isset($userData)){
                                if($value->role_id == $userData->role){
                                    $selected = 'selected';
                                }
                            }
                        @endphp
                        <option value="{{$value->role_id}}" {{$selected}}>{{$value->role_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('commision_percent',trans('backend/users.commision_percent')) }}
                {{ Form::number('commision_percent', old('commision_percent'), ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/users.commision_percent'),"id"=>"commision_percent","name"=>"commision_percent","onkeypress"=>"return onlyNumberKey(event)","min"=>0,'max'=>100]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('password',trans('backend/users.password')) }}
                {{ Form::password('password', ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/users.password'),"id"=>"password","name"=>"password","maxlength" => "8","minlength" => "8",$required]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('confirm_password',trans('backend/users.confirm_password')) }}
                {{ Form::password('confirm_password', ["class"=>"form-control form-control-sm", "placeholder"=>trans('backend/users.confirm_password'), "id"=>"confirm_password","maxlength" => "8","minlength" => "8", $required]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                <label for="role_status">{{trans('backend/users.status')}}</label>
                <select name="status" id="status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($userData)) {{ ($userData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($userData)) {{ ($userData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            {{ Form::label('profile',trans('backend/users.profile')) }}
            {{ Form::file('profile', ["accept"=>"image/*", "onChange"=>"previewImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"profile","name"=>"profile"]) }}
            <div class="col-3 mt-2" id="icon_uploaded">
                @if(!empty($userData->profile))
                    <img src="{{asset($userData->profile)}}" width="80px" height="80px">
                @endif
            </div>
            <div class="col-3 mt-2" id="icon_preview" style="display:none">
                <img src="" id="profile_preview" width="80px" height="80px"/>
            </div>
        </div>
        <div class="col-md-6">
            {{Form::label('user_pin',trans('backend/users.user_pin'))}}
            <input type="text" class="form-control form-control-sm" name="user_pin" id="user_pin"
                   value="{{$generatePin}}" placeholder="{{trans('backend/users.user_pin')}}" maxlength="6"
                   readonly>

        </div>
    </div>

</div>

@if($user->role == 1)
<div class="form-sec mt-4">
<div class="card card-secondary">
    <div class="card-header ">
        <h3 class="card-title">{{trans('backend/users.assign_branch')}}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            @if(isset($branchList))
                @foreach($branchList as $key => $value)
                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="branch_id[]"
                                   value="{{$value->branch_id}}" @if(isset($userBranchIds)) {{ (in_array($value->branch_id,$userBranchIds))? "checked" : "" }}@endif>
                            <span class="slider round"></span>
                            <span class="switch-label">{{$value->name}}</span>
                        </label>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
</div>
@endif

<div id="display_message" class="col-md-12 display-none">
    <div class="alert display-none alert-success"></div>
    <div class="alert display-none alert-danger"></div>
</div>
<div class="card-header ui-sortable-handle" style="cursor: move;">
    <div class="card-tools">
        <div class="form-group ">
            {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading', 'data-original-text'=>trans('backend/common.submit')])}}
            &nbsp;&nbsp;
            <a href="{{ route('admin.users.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
