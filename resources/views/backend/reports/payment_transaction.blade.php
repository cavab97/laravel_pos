@php
$permission = new \App\Models\Permissions();
$lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('styles')
<link rel="stylesheet" href="{{asset('backend/plugins/select2/css/select2.min.css')}}">
    <style>
        .table-top-bar {
            margin-top: 1rem !important;
        }
        .table-top-bar > div > div > label {
            margin: auto 0;
        }
    </style>
@endsection
@section('scripts')
<script src="{{asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/plugins/select2/js/select2.full.min.js')}}"></script>
<script>
    $(function() {
        var oTable = $('#shift-list').dataTable({
            "bStateSave": false,
            "processing": true,
            "serverSide": true,
            "bProcessing": true,
            "iDisplayLength": 10,
            "bServerSide": true,
            "bPaginate": true,
            "sAjaxSource": adminUrl + '/reports-payment-paginate',
            "lengthChange": true,
            "fnServerParams": function(aoData) {
                var acolumns = this.fnSettings().aoColumns,
                    columns = [];
                $.each(acolumns, function(i, item) {
                    columns.push(item.data);
                });
                aoData.push({
                    name: 'columns',
                    value: columns
                });
                aoData.push({
                    name: 'terminal_id',
                    value: $('#terminal_id').val()
                });
                aoData.push({
                    name: 'branch_id',
                    value: $('#branch_id').val()
                });
                aoData.push({
                    name: 'from_date',
                    value: $('#from_date').val()
                });
                aoData.push({
                    name: 'to_date',
                    value: $('#to_date').val()
                });

            },    
            dom: '<"row"<"ml-auto d-inline-flex"B>><"row table-top-bar"<l><"ml-auto"f>>tip',
            buttons: [
                {
                    extend: 'copy',
                    title : 'Payment_Transaction'
                },
                    
                {
                    extend: 'csv',
                    title : 'Payment_Transaction'
                },    
                {
                    extend: 'excel',
                    title : 'Payment_Transaction'
                },
                {
                    extend: 'pdf',
                    title : 'Payment_Transaction'
                },
            ],
            "columns": [{
                    data: 'id'
                },
                { data: 'payment_option' },
                { data: "sales_amount" },
                { data: "total_sales_transaction" },
                { data: "refunds_amount" },
                { data: "refunds_transaction" },
                { data: "cancel_amount" },
                { data: "cancel_transaction" },
            ],
            "ordering": false,
            //"order": [[1, "desc"]],
            "oLanguage": {
                "sLengthMenu": "Show _MENU_ entries"
            },
            "fnInitComplete": function() {},
            'fnServerData': function(sSource, aoData, fnCallback) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    },
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "fnDrawCallback": function() {
                $('body').css('min-height', ($('#shift-list tr').length * 50) + 200);
                $(window).trigger('resize');
                /* if ($('#branch_id').val().length == 1) {
                    oTable.fnSetColumnVis(3).visible( false ).adjust(false)
                    //oTable.column(3).visible(false);
                    oTable.columns.adjust().draw();
                } */
            },
            "columnDefs": [{
                "render": function(data, type, row, meta) {
                    return [
                        parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1
                    ].join('');
                },
                "targets": $('#shift-list th#id').index(),
                "orderable": false,
                "sortable": false
            }, {
                "render": function(data, type, row) {
                    return row.total_sales_transaction ?? 0;
                },
                "targets": $('#shift-list th#total_sales_transaction').index(),
            }, {
                "render": function(data, type, row) {
                    if (row.sales_amount < 0) {
                        return (0).toFixed(2);
                    } else {
                        return (row.sales_amount ?? 0).toFixed(2);
                    }
                },
                "targets": $('#shift-list th#sales_amount').index(),
            }, {
                "render": function(data, type, row) {
                    if (row.refunds_amount) {
                        return (row.refunds_amount * -1).toFixed(2)
                    } else {
                        return (0).toFixed(2);
                    }
                },
                "targets": $('#shift-list th#refunds_amount').index(),
            }, {
                "render": function(data, type, row) {
                    return row.refunds_transaction ?? 0;
                },
                "targets": $('#shift-list th#refunds_transaction').index(),
            }, {
                "render": function(data, type, row) {
                    return (row.cancel_amount ?? 0).toFixed(2);
                },
                "targets": $('#shift-list th#cancel_amount').index(),
            }, {
                "render": function(data, type, row) {
                    return row.cancel_transaction ?? 0;
                },
                "targets": $('#shift-list th#cancel_transaction').index(),
            }, {
                "render": function(data, type, row) {
                    let total_net = 0.00;
                    let sales_amount = row.sales_amount ?? 0;
                    let refunds_amount = row.refunds_amount ?? 0;
                    let cancel_amount = row.cancel_amount ?? 0;
                    total_net = sales_amount - refunds_amount - cancel_amount;
                    return total_net.toFixed(2);
                },
                "targets": $('#shift-list th#net').index(),
            }, {
                "render": function(data, type, row) {
                    let count_sales = row.total_sales_transaction ?? 0;
                    let count_cancel = row.cancel_transaction ?? 0;
                    let count_refund = row.refunds_transaction ?? 0;
                    return count_sales + count_refund + count_cancel;
                },
                "targets": $('#shift-list th#total_transaction').index(),
            }, ]
        });
        $('#btnSubmit').click(function() {
            oTable.fnDraw();
        });

        $('#to_date').datetimepicker({
            format: 'DD-MM-YYYY',
            timePicker: false,
            todayHighlight: true,
            todayBtn: "linked",
            autoclose: true,
            maxDate: $.now()
        });
        let dateTime = new Date();
        var firstDate = new Date('01/' + dateTime.getMonth() + 1 + '/' + dateTime.getFullYear());
        $('#from_date').datetimepicker({
            format: 'DD-MM-YYYY',
            timePicker: false,
            todayHighlight: true,
            todayBtn: "linked",
            autoclose: true,
            defaultDate: firstDate,
            maxDate: $.now()
        })
        $("#from_date").on("change.datetimepicker", function(e) {
            $("#from_date").val($.datepicker.formatDate('yy-mm-dd', new Date(e.date)));
            //$('#to_date').datetimepicker('minDate', e.date);
        });

        $("#to_date").on("change.datetimepicker", function(e) {
            $("#to_date").val($.datepicker.formatDate('yy-mm-dd', new Date(e.date)));
            $('#from_date').datetimepicker('maxDate', e.date);
        });

        $('#branch_id').select2({
            placeholder: "Select a Branch",
            allowClear: true
        });
    });

    function pressEnter(event) {
        if (event.keyCode === 13 || event.type == 'change') {
            $("#btnSubmit").click();
        }
    }
</script>
<style>
    ul.select2-selection__rendered {
        padding: .25rem !important;
        margin-top: 0 !important;
        vertical-align: middle;
    }

    .select2>span.selection>span>ul>li>button {
        border-right: none !important;
    }

    .select2-container>span.selection>span {
        padding-bottom: 0;
        /* padding: .25rem; */
    }

    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        content: "&#215;";
        color: blue;
        line-height: .8;
        font-size: 22px;
        margin: 0;
        margin-right: -.25rem;
        padding: .5rem .38rem;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__clear:hover {
        background: #bbb;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="content-header-row row mb-2">
                <div class="col-sm-6">
                    <h1>{{trans('backend/common.payment_transaction')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                        <li class="breadcrumb-item active">{{trans('backend/common.payment_transaction')}}</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="custom-content content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title">{{trans('backend/common.list_trasaction')}}</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row mb-2">
                            @if(isset($availableBranch))
                            <div class="col-md-3 mt-2 select2-purple">
                                <select name="branch_id[]" id="branch_id" multiple class="form-control form-control-sm select2 js-example-basic-multiple js-states" data-placeholder="{{trans('backend/reports.select_branch')}}" required>
                                    @foreach($availableBranch as $key => $value)
                                    <option value="{{$key}}" selected>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            @if(isset($terminalList))
                            <div class="col-md-3 mt-2">
                                <select class="form-control form-control-sm" id="terminal_id" name="terminal_id">
                                    <option value="">{{trans('backend/common.select_terminal')}}</option>
                                    @foreach($terminalList as $value)
                                    <option value="{{$value->terminal_id}}">{{$value->terminal_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-3 mt-2">
                                <div class="input-group date" id="from_date" data-target-input="nearest">
                                    {{ Form::text('from_date', old('from_date'), ["required","class"=>"form-control form-control-sm datetimepicker-input","placeholder"=>trans('backend/common.from_date'),"id"=>"from_date","data-target"=>"#from_date"]) }}
                                    <div class="input-group-append" data-target="#from_date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-2">
                                <div class="input-group date" id="to_date" data-target-input="nearest">
                                    {{ Form::text('to_date', old('to_date'), ["required","class"=>"form-control form-control-sm datetimepicker-input","placeholder"=>trans('backend/common.to_date'),"id"=>"to_date","data-target"=>"#to_date"]) }}
                                    <div class="input-group-append" data-target="#to_date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 mt-2">
                                <button type="button" id="btnSubmit" name="submit" class="btn btn btn-warning btn-sm">Filter
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="shift-list" class="table table-bordered table-hover dataTable dtr-inline">
                                <thead>
                                    <tr>
                                        <th id="id">{{trans('backend/common.no')}}</th>
                                        <th id="payment_option">{{trans('backend/reports.payment_option')}}</th>
                                        <th id="sales_amount">{{trans('backend/reports.sales_amount')}}</th>
                                        <th id="total_sales_transaction">{{trans('backend/reports.total_sales_transaction')}}</th>
                                        <th id="refunds_amount">{{trans('backend/reports.refunds_amount')}}</th>
                                        <th id="refunds_transaction">{{trans('backend/reports.refunds_transaction')}}</th>
                                        <th id="cancel_amount">{{trans('backend/reports.cancel_amount')}}</th>
                                        <th id="cancel_transaction">{{trans('backend/reports.cancel_transaction')}}</th>
                                        <th id="net">{{trans('backend/reports.net')}}</th>
                                        <th id="total_transaction">{{trans('backend/reports.total_transaction')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>
    </section>
    <!-- Main content -->
</div>
@endsection
