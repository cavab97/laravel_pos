@extends('backend.layout')

@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/terminal.js')}}"></script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/terminal.terminal')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.terminal.index')}}">{{trans('backend/terminal.terminal')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{trans('backend/common.edit')}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="custom-content content">
            <div class="row">
                <div class="col-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/terminal.edit_terminal')}}</h3>
                        </div>
                        <div class="card-body">
                            @include('backend.terminal.form')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
