@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/users.js')}}"></script>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-md-6">
                        <h1>{{trans('backend/users.users')}}</h1>
                    </div>
                    <div class="col-md-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.users.index')}}">{{trans('backend/users.users')}}</a></li>
                            <li class="breadcrumb-item active">Permissions</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="custom-content content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-dark card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                <li class="pt-2 px-3"><h3
                                            class="card-title">{{$userData->name}} </h3>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                       href="#custom-tabs-one-home"
                                       role="tab" aria-controls="custom-tabs-one-home"
                                       aria-selected="true">User Permission</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                       href="#custom-tabs-one-profile"
                                       role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">POS
                                        Permission</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <form action="{{route('admin.users.permissions.store',$userData->uuid)}}" method="post"
                                  id="frmPermission">
                                {{csrf_field()}}
                                <div class="tab-content" id="custom-tabs-one-tabContent">

                                    <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                                         aria-labelledby="custom-tabs-one-home-tab">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th># <span class="float-right icheck-primary">Checked All <input type="checkbox" name="checkedAll" id="checkedAll"><label for="checkedAll"></label></span></th>
                                                            <?php
                                                            foreach ($userData->actionList as $action) {
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
                                                        @foreach($userData->moduleList as $m => $module)
                                                            @if($module != 'roles')
                                                                <tr>
                                                                    <td>
                                                                        <strong>{{ucwords(str_replace('_',' ',$module))}}</strong>
                                                                    </td>
                                                                    @foreach($userData->actionList as $a => $action)
                                                                        <td class="text-center">
                                                                            <?php
                                                                            $permissionName = $action . "_" . $module;
                                                                            if (in_array($permissionName, $userData->permissionList)) {
                                                                                $checked = '';
                                                                                if (in_array($permissionName, $userData->allowPermission)) {
                                                                                    $checked = 'checked';
                                                                                }
                                                                                $permissionId = "permission_" . $m . "_" . $a;
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

                                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel"
                                         aria-labelledby="custom-tabs-one-profile-tab">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th># <span class="float-right icheck-primary">Checked All <input type="checkbox" name="checkedPOSAll" id="checkedPOSAll"><label for="checkedPOSAll"></label></span></th>
                                                            <?php
                                                            foreach ($userData->posactionList as $action) {
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
                                                        @foreach($userData->posmoduleList as $m => $module)
                                                            @if($module != 'roles')
                                                                <tr>
                                                                    <td>
                                                                        <strong>{{ucwords(str_replace('_',' ',$module))}}</strong>
                                                                    </td>
                                                                    @foreach($userData->posactionList as $a => $action)
                                                                        <td class="text-center">
                                                                            <?php
                                                                            $permissionName = $action . "_" . $module;
                                                                            if (in_array($permissionName, $userData->posPermissionList)) {
                                                                                $checked = '';
                                                                                if (in_array($permissionName, $userData->allowPosPermission)) {
                                                                                    $checked = 'checked';
                                                                                }
                                                                                $permissionId = "permission_" . $m . "_" . $a;
                                                                                echo "<div class='icheck-primary d-inline'><input type='checkbox' class='pos_check' id='pos_$permissionId' name='pos_permissions[]' $checked value='$permissionName'/><label for='pos_$permissionId'></label></div>";
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
                                            <!--  -->
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success  float-right"
                                            id="btnSubmit" data-text="Submit"
                                            data-loading-text="<span class='spinner-border spinner-border-sm'></span> Loading">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
