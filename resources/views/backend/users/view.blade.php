@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/users.js')}}"></script>

@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/users.users')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.users.index')}}">{{trans('backend/users.users')}}</a>
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
                            <h3 class="card-title">{{trans('backend/users.view_user')}}</h3>
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
                                                        <strong>{{trans('backend/users.name')}}</strong>:
                                                        <span>{{$userData->name}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/users.username')}}</strong>:
                                                        <span>{{$userData->username}}</span>
                                                    </p>

                                                    <p>
                                                        <strong>{{trans('backend/users.mobile')}}</strong>:
                                                        <span>{{$userData->mobile}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/users.country_code')}}</strong>:
                                                        <span>{{$userData->country_code}}</span>
                                                    </p>

                                                    @if(!empty($customerData->last_login))
                                                    <p>
                                                        <strong>{{trans('backend/users.last_login')}}</strong>:
                                                        <span>{{date('d-m-Y H:i:s',strtotime($userData->last_login))}}</span>
                                                    </p>
                                                    @endif

                                                    <p>
                                                        <strong>{{trans('backend/users.profile')}}</strong>:
                                                        <span>
                                                            @if(!empty($userData->profile))
                                                            <img src="{{asset($userData->profile)}}"
                                                                   alt=""
                                                                   style="width: 100px;height: 100px;"/>
                                                            @else
                                                                <img src="{{asset(config('constants.default_user'))}}"
                                                                     alt=""
                                                                     style="width: 100px;height: 100px;"/>
                                                            @endif
                                                        </span>
                                                    </p>

                                                </div>

                                                <div class="col-md-6">
                                                   {{-- <p>
                                                        <strong>{{trans('backend/users.user_pin')}}</strong>:
                                                        <span>{{$userData->user_pin}}</span>
                                                    </p>--}}
                                                    <p>
                                                        <strong>{{trans('backend/users.commision_percent')}}</strong>:
                                                        <span>{{$userData->commision_percent}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/roles.role')}}</strong>:
                                                        <span>{{$userData->role_name}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/users.assign_branch')}}</strong>:
                                                    <div>
                                                        @if(isset($userData->userBranch))
                                                            @foreach($userData->userBranch as $value)
                                                                <a href="{{route('admin.branch.show',$value->uuid)}}"><span class="badge badge-success mr-1">{{$value->name}}</span></a>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/users.status')}}</strong>:
                                                        @if($userData->status == 1)
                                                            <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                                        @endif
                                                    </p>
                                                </div>

                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.updated_by')}}</strong>:
                                                        <span>{{$userData->updated_name}}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.updated_at')}}</strong>:
                                                        <span>@if($userData->updated_at){{date('d-m-Y H:i:s', strtotime($userData->updated_at))}}@endif</span>
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
