@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/branch.js')}}"></script>
    <script>
        $("#open_from").datetimepicker({
            format: 'HH:mm',
        }).on('dp.change', function (selected) {
            $("#closed_on").valid();
        });
        $("#closed_on").datetimepicker({
            format: 'HH:mm',
        }).on('dp.change', function (selected) {
            $("#open_from").valid();
        });
    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="content-header-row col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/branch.branch')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.branch.index')}}">{{trans('backend/branch.branch')}}</a>
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
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/branch.edit_branch')}}</h3>
                        </div>
                        <div class="card-body">
                            @include('backend.branch.form')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection