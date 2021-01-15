@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/product.js')}}"></script>
    <script src="{{asset('backend/plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $('.category_select2').select2();
        $('#att_category_id').select2();
        $('#modifier_id').select2();
        $('.popper').popover({
            placement: 'top',
            container: 'div#content-body-div',
            //trigger: 'hover',
            html: true,
            content: function () {
                return $(this).next('.popper-content').html();
            }
        })
        .click(function(){
            if ($(this).hasClass('popover-show'))
                $(this).removeClass('popover-show');
            else
                $(this).addClass('popover-show');
        });
    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/product.product')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.product.index')}}">{{trans('backend/product.product')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{trans('backend/common.add')}}</li>
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
                            <h3 class="card-title">{{trans('backend/product.add_product')}}</h3>
                        </div>
                        <div class="card-body">
                            @include('backend.product.import-excel')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
