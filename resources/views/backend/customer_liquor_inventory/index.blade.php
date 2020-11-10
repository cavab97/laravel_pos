@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/customer_liquor_inventory.js')}}"></script>
    <script>
        $(function () {
            $('#inventory-list').DataTable();
            $('#inventory-list').DataTable().page.len(10).draw();
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
                        <h1>{{trans('backend/wine_store_management.wine_store_management')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/wine_store_management.wine_store_management')}}</li>
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
                            <h3 class="card-title">{{trans('backend/wine_store_management.list_customer_liquor_inventory')}}</h3>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="inventory-list" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/wine_store_management.customer_name')}}</th>
                                    <th>{{trans('backend/wine_store_management.product_name')}}</th>
                                    <th>{{trans('backend/inventory.branch_name')}}</th>
                                    <th>{{trans('backend/inventory.qty')}}</th>
                                    <th>{{trans('backend/wine_store_management.expired_on')}}</th>
                                    <th id="status">{{trans('backend/common.status')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($inventoryList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->customer_name}}</td>
                                        <td>{{$value->product_name}}</td>
                                        <td>{{$value->branch_name}}</td>
                                        <td>{{$value->qty}}</td>
                                        <td>{{$value->cl_expired_on}}</td>
                                        <td>
                                            @if($value->status == 1)
                                                <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                            @else
                                                <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($permission::checkActionPermission('view_wine_store_management'))
                                                <a href="{{route('admin.wine_store_management.show',$value->uuid)}}" class="btn btn-info btn-sm">
                                                    <i class="fa fa-eye text-white"></i></a>
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