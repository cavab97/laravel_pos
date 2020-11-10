@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/box.js')}}"></script>
    <script>
        $(function () {
            $('#box-list').DataTable();
            $('#box-list').DataTable().page.len(10).draw();
        });
    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/rac.box')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/rac.box')}}</li>
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
                            <h3 class="card-title">{{trans('backend/rac.list_box')}}</h3>
                            @if($permission::checkActionPermission('add_box'))
                                <div class="float-right">
                                    <button type="button" onclick="addBox(this)"
                                            class="btn btn-block btn-info">{{trans('backend/common.add')}}
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="card-body table-responsive">
                            <table id="box-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/rac.branch')}}</th>
                                    <th>{{trans('backend/rac.rac_name')}}</th>
                                    <th>{{trans('backend/rac.name')}}</th>
                                    <th>{{trans('backend/common.status')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($boxList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value['branch_name']}}</td>
                                        <td>{{$value['rac_name']}}</td>
                                        <td>{{$value['name']}}</td>
                                        <td>
                                            @if($value['status'] == 1)
                                                <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                            @else
                                                <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($permission::checkActionPermission('edit_box'))
                                                <a href="javascript:void(0);" class="btn btn-warning btn-sm"
                                                   onclick="editBox(this,'{{$value['uuid']}}')"><i
                                                            class="fa fa-edit text-white"></i></a>
                                            @endif
                                            @if($permission::checkActionPermission('delete_box'))
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                                   onclick="deleteBox(this,'{{$value['uuid']}}')"
                                                ><i class="fas fa-trash-alt text-white"></i></a>
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