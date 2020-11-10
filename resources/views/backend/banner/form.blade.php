@if(isset($bannerData))
    {{ Form::model($bannerData, ['route' => ['admin.banner.update',$bannerData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmBanner', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('banner_id', $bannerData->banner_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.banner.store', 'files' => true, 'role' => 'form', 'id'=>'frmBanner', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('title',trans('backend/banner.title')) }}
                {{ Form::text('title', old('title'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/banner.title'),"id"=>"title","name"=>"title"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                <label for="status">{{trans('backend/banner.status')}}</label>
                <select name="status" id="status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($bannerData)) {{ ($bannerData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($bannerData)) {{ ($bannerData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <label class="control-label">Banner for mobile</label><br>
            <span style="color: red" class="text-sm"> (JPEG, PNG format only. Suggested photo width and height 299*156px) </span>
            {{ Form::file('banner_for_mobile', [$required, "accept"=>"image/*", "onChange"=>"previewImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"banner_for_mobile","name"=>"banner_for_mobile"]) }}
            <div class="col-3 mt-2" id="icon_uploaded">
                @if(!empty($bannerData->banner_for_mobile))
                    <img src="{{asset($bannerData->banner_for_mobile)}}" width="80px" height="80px">
                @endif
            </div>
            <div class="col-3 mt-2" id="icon_preview" style="display:none">
                <img src="" id="profile_preview" width="80px" height="80px"/>
            </div>
        </div>
        <div class="col-md-6 required">
            <label class="control-label">Banner for web</label><br>
                <span style="color: red " class="text-sm"> (JPEG, PNG format only. Suggested photo width and height 1366*234px) </span>
            {{ Form::file('banner_for_web', [$required, "accept"=>"image/*", "onChange"=>"previewWebImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"banner_for_web","name"=>"banner_for_web"]) }}
            <div class="col-3 mt-2" id="web_icon_uploaded">
                @if(!empty($bannerData->banner_for_web))
                    <img src="{{asset($bannerData->banner_for_web)}}" width="80px" height="80px">
                @endif
            </div>
            <div class="col-3 mt-2" id="web_icon_preview" style="display:none">
                <img src="" id="web_profile_preview" width="80px" height="80px"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description',trans('backend/common.description')) }}
                {{ Form::textarea('description', old('description'), ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/common.description'),"id"=>"description","name"=>"description","rows"=>"3"]) }}
            </div>
        </div>
    </div>
    @if(isset($branchList) && count($branchList) > 0)
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border">
                    <h3 class="card-title">{{trans('backend/category.assign_branch')}}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($branchList as $key=>$value)
                            @php
                                $checked = '';
                                $branchArray = array();
                                if (isset($bannerData->branchData)) {
                                foreach ($bannerData->branchData as $k=>$v){
                                     if($value['branch_id']== $v->branch_id && ($v->status == 1)){
                                            array_push($branchArray,$v->branch_id);
                                        }
                                    }
                                }
                             if(in_array($value['branch_id'],$branchArray)){
                                $checked = 'checked';
                            }
                            @endphp
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="branch_id">{{$value['name']}}</label>
                                    <br>
                                    <label class="switch">
                                        <input type="checkbox" id="customSwitch{{$key}}"
                                               name="branch_id[]" {{$checked}}
                                               value="{{$value['branch_id']}}">
                                        <span class="slider round"></span>
                                    </label>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        </div>
    @endif
</div>
<div id="display_message" class="col-md-12 display-none">
    <div class="alert display-none alert-success"></div>
    <div class="alert display-none alert-danger"></div>
</div>
<div class="card-header ui-sortable-handle" style="cursor: move;">
    <div class="card-tools">
        <div class="form-group ">
            {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('backend/common.submit')])}}
            &nbsp;&nbsp;
            <a href="{{ route('admin.banner.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
