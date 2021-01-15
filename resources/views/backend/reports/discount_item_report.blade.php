@php
$permission = new \App\Models\Permissions();
$lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('styles')
<link rel="stylesheet" href="{{asset('backend/plugins/select2/css/select2.min.css')}}">
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
            "sAjaxSource": adminUrl + '/reports-discount-item-paginate',
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
                aoData.push({
                    name: 'category_id',
                    value: $('#category_id').val()
                });
                if ($('#choose-price-type').val() == '%') {
                    aoData.push({
                        name: 'percentage_discount',
                        value: $('#percentage_select').val()
                    });
                } else {
                    aoData.push({
                        name: 'price_opt',
                        value: $('#price_opt').val()
                    });
                    aoData.push({
                        name: 'price',
                        value: $('#price').val()
                    });

                }


            },
            "columns": [
                { data: 'id' },
                { data: 'invoice_no' },
                { data: "terminal_name" },
                { data: "cashier" },
                { data: "product_name" },
                { data: "discount_amount" },
                { data: "discount_remark" },
                { data: "updated_at" },
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
                    var order_number = row.invoice_no;
                    var uuid = row.uuid;
                    var url = '';
                    url += '<a href="' + adminUrl + '/order/' + uuid + '" >' + order_number + '\</a> ';
                    return [
                        url
                    ].join('');
                },
                "targets": $('#shift-list th#invoice_no').index(),
                "orderable": false,
                "searchable": false
            }, {
                "render": function(data, type, row) {
                    var amount = row.discount_amount;
                    var discountType = row.discount_type;
                    return discountType == 1 ? amount + '%' : 'RM ' + amount.toFixed(2);
                },
                "targets": $('#shift-list th#discount_amount').index(),
                "orderable": false,
                "searchable": false
            }
            ]
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
        $('#choose-price-type').change(function(e) {
            if (this.value == "%") {
                $('#percentage_select').removeClass('d-none');
                $('#price-option').addClass('d-none');
            } else {//if (this.value = "RM") {

                $('#percentage_select').addClass('d-none');
                $('#price-option').removeClass('d-none');
            }
            console.log(this.value);
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
                    <h1>{{trans('backend/common.discount_item_report')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                        <li class="breadcrumb-item active">{{trans('backend/common.discount_item_report')}}</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="custom-content content">
        <!-- <ul class="nav nav-tabs nav-justified">
            <li class="nav-item"><a class="nav-link active" href="#">Discount Item</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Discount Order</a></li>
        </ul> -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title">{{trans('backend/common.list_item')}}</h3>
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
                            <div class="col-md-3 mt-2">
                                <select class="form-control form-control-sm" id="category_id" name="category_id">
                                    <option value="">{{trans('backend/common.select_category')}}</option>
                                    @foreach($categoryList as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-2 input-group">
                                <select class="form-control form-control-sm" id="percentage_select" name="percentage_select" style="border-radius: .2rem;">
                                    @for($index=0; $index < 100; $index+=10)
                                    <option value="{{$index}}">{{$index}}</option>
                                    @endfor
                                    <option value="100">100 / Free</option>
                                </select>
                                <select class="form-control form-control-sm col-4" id="choose-price-type" name="choose-price-type">
                                    <option value="%">%</option>
                                    <option value="RM">RM</option>
                                </select>
                                <div class="col-8 px-0 d-none" id="price-option">
                                <input type="number" id="price" name="price" class="form-control form-control-sm price-padding"
                                           placeholder="{{trans('backend/product.price')}}" min="0"
                                           onkeyup="pressEnter(event)">
                                    <span id="number">
                                        <select name="price_opt" class="price_opt" id="price_opt">
                                            <option value="=">{{'='}}</option>
                                            <option value=">=">{{'>='}}</option>
                                            <option value="<=">{{'<='}}</option>
                                        </select>
                                    </span>
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
                                        <th id="invoice_no">{{trans('backend/reports.invoice_no')}}</th>
                                        <th id="terminal_name">{{trans('backend/reports.terminal_name')}}</th>
                                        <th id="cashier">{{trans('backend/reports.cashier')}}</th>
                                        <th id="product_name">{{trans('backend/reports.product_name')}}</th>
                                        <th id="discount_amount">{{trans('backend/reports.discount_amount')}}</th>
                                        <th id="discount_remark">{{trans('backend/reports.discount_remark')}}</th>
                                        <th id="updated_at">{{trans('backend/common.updated_at')}}</th>
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
