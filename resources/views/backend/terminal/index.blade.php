@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/terminal.js')}}"></script>
    <script>
        $(function () {
            $('#terminal-list').DataTable();
            $('#terminal-list').DataTable().page.len(10).draw();
        });

    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/terminal.terminal')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/terminal.terminal')}}</li>
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
                            <h3 class="card-title">{{trans('backend/terminal.list_terminal')}}</h3>
                            @if($permission::checkActionPermission('add_terminal'))
                                <div class="float-right">
                                    <a href="{{route('admin.terminal.create')}}"
                                       class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body table-responsive">
                            <table id="terminal-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/terminal.terminal_name')}}</th>
                                    <th>{{trans('backend/terminal.terminal_key')}}</th>
                                    <th>{{trans('backend/terminal.terminal_type')}}</th>
                                    <th>{{trans('backend/common.status')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($terminalList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->terminal_name}}</td>
                                        <td>{{$value->terminal_key}}</td>
                                        <td>
                                            @if($value->terminal_type == 1)
                                                <span class="badge badge-success">Cashier</span>
                                            @elseif($value->terminal_type == 2)
                                                <span class="badge badge-info">Waiter</span>
                                            @else
                                                <span class="badge badge-info">Attendance</span>
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

                                            @if($permission::checkActionPermission('edit_terminal'))
                                                <a href="{{route('admin.terminal.edit',$value->uuid)}}" class="btn btn-warning btn-sm"><i
                                                            class="fa fa-edit text-white"></i></a>
                                            @endif
                                            @if($permission::checkActionPermission('delete_terminal'))
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                                   onclick="deleteTerminal(this,'{{$value->uuid}}');"><i
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
