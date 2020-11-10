@extends('backend.layout')
@php
    $lang = \App\Models\Languages::getBackLang();
@endphp
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/roles.js')}}"></script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/roles.roles')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.roles.index')}}">{{trans('backend/roles.roles')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{trans('backend/common.edit')}}</li>
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
                            <h3 class="card-title">
                                <i class="fas fa-edit"></i>
                                {{trans('backend/roles.edit_role')}}</h3>
                        </div>
                        <div class="card-body">
                            @include('backend.role.form')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection