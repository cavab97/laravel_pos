@if(isset($tableData))
    {{ Form::model($tableData, ['route' => ['admin.table.update',$tableData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmTable', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('table_id', $tableData->table_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.table.store', 'files' => true, 'role' => 'form', 'id'=>'frmTable', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

        <div class="row">
            <div class="col-md-6 required">
                <div class="form-group">
                    {{ Form::label('table_name',trans('backend/table.name')) }}
                    {{ Form::text('table_name', old('table_name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/table.name'),"id"=>"table_name","name"=>"table_name"]) }}
                </div>
            </div>
            <div class="col-md-6 required">
                <div class="form-group">
                    {{ Form::label('branch_id',trans('backend/branch.branch')) }}
                    <select id="branch_id" name="branch_id" class="form-control form-control-sm">
                        <option value="">{{trans('backend/table.select_branch')}}</option>
                        <?php
                        foreach ($branchList as $key => $value) {
                            $selected = '';
                            if (isset($tableData)) {
                                if ($value['branch_id'] == $tableData->branch_id) {
                                    $selected = 'selected';
                                }
                            }
                            echo '<option value="' . $value['branch_id'] . '" ' . $selected . ' >' . $value['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="table_section">{{trans('backend/table.table_section')}}</label>
                    {{ Form::text('table_section', old('table_section'), ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/table.table_section'),"id"=>"table_section","name"=>"table_section"]) }}
                </div>
            </div>
            <div class="col-md-3 @if(isset($tableData)) {{ ($tableData->table_type=="2")? "display-none" : "" }}@endif" id="service_charge_sel">
                <div class="form-group">
                    <label for="table_service_charge">{{trans('backend/table.table_service_charge')}}</label>
                    {{ Form::number('table_service_charge', old('table_service_charge'), ["min"=>0,"class"=>"form-control form-control-sm","placeholder"=>trans('backend/table.table_service_charge'),"id"=>"table_service_charge","name"=>"table_service_charge","max"=>100]) }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 required">
                <div class="form-group">
                    <label for="table_capacity">{{trans('backend/table.table_capacity')}}</label>
                    {{ Form::number('table_capacity', old('table_capacity'), ["min"=>1,"required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/table.table_capacity'),"id"=>"table_capacity","name"=>"table_capacity"]) }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="table_type">{{trans('backend/table.table_type')}}</label>
                    <select name="table_type" id="table_type" class="form-control form-control-sm" onchange="showHideserviceCharge(this);" required>
                        <option value="1" @if(isset($tableData)) {{ ($tableData->table_type=="1")? "selected" : "" }}@endif>{{trans('backend/table.dinein')}}</option>
                        <option value="2" @if(isset($tableData)) {{ ($tableData->table_type=="2")? "selected" : "" }}@endif>{{trans('backend/table.takeaway')}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 required">
                <div class="form-group">
                    <label for="status">{{trans('backend/table.status')}}</label>
                    <select name="status" id="status" class="form-control form-control-sm" required>
                        <option value="1" @if(isset($tableData)) {{ ($tableData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                        <option value="0" @if(isset($tableData)) {{ ($tableData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                    </select>
                </div>
            </div>
            @if(isset($tableData))
                <div class="col-md-3 required">
                    <div class="form-group">
                        <label for="table_qr">{{trans('backend/table.table_qr')}}</label>
                        <a href="javascript:void(0);"
                           onclick="reGenerateQR(this,'{{$tableData->uuid}}');" class="text-left"><i
                                    class="fa fa-refresh text-success"></i></a>
                        {{ Form::text('table_qr', old('table_qr'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/table.table_qr'),"id"=>"table_qr","name"=>"table_qr"]) }}
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
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
            {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info btn-sm','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('backend/common.submit')])}}
            &nbsp;&nbsp;
            <a href="{{ route('admin.table.index')}}"
               class="btn btn-danger btn-sm">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>
{{ Form::close() }}
