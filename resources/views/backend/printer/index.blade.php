@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/printer.js')}}"></script>
    <script>
        $(function () {
            $('#printer-list').DataTable();
            $('#printer-list').DataTable().page.len(10).draw();
        });

    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/printer.printer')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                    href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/printer.printer')}}</li>
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
                            <h3 class="card-title">{{trans('backend/printer.list_printer')}}</h3>
                            @if($permission::checkActionPermission('add_printer'))
                                <div class="float-right">
                                    <a href="{{route('admin.printer.create')}}"
                                       class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body table-responsive">
                            <table id="printer-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/printer.branch')}}</th>
                                    <th>{{trans('backend/printer.name')}}</th>
                                    <th>{{trans('backend/printer.printer_ip')}}</th>
                                    <th>{{trans('backend/printer.printer_is_cashier')}}</th>
                                    <th>{{trans('backend/common.status')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($printerList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->branch_name}}</td>
                                        <td>{{$value->printer_name}}</td>
                                        <td>{{$value->printer_ip}}</td>
                                        <td>
                                            @if($value->printer_is_cashier == 1)
                                                <span
                                                    class="badge badge-success">{{trans('backend/common.yes')}}</span>
                                            @else
                                                <span
                                                    class="badge badge-info">{{trans('backend/common.no')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($value->status == 1)
                                                <span
                                                    class="badge badge-success">{{trans('backend/common.active')}}</span>
                                            @else
                                                <span
                                                    class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>

                                            @if($permission::checkActionPermission('edit_printer'))
                                                <a href="{{route('admin.printer.edit',$value->uuid)}}" class="btn btn-warning btn-sm"><i
                                                        class="fa fa-edit text-white"></i></a>
                                            @endif
                                            @if($permission::checkActionPermission('delete_printer'))
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                                   onclick="deletePrinter(this,'{{$value->uuid}}');"><i
                                                        class="fa fa-trash text-white"></i></a>
                                            @endif
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
