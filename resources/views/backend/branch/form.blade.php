@if(isset($branchData))
    {{ Form::model($branchData, ['route' => ['admin.branch.update',$branchData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmBranch', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('branch_id', $branchData->branch_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.branch.store', 'files' => true, 'role' => 'form', 'id'=>'frmBranch', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif

<div class="form-sec">

    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('name',trans('backend/branch.name')) }}
                {{ Form::text('name', old('name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.name'),"id"=>"name","name"=>"name"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('contact_no',trans('backend/branch.contact_no')) }}
                {{ Form::tel('contact_no', old('contact_no'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.contact_no'),"id"=>"contact_no","name"=>"contact_no","onkeypress"=>"return onlyNumberKey(event)","minlength"=>6,"maxlength"=>12]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('email',trans('backend/branch.email')) }}
                {{ Form::email('email', old('email'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.email'),"id"=>"email","name"=>"email"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('contact_person',trans('backend/branch.contact_person')) }}
                {{ Form::text('contact_person', old('contact_person'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.contact_person'),"id"=>"contact_person","name"=>"contact_person"]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('open_from',trans('backend/branch.open_from')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    {{ Form::text('open_from', old('open_from'), ["required","class"=>"form-control form-control-sm float-right","placeholder"=>trans('backend/branch.open_from'),"id"=>"open_from","name"=>"open_from"]) }}
                    <span id="from_miss_error" class="text-danger"></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('closed_on',trans('backend/branch.closed_on')) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    {{ Form::text('closed_on', old('closed_on'), ["required","class"=>"form-control form-control-sm float-right","placeholder"=>trans('backend/branch.closed_on'),"id"=>"closed_on","name"=>"closed_on"]) }}
                    <span id="to_miss_error" class="text-danger"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">        
        <div class="col-md-2 required">
            <div class="form-group">
                {{ Form::label('order_prefix',trans('backend/branch.order_prefix')) }}
                {{ Form::text('order_prefix', old('order_prefix'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.order_prefix'),"id"=>"order_prefix","maxlength"=>"5"]) }}
            </div>
        </div>
        <div class="col-md-2 required">
            <div class="form-group">
                {{ Form::label('invoice_start',trans('backend/branch.invoice_start')) }}
                {{ Form::number('invoice_start', old('invoice_start'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.invoice_start'),"id"=>"invoice_start","maxlength"=>"6"]) }}
            </div>
        </div>
		<div class="col-md-2">
            <div class="form-group">
                {{ Form::label('service_charge',trans('backend/branch.service_charge')) }}
                {{ Form::number('service_charge', old('service_charge'), ["min"=>0,"class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.service_charge'),"id"=>"service_charge","name"=>"service_charge","max"=>100]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('address',trans('backend/branch.address')) }}
                {{ Form::textarea('address', old('address'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.address'),"id"=>"address","name"=>"address","rows"=>"3"]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('latitude',trans('backend/branch.latitude')) }}
                {{ Form::number('latitude', old('latitude'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.latitude'),"id"=>"latitude","name"=>"latitude","min"=>0]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('longitude',trans('backend/branch.longitude')) }}
                {{ Form::number('longitude', old('longitude'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/branch.longitude'),"id"=>"longitude","name"=>"longitude","min"=>0]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">

                {{ Form::label('branch_banner',trans('backend/branch.branch_banner')) }}
                {{ Form::file('branch_banner', [$required, "accept"=>"image/*", "onChange"=>"previewImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"branch_banner","name"=>"branch_banner"]) }}
                <div class="col-3 mt-2" id="icon_uploaded">
                    @if(!empty($branchData->branch_banner))
                        <img src="{{asset($branchData->branch_banner)}}" width="80px" height="80px">
                    @endif
                </div>
                <div class="col-3 mt-2" id="icon_preview" style="display:none">
                    <img src="" id="profile_preview" width="80px" height="80px"/>
                </div>
            </div>
        </div>

        <div class="col-md-6 required">
            <div class="form-group">
                <label for="role_status">{{trans('backend/branch.status')}}</label>
                <select name="status" id="status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($branchData)) {{ ($branchData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($branchData)) {{ ($branchData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>        
    </div>
	
	<div class="row">
        @if(isset($taxList) && count($taxList) > 0)
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header bg-white border">
                        <h3 class="card-title">{{trans('backend/branch.assign_tax')}}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($taxList as $key=>$value)
                                @php
                                    $checked = '';
                                    $taxArray = array();
                                    if (isset($branchData->taxData)) {
                                    foreach ($branchData->taxData as $k=>$v){
                                         if($value['tax_id']== $v->tax_id && ($v->status == 1)){
                                                array_push($taxArray,$v->tax_id);
                                            }
                                        }
                                    }
                                 if(in_array($value['tax_id'],$taxArray)){
                                    $checked = 'checked';
                                }
                                @endphp
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="switch">
                                            <input type="checkbox"
                                                   id="customSwitch{{$key}}"
                                                   name="tax_id[]"
                                                   value="{{$value['tax_id']}}" {{$checked}}>
                                            <span class="slider round"></span>
                                        </label>
                                        <label for="tax_id">{{$value['code'].' ( '.$value['rate'].'% ) '}}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

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
            <a href="{{ route('admin.branch.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
