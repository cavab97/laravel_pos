@php
    $permission = new \App\Models\Permissions();
    $lang = \App\Models\Languages::getBackLang();

@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script>
        $(function () {
            var oTable = $('#order-list').dataTable({
                "bStateSave": false,
                "processing": true,
                "serverSide": true,
                "bProcessing": true,
                "iDisplayLength": 10,
                "bServerSide": true,
                "bPaginate": true,
                "sAjaxSource": adminUrl + '/order-paginate',
                lengthChange: true,
                "fnServerParams": function (aoData) {
                    var acolumns = this.fnSettings().aoColumns,
                        columns = [];
                    $.each(acolumns, function (i, item) {
                        columns.push(item.data);
                    });
                    var name = $('#name').val();
                    var invoice_no = $('#invoice_no').val();
                    var from_date = $('[name="from_date"]').val();
                    var to_date = $('[name="to_date"]').val();

                    aoData.push({name: 'columns', value: columns});
                    aoData.push({name: 'name', value: name});
                    aoData.push({name: 'invoice_no', value: invoice_no});
                    aoData.push({name: 'from_date', value: from_date});
                    aoData.push({name: 'to_date', value: to_date});
                },
                "columns": [
                    {"data": 'order_id', sortables: false},
                    {"data": 'invoice_no', sortables: false},
                    {"data": 'customer_name', sortables: false},
                    {"data": 'terminal_name', sortables: false},
                    {"data": 'grand_total', sortables: false},
                    {"data": 'order_source', sortables: false},
                    {"data": 'order_status', sortables: false},
                    {"data": 'order_date', sortables: false},
                ],
                "order": [[0, "desc"]],
                "oLanguage": {
                    "sLengthMenu": "Show _MENU_ entries"
                },
                "fnInitComplete": function () {
                },
                'fnServerData': function (sSource, aoData, fnCallback) {
                    $.ajax
                    ({
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
                "fnDrawCallback": function () {
                    $('body').css('min-height', ($('#users-list tr').length * 50) + 200);
                    $(window).trigger('resize');
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            var order_number = row.invoice_no;
                            var uuid = row.uuid;
                            var url = '';
                            url += '<a href="' + adminUrl + '/order/' + uuid + '" >' + order_number + '\</a>&nbsp;&nbsp;';
                            return [
                                url
                            ].join('');
                        },
                        "targets": $('#order-list th#invoice_no').index(),
                        "orderable": false,
                        "searchable": false
                    }, {
                        "render": function (data, type, row) {
                            var order_source = row.order_source;
                            if (order_source == 1) {
                                order_source = 'Web';
                            } else {
                                order_source = 'App';
                            }
                            return [
                                order_source
                            ].join('');
                        },
                        "targets": $('#order-list th#order_source').index(),
                        "orderable": false,
                        "searchable": false
                    }, {
                        "render": function (data, type, row) {
                            var order_status = row.order_status;
                            if (order_status == 1) {
                                order_status = 'New';
                            } else if (order_status == 2) {
                                order_status = 'Ongoing';
                            } else if (order_status == 3) {
                                order_status = 'Cancelled';
                            } else if (order_status == 4) {
                                order_status = 'Completed';
                            } else {
                                order_status = 'Refunded';
                            }
                            return [
                                order_status
                            ].join('');
                        },
                        "targets": $('#order-list th#order_status').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row, meta) {
                            return [
                                parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1
                            ].join('');
                        },
                        "targets": $('#order-list th#id').index(),
                        "orderable": false,
                        "bSortable": false
                    },
                ]
            });
            $('#btnSubmit').click(function () {
                oTable.fnDraw();
            });
            $('#to_date').datetimepicker({
                format: 'DD-MM-YYYY',
                timePicker: false,
                todayHighlight: true,
                todayBtn: "linked",
                autoclose: true
            });
            $('#from_date').datetimepicker({
                format: 'DD-MM-YYYY',
                timePicker: false,
                todayHighlight: true,
                todayBtn: "linked",
                autoclose: true
            })
            ;
            $("#from_date").on("change.datetimepicker", function (e) {
                $('#to_date').datetimepicker('minDate', e.date);
            });

            $("#to_date").on("change.datetimepicker", function (e) {
                $('#from_date').datetimepicker('maxDate', e.date);
            });
        });

        function pressEnter(event) {
            if (event.keyCode === 13 || event.type == 'change') {
                $("#btnSubmit").click();
            }
        }
    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/order.orders')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/order.orders')}}</li>
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
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/order.list_order')}}</h3>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="name" name="name" class="form-control form-control-sm"
                                           placeholder="{{trans('backend/common.name')}}" onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="invoice_no" name="invoice_no"
                                           class="form-control form-control-sm"
                                           placeholder="{{trans('backend/order.invoice_no')}}"
                                           onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    <div class="input-group date" id="from_date" data-target-input="nearest">
                                        {{ Form::text('from_date', old('from_date'), ["required","class"=>"form-control form-control-sm datetimepicker-input","placeholder"=>trans('backend/common.from_date'),"id"=>"from_date","data-target"=>"#from_date"]) }}
                                        <div class="input-group-append" data-target="#from_date"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <div class="input-group date" id="to_date" data-target-input="nearest">
                                        {{ Form::text('to_date', old('to_date'), ["required","class"=>"form-control form-control-sm datetimepicker-input","placeholder"=>trans('backend/common.to_date'),"id"=>"to_date","data-target"=>"#to_date"]) }}
                                        <div class="input-group-append" data-target="#to_date"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <button type="button" id="btnSubmit" name="submit"
                                            class="btn btn btn-warning btn-sm">Filter
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="order-list" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                    <thead>
                                    <tr>
                                        <th id="id">{{trans('backend/common.no')}}</th>
                                        <th id="invoice_no">{{trans('backend/order.invoice_no')}}</th>
                                        <th id="customer_name">{{trans('backend/order.customer_name')}}</th>
                                        <th id="terminal_name">{{trans('backend/order.terminal_name')}}</th>
                                        <th id="grand_total">{{trans('backend/order.grand_total')}}</th>
                                        <th id="order_source">{{trans('backend/order.order_source')}}</th>
                                        <th id="order_status">{{trans('backend/order.order_status')}}</th>
                                        <th id="order_date">{{trans('backend/order.order_date')}}</th>
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
