@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/payment_type.js')}}"></script>
    <script>
        $(function () {
            $('#payment-list').DataTable();
            $('#payment-list').DataTable().page.len(10).draw();
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
                        <h1>{{trans('backend/payment.payment_type')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/payment.payment_type')}}</li>
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
                            <h3 class="card-title">{{trans('backend/payment.list_payment_type')}}</h3>
                            @if($permission::checkActionPermission('add_payment_type'))
                                <div class="float-right">
                                    <button type="button" onclick="addPaymentType(this)"
                                            class="btn btn-block btn-info">{{trans('backend/common.add')}}
                                    </button>
                                </div>
                            @endif
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="payment-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th>{{trans('backend/payment.name')}}</th>
                                    <th>{{trans('backend/payment.icon')}}</th>
                                    <th>{{trans('backend/payment.status')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($paymentTypeList as $key => $value)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->name}}</td>
                                        <td><img src="{{asset($value->payment_icon)}}" width="50px"></td>
                                        <td>
                                            @if($value->status == 1)
                                                <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                            @else
                                                <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($permission::checkActionPermission('edit_payment_type'))
                                                <a href="javascript:void(0);" class="btnb btn-warning btn-sm"
                                                   onclick="editPaymentType(this,'{{$value->uuid}}')"><i
                                                            class="fa fa-edit text-white"></i></a>
                                            @endif
                                            @if($permission::checkActionPermission('delete_payment_type'))
                                                <a href="javascript:void(0);" class="btnb btn-danger btn-sm"
                                                   onclick="deletePaymentType(this,'{{$value->uuid}}');"><i
                                                            class="fa fa-trash text-white"></i></a>

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
