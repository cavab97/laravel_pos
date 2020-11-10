@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/table.js')}}"></script>
    <script>
        $(function () {
            $('#table-list').DataTable();
            $('#table-list').DataTable().page.len(10).draw();
        });

    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/table.table')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/table.table')}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="custom-content content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/table.list_table')}}</h3>
                            @if($permission::checkActionPermission('add_table'))
                                <div class="float-right">
                                    <a href="{{route('admin.table.create')}}"
                                       class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body table-responsive">
                            <table id="table-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/table.name')}}</th>
                                    <th>{{trans('backend/table.table_type')}}</th>
                                    <th>{{trans('backend/branch.branch')}}</th>
                                    <th>{{trans('backend/table.table_capacity')}}</th>
                                    <th>{{trans('backend/common.status')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tableList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->table_name}}</td>
                                        <td>{{($value->table_type == 1) ? trans('backend/table.dinein'):trans('backend/table.takeaway')}}</td>
                                        <td>{{$value->branch_name}}</td>
                                        <td>{{$value->table_capacity}}</td>
                                        <td>
                                            @if($value->status == 1)
                                                <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                            @else
                                                <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($permission::checkActionPermission('edit_table'))
                                                <a href="{{route('admin.table.edit',$value->uuid)}}"
                                                   class="btn btn-sm btn-warning"><i
                                                            class="fa fa-edit text-white"></i></a>
                                            @endif
                                            @if($permission::checkActionPermission('delete_table'))
                                                <a href="javascript:void(0);"
                                                   onclick="deleteTable(this,'{{$value->uuid}}');"
                                                   class="btn btn-sm btn-danger"><i
                                                            class="fa fa-trash text-white"></i></a>

                                        @endif
                                        <!--
                                            <a href="javascript:void(0);"
                                               onclick="generateQR(this,'{{$value->uuid}}');"
                                               class="btn btn-sm btn-success">
                                                <i class="fa fa-qrcode text-warning"></i>
                                            </a>-->
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
