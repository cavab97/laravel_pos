@if(isset($roleData))
    {{ Form::model($roleData, ['route' => ['admin.roles.update',$roleData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmRole', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('role_id', $roleData->role_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.roles.store', 'files' => true, 'role' => 'form', 'id'=>'frmRole', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif

<div class="form-sec">
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('role_name',trans('backend/roles.role_name')) }}
                {{ Form::text('role_name', old('role_name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/roles.role_name'),"id"=>"role_name","name"=>"role_name"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                <label for="role_status">{{trans('backend/roles.role_status')}}</label>
                <select name="role_status" id="role_status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($roleData)) {{ ($roleData->role_status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($roleData)) {{ ($roleData->role_status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>
    </div>

</div>

<div class="card card-secondary card-tabs">
    <div class="card-header ui-sortable-handle p-0 pt-1" style="cursor: move;">
        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
            {{--<li class="pt-2 px-3"><h3 class="card-title">Permissions</h3></li>--}}
            <li class="nav-item">
                <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home"
                   role="tab" aria-controls="custom-tabs-one-home"
                   aria-selected="true">{{trans('backend/roles.role_permission')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile"
                   role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">POS Permission</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="custom-tabs-one-tabContent">
            <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                 aria-labelledby="custom-tabs-one-home-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/roles.permission')}}
                                        <span class="float-right icheck-primary">Checked All <input type="checkbox" name="checkedAll" id="checkedAll"><label for="checkedAll"></label></span>
                                    </th>
                                    <?php
                                    foreach ($rolePermission['actionList'] as $action) {
                                        $name = $action;
                                        if ($action == 'browse') {
                                            $name = 'List';
                                        } elseif ($action == 'read') {
                                            $name = 'View';
                                        }
                                        echo "<th class='text-center'>" . ucfirst($name) . "</th>";
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rolePermission['moduleList'] as $m => $module)
                                    @if($module != 'roles')
                                        <tr>
                                            <td>
                                                <strong>{{ucwords(str_replace('_',' ',$module))}}</strong>
                                            </td>
                                            @foreach($rolePermission['actionList'] as $a => $action)
                                                <td class="text-center">
                                                    <?php
                                                    $permissionName = $action . "_" . $module;
                                                    if (in_array($permissionName, $rolePermission['allPermissionList'])) {
                                                        $checked = '';
                                                        if (in_array($permissionName, $rolePermission['allowPermission'])) {
                                                            $checked = 'checked';
                                                        }
                                                        $permissionId = "permission_" . $m . "_" . $a;
                                                        /*echo "<input type='checkbox' name='permissions[]' class='flat-red' $checked value='$permissionName'/>";*/
                                                        echo "<div class='icheck-primary d-inline'><input type='checkbox' class='sub_chk' id='$permissionId' name='permissions[]' $checked value='$permissionName'/><label for='$permissionId'></label></div>";
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/roles.permission')}}
                                        <span class="float-right icheck-primary">Checked All <input type="checkbox" name="checkedPOSAll" id="checkedPOSAll"><label for="checkedPOSAll"></label></span>
                                    </th>
                                    <?php
                                    foreach ($posRolePermission['actionList'] as $action) {
                                        $name = $action;
                                        if ($action == 'browse') {
                                            $name = 'List';
                                        } elseif ($action == 'read') {
                                            $name = 'View';
                                        }
                                        echo "<th class='text-center'>" . ucfirst($name) . "</th>";
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($posRolePermission['moduleList'] as $m => $module)
                                    @if($module != 'roles')
                                        <tr>
                                            <td>
                                                <strong>{{ucwords(str_replace('_',' ',$module))}}</strong>
                                            </td>
                                            @foreach($posRolePermission['actionList'] as $a => $action)
                                                <td class="text-center">
                                                    <?php
                                                    $permissionName = $module;//$action . "_" . $module;
                                                        $checked = '';
                                                        if (in_array($permissionName, $posRolePermission['allowPOSPermission'])) {
                                                            $checked = 'checked';
                                                        }
                                                        $permissionId = "permission_" . $m . "_" . $a;
                                                        echo "<div class='icheck-primary d-inline'><input type='checkbox' class='pos_check' id='pos_$permissionId' name='pos_permissions[]' $checked value='$permissionName'/><label for='pos_$permissionId'></label></div>";
                                                    if (in_array($permissionName, $posRolePermission['allPOSPermissionList'])) {
                                                    } else {
                                                        //echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
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
            <a href="{{ route('admin.roles.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}