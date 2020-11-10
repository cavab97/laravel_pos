@php
    $lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/setting.js')}}"></script>
    <script>
        $(function () {
            $('#setting-list').DataTable();
            $('#setting-list').DataTable().page.len(10).draw();
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
                        <h1>{{trans('backend/setting.settings')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/setting.settings')}}</li>
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
                            <h3 class="card-title">{{trans('backend/setting.list_settings')}}</h3>
                            {{--@if($permission::checkActionPermission('add_roles'))--}}
                            <div class="float-right">
                                <a href="javascript:void(0);" onclick="addSetting(this)" class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>

                            </div>
                            {{--@endif--}}
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="setting-list" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/setting.display_name')}}</th>
                                    <th>{{trans('backend/setting.namespace')}}</th>
                                    <th>{{trans('backend/setting.value')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($settingList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->display_name}}</td>
                                        <td>{{$value->namespace}}</td>
                                        <td>{{$value->value}}</td>
                                        <td>
                                            <a href="javascript:void(0);" onclick="editSetting(this,'{{$value->uuid}}')" class="btn btn-warning btn-sm"><i class="fa fa-edit text-warning text-white"></i></a>
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