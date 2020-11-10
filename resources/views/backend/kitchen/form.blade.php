@if(isset($kitchenData))
    {{ Form::model($kitchenData, ['route' => ['admin.kitchen.update',$kitchenData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmKitchen', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('kitchen_id', $kitchenData->kitchen_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.kitchen.store', 'files' => true, 'role' => 'form', 'id'=>'frmKitchen', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

        <div class="row">
            <div class="col-md-4 required">
                <div class="form-group">
                    {{ Form::label('kitchen_name',trans('backend/kitchen.name')) }}
                    {{ Form::text('kitchen_name', old('kitchen_name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/kitchen.name'),"id"=>"kitchen_name","name"=>"kitchen_name"]) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('branch_id',trans('backend/branch.branch')) }}
                    <select id="branch_id" name="branch_id" class="form-control form-control-sm" required
                            onclick="getPrinter(this)">
                        <option value="">{{trans('backend/kitchen.select_branch')}}</option>
                        <?php
                        foreach ($branchList as $key => $value) {
                            $selected = '';
                            if (isset($kitchenData)) {
                                if ($value['branch_id'] == $kitchenData->branch_id) {
                                    $selected = 'selected';
                                }
                            }
                            echo '<option value="' . $value['branch_id'] . '" ' . $selected . ' >' . $value['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('kitchen_printer_id',trans('backend/printer.printer')) }}
                    <select id="kitchen_printer_id" name="kitchen_printer_id" class="form-control form-control-sm"
                            required>
                        <option value="">{{trans('backend/kitchen.select_printer')}}</option>
                        <?php
                        if (isset($printerList) && !empty($printerList)) {
                            foreach ($printerList as $key => $value) {
                                $selected = '';
                                if (isset($kitchenData)) {
                                    if ($value['printer_id'] == $kitchenData->kitchen_printer_id) {
                                        $selected = 'selected';
                                    }
                                }
                                echo '<option value="' . $value['printer_id'] . '" ' . $selected . ' >' . $value['printer_name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 required">
                <div class="form-group">
                    <label for="status">{{trans('backend/category.status')}}</label>
                    <select name="status" id="status" class="form-control form-control-sm" required>
                        <option value="1" @if(isset($kitchenData)) {{ ($kitchenData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                        <option value="0" @if(isset($kitchenData)) {{ ($kitchenData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
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
            {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('backend/common.submit')])}}
            &nbsp;&nbsp;
            <a href="{{ route('admin.kitchen.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
