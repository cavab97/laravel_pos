@if(isset($taxData))
    {{ Form::model($taxData, ['route' => ['admin.tax.update',$taxData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmTax', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('tax_id', $taxData->tax_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.tax.store', 'files' => true, 'role' => 'form', 'id'=>'frmTax', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

    <div class="row">
        <div class="col-md-4 required">
            <div class="form-group">
                {{ Form::label('code',trans('backend/tax.code')) }}
                {{ Form::text('code', old('code'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/tax.code'),"id"=>"code","name"=>"code"]) }}
            </div>
        </div>
        <div class="col-md-4 required">
            <div class="form-group">
                {{ Form::label('rate',trans('backend/tax.rate')) }}
                {{ Form::number('rate', old('rate'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/tax.rate'),"id"=>"rate","name"=>"rate","min"=>0,"max"=>100]) }}
            </div>
        </div>
        <div class="col-md-4 required">
            <div class="form-group">
                <label for="status">{{trans('backend/category.status')}}</label>
                <select name="status" id="status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($taxData)) {{ ($taxData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($taxData)) {{ ($taxData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-10 required">
            <div class="form-group">
                {{ Form::label('description',trans('backend/tax.description'), ['class'=>"col-form-label text-right"]) }}
                {{ Form::textarea('description', old('description'), ["class"=>"form-control","placeholder"=>trans('backend/tax.description'),"id"=>"description",'rows'=>2,'cols'=>5]) }}
            </div>
        </div>

        <input type="hidden" name="is_fixed" value="0">
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
            <a href="{{ route('admin.tax.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

@if($hasPermission)
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
                                   value="{{$value->branch_id}}" @if(isset($selectedBranchList)) {{ (in_array($value->branch_id, $selectedBranchList))? "checked" : "" }}@endif>
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
{{ Form::close() }}
