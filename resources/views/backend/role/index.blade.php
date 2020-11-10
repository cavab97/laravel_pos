@php
    $lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/roles.js')}}"></script>
    <script>
        $(function () {
            $('#role-list').DataTable();
            $('#role-list').DataTable().page.len(10).draw();
        });

    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/roles.roles')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/roles.roles')}}</li>
                        </ol>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="custom-content content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/roles.list_role')}}</h3>
                            {{--@if($permission::checkActionPermission('add_roles'))--}}
                                <div class="float-right">
                                    <a href="{{route('admin.roles.create')}}" class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>
                                    {{--<button type="button" onclick="addRole(this)"
                                            class="btn btn-block btn-info">{{trans('backend/common.add')}}
                                    </button>--}}
                                </div>
                            {{--@endif--}}
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="role-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/roles.role_name')}}</th>
                                    <th>{{trans('backend/roles.role_status')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($roleList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->role_name}}
                                        </td>
                                        <td>
                                            @if($value->role_status == 1)
                                                <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                            @else
                                                <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{--@if($permission::checkActionPermission('edit_roles'))--}}
                                                <a href="{{route('admin.roles.edit',$value->uuid)}}" class="btn btn-warning btn-sm"
                                                ><i class="fa fa-edit text-warning text-white"></i></a>
                                                {{--<a href="{{route('admin.roles.permissions',$value->uuid)}}"
                                                ><i class="fa fa-lock text-blue" aria-hidden="true"></i></a>--}}
                                            {{--@endif--}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.col -->
            </div>
        </section>
        <!-- Main content -->
    </div>
@endsection