@php
    $permission = new \App\Models\Permissions();
    $lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/modifier.js')}}"></script>
    <script>
        $(function () {
            $('#modifier-list').DataTable();
            $('#modifier-list').DataTable().page.len(10).draw();
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
                        <h1>{{trans('backend/modifier.modifier')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/modifier.modifier')}}</li>
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
                            <h3 class="card-title">{{trans('backend/modifier.list_modifier')}}</h3>
                            @if($permission::checkActionPermission('add_modifier'))
                            <div class="float-right">
                                <button type="button" onclick="addModifier(this)"
                                        class="btn btn-block btn-info">{{trans('backend/common.add')}}
                                </button>
                            </div>
                            @endif
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="modifier-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/modifier.name')}}</th>
                                    <th>{{trans('backend/modifier.is_default')}}</th>
                                    <th>{{trans('backend/common.status')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($modifierList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->name}}</td>
                                        <td>
                                            @if($value->is_default == 1)
                                                <span class="badge badge-info rounded">{{trans('backend/common.yes_lable')}}</span>
                                            @else
                                                <span class="badge badge-secondary rounded">{{trans('backend/common.no_lable')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($value->status == 1)
                                                <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                            @else
                                                <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($permission::checkActionPermission('edit_modifier'))
                                                <a href="javascript:void(0);" class="btn btn-warning btn-sm"
                                                   onclick="editModifier(this,'{{$value->uuid}}')"><i
                                                            class="fa fa-edit text-white"></i></a>
                                            @endif
                                            @if($permission::checkActionPermission('delete_modifier'))
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                                   onclick="deleteModifier(this,'{{$value->uuid}}')"
                                                ><i class="fas fa-trash-alt text-white"></i></a>
                                            @endif
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