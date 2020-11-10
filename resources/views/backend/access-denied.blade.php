@extends('backend.layout')

@section('content')
    <div class="custom-content-wrapper content-wrapper">
        <div class="sub-content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="">
                <div class="container-fluid">
                    <div class="custom-content-header">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <h1 class="page-title">403 Error Page</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right bg-white mb-0 pl-0">
                                    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
                                    <li class="breadcrumb-item active">403 Error Page</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="company-listing-data-sec">
                        <div class="error-page bg-white p-5">
                            <div class="error-box">
                                <div class="error-body text-center">
                                    <h1 class="error-title text-danger">403</h1>
                                    <h3 class="text-uppercase error-subtitle">PAGE NOT FOUND !</h3>
                                    <p class="text-muted my-3">YOU SEEM TO BE TRYING TO FIND HIS WAY HOME</p>
                                    <a href="{{route('admin.home')}}"
                                       class="btn-backtohome btn-border-20 btn btn-rounded waves-effect waves-light m-b-40">Back
                                        to home</a></div>
                            </div>
                            <!-- /.error-content -->
                        </div>
                    </div>
                </div>


                <!-- /.error-page -->
            </section>
            <!-- /.content -->
        </div>
    </div>
    <!-- /.content-wrapper -->
@endsection
