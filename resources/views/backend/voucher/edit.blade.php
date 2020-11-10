<?php
$categoriesData = [];
if (isset($voucherData->voucher_categories)) {
    $categoriesData = explode(',', $voucherData->voucher_categories);
}
?>
@extends('backend.layout')

@section('styles')
    <link rel="stylesheet"
          href="{{asset('backend/plugins/select2/css/select2.min.css')}}">
@endsection
@section('scripts')
    <script src="{{asset('backend/plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/voucher.js')}}"></script>
    <script src="{{asset('backend/plugins/select2/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {

            $('.category_select2').select2();
            $('.products_select2').select2();

            $('#voucher_applicable_to').datetimepicker({
                format: 'DD-MM-YYYY',
                timePicker: false,
                todayHighlight: true,
                todayBtn: "linked",
                autoclose: true,
                useCurrent: false
            });
            $('#voucher_applicable_from').datetimepicker({
                format: 'DD-MM-YYYY',
                timePicker: false,
                todayHighlight: true,
                todayBtn: "linked",
                autoclose: true,
                useCurrent: false,
                //minDate: new Date(),
            });
            function pasteDate() {
                $('#voucher_applicable_from').datetimepicker('maxDate', moment({!! json_encode($voucher_applicable_to) !!}));
                $('#voucher_applicable_to').datetimepicker('minDate', moment({!! json_encode($voucher_applicable_from) !!}));
            }
            $("#voucher_applicable_from").on("change.datetimepicker", function (e) {
                $('#voucher_applicable_to').datetimepicker('minDate', e.date);
            });

            $("#voucher_applicable_to").on("change.datetimepicker", function (e) {
                $('#voucher_applicable_from').datetimepicker('maxDate', e.date);
            });
            window.onload = setTimeout(pasteDate(), 10);
        });
    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/voucher.voucher')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.voucher.index')}}">{{trans('backend/voucher.voucher')}}</a>
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
                            <h3 class="card-title">{{trans('backend/voucher.edit_voucher')}}</h3>
                        </div>
                        <div class="card-body">
                            @include('backend.voucher.form')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
