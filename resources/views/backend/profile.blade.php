@extends('backend.layout')

@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/profile.js')}}"></script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/common.profile')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/common.profile')}}</li>
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
                            <h3 class="card-title">
                                {{trans('backend/common.update_profile')}}</h3>
                        </div>
                        <div class="card-body">

                            {{ Form::model($userData, ['route' => ['admin.profile.post'], 'files' => true, 'role' => 'form', 'id'=>'frmProfile', 'method'=>'post', 'class'=>'form-horizontal' ]) }}

                            <div class="form-sec">

                                <div class="row">
                                    <div class="col-md-6 required">
                                        <div class="form-group">
                                            {{ Form::label('name',trans('backend/users.name')) }}
                                            {{ Form::text('name', old('name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/users.name'),"id"=>"name","name"=>"name"]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6 required">
                                        <div class="form-group">
                                            {{ Form::label('username',trans('backend/users.username')) }}
                                            {{ Form::text('username', old('username'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/users.username'),"id"=>"username","name"=>"username"]) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 required">
                                        <div class="form-group">
                                            {{ Form::label('email',trans('backend/common.email')) }}
                                            {{ Form::email('email', old('email'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/common.email'),"id"=>"email","name"=>"email"]) }}
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('password',trans('backend/users.password')) }}
                                            {{ Form::password('password', ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/users.password'),"id"=>"password","name"=>"password","maxlength" => "8","minlength" => "8"]) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('confirm_password',trans('backend/users.confirm_password')) }}
                                            {{ Form::password('confirm_password', ["class"=>"form-control form-control-sm", "placeholder"=>trans('backend/users.confirm_password'), "id"=>"confirm_password","maxlength" => "8","minlength" => "8"]) }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-header ui-sortable-handle" style="cursor: move;">
                                <div class="card-tools">
                                    <div class="form-group ">
                                        {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading', 'data-original-text'=>trans('backend/common.submit')])}}                                        &nbsp;&nbsp;

                                    </div>
                                </div>
                            </div>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection