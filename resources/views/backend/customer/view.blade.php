@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/customer.js')}}"></script>

@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/customer.customer')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.customer.index')}}">{{trans('backend/customer.customer')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{trans('backend/common.view')}}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <section class="custom-content content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/customer.view_customer')}}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">{{trans('backend/common.details')}}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/customer.name')}}</strong>:
                                                        <span>{{$customerData->name}}</span>
                                                    </p>


                                                    <p>
                                                        <strong>{{trans('backend/customer.mobile')}}</strong>:
                                                        <span>{{$customerData->mobile}}</span>
                                                    </p>

                                                    <p>
                                                        <strong>{{trans('backend/customer.profile')}}</strong>:
                                                        <span>
                                                            @if(!empty($customerData->profile))
                                                            <img src="{{asset($customerData->profile)}}"
                                                                   alt=""
                                                                   style="width: 100px;height: 100px;"/>
                                                            @else
                                                                <img src="{{asset(config('constants.default_user'))}}"
                                                                     alt=""
                                                                     style="width: 100px;height: 100px;"/>
                                                            @endif
                                                        </span>
                                                    </p>
                                                    @if(!empty($customerData->last_login))
                                                    <p>
                                                        <strong>{{trans('backend/users.last_login')}}</strong>:
                                                        <span>{{date('d-m-Y H:i:s',strtotime($customerData->last_login))}}</span>
                                                    </p>
                                                    @endif
                                                </div>

                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/customer.username')}}</strong>:
                                                        <span>{{$customerData->username}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/common.email')}}</strong>:
                                                        <span>{{$customerData->email}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/customer.status')}}</strong>:
                                                        @if($customerData->status == 1)
                                                            <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                                        @endif
                                                    </p>
                                                </div>

                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.updated_by')}}</strong>:
                                                        <span>{{$customerData->updated_name}}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.updated_at')}}</strong>:
                                                        <span>@if($customerData->updated_at){{date('d-m-Y H:i:s', strtotime($customerData->updated_at))}}@endif</span>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection