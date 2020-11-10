@if(isset($printerData))
    {{ Form::model($printerData, ['route' => ['admin.printer.update',$printerData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmPrinter', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('printer_id', $printerData->printer_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.printer.store', 'files' => true, 'role' => 'form', 'id'=>'frmPrinter', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('branch_id',trans('backend/printer.branch')) }}
                    <select id="branch_id" name="branch_id" class="form-control form-control-sm" required>
                        <option value="">{{trans('backend/printer.select_branch')}}</option>
                        <?php
                        foreach ($branchList as $key => $value) {
                            $selected = '';
                            if (isset($printerData)) {
                                if ($value['branch_id'] == $printerData->branch_id) {
                                    $selected = 'selected';
                                }
                            }
                            echo '<option value="' . $value['branch_id'] . '" ' . $selected . ' >' . $value['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 required">
                <div class="form-group">
                    {{ Form::label('printer_name',trans('backend/printer.name')) }}
                    {{ Form::text('printer_name', old('printer_name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/printer.name'),"id"=>"printer_name","name"=>"printer_name"]) }}
                </div>
            </div>
            <div class="col-md-4 required">
                <div class="form-group">
                    {{ Form::label('printer_ip',trans('backend/printer.printer_ip')) }}
                    {{ Form::text('printer_ip', old('printer_ip'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/printer.printer_ip'),"id"=>"printer_ip","name"=>"printer_ip"]) }}
                </div>
            </div>
            <div class="col-md-4 required">
                <div class="form-group">
                    <label for="status">{{trans('backend/category.status')}}</label>
                    <select name="status" id="status" class="form-control form-control-sm" required>
                        <option value="1" @if(isset($printerData)) {{ ($printerData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                        <option value="0" @if(isset($printerData)) {{ ($printerData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                    </select>
                </div>
            </div>
            <?php
            $se_checked = 'checked';
            if (isset($printerData->printer_is_cashier)) {
                if ($printerData->printer_is_cashier == 1) {
                    $se_checked = 'checked';
                } else {
                    $se_checked = '';
                }
            }?>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="printer_is_cashier">
                        {{trans('backend/printer.printer_is_cashier')}}</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="printer_is_cashier" {{$se_checked}} value="1">
                        <span class="slider round"></span>
                    </label>
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
            {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('backend/common.submit')])}}
            &nbsp;&nbsp;
            <a href="{{ route('admin.printer.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
