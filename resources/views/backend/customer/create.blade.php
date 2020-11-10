@extends('backend.layout')
@php
    $generatePin = \App\Models\Helper::generatePin(6);
@endphp
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/customer.js')}}"></script>
@endsection
<style>
    .nav.nav-tabs > li.disabled { pointer-events: none; }
</style>
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/customer.customers')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.customer.index')}}">{{trans('backend/customer.customers')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{trans('backend/common.add')}}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <section class="custom-content content">
                    <div class="card card-secondary">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="nav-item"><a href="#basicInfo" data-toggle="tab" class="nav-link active" id="cus_info">{{trans('backend/customer.customer_details')}}</a></li>
                                <li class="nav-item disabled"><a href="#addressArea" data-toggle="tab" class="nav-link" id="cus_address">{{trans('backend/customer.customer_address')}}</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active " id="basicInfo">
                                    <div class="card-header">
                                        <h3 class="card-title">{{trans('backend/customer.add_customer')}}</h3>
                                    </div>
                                    @include('backend.customer.form')
                                </div>
                                <div class="tab-pane" id="addressArea">

                                    @include('backend.customer.addressform')
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--<div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-plus-square-o"></i> {{trans('backend/customer.add_customer')}}</h3>
                        </div>
                        <div class="card-body">
                            @include('backend.customer.form')
                        </div>
                    </div>--}}

        </section>
    </div>
@endsection