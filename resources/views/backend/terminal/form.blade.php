@if(isset($terminalData))
    {{ Form::model($terminalData, ['route' => ['admin.terminal.update',$terminalData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmTerminal', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('terminal_id', $terminalData->terminal_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.terminal.store', 'files' => true, 'role' => 'form', 'id'=>'frmTerminal', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

        <div class="row">
            <div class="col-md-4 required">
                <div class="form-group">
                    {{ Form::label('terminal_name',trans('backend/terminal.terminal_name')) }}
                    {{ Form::text('terminal_name', old('terminal_name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/terminal.terminal_name'),"id"=>"terminal_name"]) }}
                </div>
            </div>
            @if(isset($terminalData))
                <div class="col-md-4 required">
                    <div class="form-group">
                        {{ Form::label('terminal_device_id',trans('backend/terminal.terminal_device_id')) }}
                    <a href="javascript:void(0);"
                       onclick="reset_device_id(this);" class="text-left"><i
                                class="fa fa-refresh text-success"></i></a>
                        {{ Form::text('terminal_device_id', old('terminal_device_id'), ["readonly","required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/terminal.terminal_device_id'),"id"=>"terminal_device_id"]) }}
                    </div>
                </div>
            @endif
            <div class="col-md-4 required">
                <div class="form-group">
                    {{ Form::label('terminal_key',trans('backend/terminal.terminal_key')) }}
                    <a href="javascript:void(0);"
                       onclick="reGenerateKey(this);" class="text-left"><i
                                class="fa fa-refresh text-success"></i></a>
                    {{ Form::text('terminal_key', old('terminal_key'), ["readonly","required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/terminal.terminal_key'),"id"=>"terminal_key"]) }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 required">
                <div class="form-group">
                    {{ Form::label('branch_id',trans('backend/branch.branch')) }}
                    <select name="branch_id" id="branch_id"
                            class="form-control"
                            data-placeholder="{{trans('backend/branch.branch')}}">
                        @if(isset($branchList))
                            @foreach($branchList as $value)
                                @php
                                    $selected = '';
                                    if (!empty($terminalData) && $value->branch_id == $terminalData->branch_id) {
                                        $selected = 'selected';
                                    }
                                @endphp
                                <option value="{{$value->branch_id}}" {{$selected}}>{{$value->name}}</option>
                            @endforeach
                        @endif
                        @if(isset($branchList) && count($branchList) == 0)
                            <option disabled="true">No branch available</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-4 required">
                <div class="form-group">
                    {{ Form::label('terminal_type',trans('backend/terminal.terminal_type')) }}
                    {!! Form::select('terminal_type',['1' => 'Cashier','2'=>'Waiter','Attendance'],null, ["required",'class'=>'form-control form-control-sm','placeholder'=>trans('backend/terminal.select_type')]) !!}

                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="terminal_is_mother">{{trans('backend/terminal.terminal_is_mother')}}</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="terminal_is_mother" value="1"
                               @if(isset($terminalData) && $terminalData->terminal_is_mother == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 required">
                <div class="form-group">
                    {{ Form::label('status',trans('backend/terminal.status')) }}
                    <select name="status" id="status" class="form-control form-control-sm" required>
                        <option value="1" @if(isset($terminalData)) {{ ($terminalData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                        <option value="0" @if(isset($terminalData)) {{ ($terminalData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
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
            <a href="{{ route('admin.terminal.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>
<script>
function reset_device_id(currentEle) {
    let ele = currentEle.nextElementSibling;
    ele.value = '';
    ele.required = false;
}
</script>
{{ Form::close() }}
