@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/cities.js')}}"></script>
    <script>
        $(function () {
            $('#city-list').DataTable();
            $('#city-list').DataTable().page.len(10).draw();
        });

    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/city.city')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/city.city')}}</li>
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
                            <h3 class="card-title">{{trans('backend/city.list_city')}}</h3>
                            @if($permission::checkActionPermission('add_cities'))
                                <div class="float-right">
                                    <a href="javascript:void(0);" onclick="addCity(this)"
                                       class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body table-responsive">
                            <table id="city-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/city.name')}}</th>
                                    <th>{{trans('backend/city.state')}}</th>
                                    @if($permission::checkActionPermission('edit_cities') || $permission::checkActionPermission('delete_cities'))
                                        <th>{{trans('backend/common.action')}}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cityList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->name}}</td>
                                        <td>{{$value->state_nm}}</td>
                                        @if($permission::checkActionPermission('edit_cities') || $permission::checkActionPermission('delete_cities'))
                                        <td>
                                            @if($permission::checkActionPermission('edit_cities'))
                                                <a href="javascript:void(0);" onclick="editCity(this,'{{$value->city_id}}')" class="btn btn-warning btn-sm"><i
                                                            class="fa fa-edit text-white"></i></a>
                                            @endif
                                            @if($permission::checkActionPermission('delete_cities'))
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                                   onclick="deleteCity(this,'{{$value->city_id}}');"><i
                                                            class="fa fa-trash text-white"></i></a>

                                            @endif
                                        </td>
                                        @endif
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
