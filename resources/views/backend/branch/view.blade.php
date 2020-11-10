@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/branch.js')}}"></script>

@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/branch.branch')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.branch.index')}}">{{trans('backend/branch.branch')}}</a>
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
                            <h3 class="card-title">{{trans('backend/branch.view_branch')}}</h3>
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
                                                        <strong>{{trans('backend/branch.name')}}</strong>:
                                                        <span>{{$branchData->name}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.email')}}</strong>:
                                                        <span>{{$branchData->email}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.contact_no')}}</strong>:
                                                        <span>{{$branchData->contact_no}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.contact_person')}}</strong>:
                                                        <span>{{$branchData->contact_person}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.open_from')}}</strong>:
                                                        <span>{{$branchData->open_from}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.closed_on')}}</strong>:
                                                        <span>{{$branchData->closed_on}}</span>
                                                    </p>
                                                    @if(!empty($branchData->service_charge))
                                                        <p>
                                                            <strong>{{trans('backend/branch.service_charge')}}</strong>:
                                                            <span>{{$branchData->service_charge.'%'}}</span>
                                                        </p>
                                                    @endif


                                                    <p>
                                                        <strong>{{trans('backend/branch.branch_banner')}}</strong>:
                                                        <span><img src="{{asset($branchData->branch_banner)}}"
                                                                   alt=""
                                                                   style="width: 100px;height: 100px;"/></span>
                                                    </p>

                                                </div>

                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/branch.address')}}</strong>:
                                                        <span>{{$branchData->address}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.latitude')}}</strong>:
                                                        <span>{{$branchData->latitude}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.longitude')}}</strong>:
                                                        <span>{{$branchData->longitude}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.order_prefix')}}</strong>:
                                                        <span>{{$branchData->order_prefix}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.invoice_start')}}</strong>:
                                                        <span>{{$branchData->invoice_start}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/branch.status')}}</strong>:
                                                        @if($branchData->status == 1)
                                                            <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                                        @endif
                                                    </p>
													@if(!empty($branchData->branch_tax))
                                                        <p>
                                                            <strong>{{trans('backend/branch.tax')}}</strong>:
                                                            @foreach($branchData->branch_tax as $key => $value)
                                                                <span>{{$value['code'].' ( '.$value['rate'].'% ) '}}</span>
                                                            @endforeach
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.updated_by')}}</strong>:
                                                        <span>{{$branchData->updated_name}}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.updated_at')}}</strong>:
                                                        <span>@if($branchData->updated_at){{date('d-m-Y H:i:s', strtotime($branchData->updated_at))}}@endif</span>
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
