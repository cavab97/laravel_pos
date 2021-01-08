@php
    $permission = new \App\Models\Permissions();
    $lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('styles')
    <link rel="stylesheet"
          href="{{asset('backend/plugins/select2/css/select2.min.css')}}">
@endsection
@section('scripts')
    <script src="{{asset('backend/plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            var oTable = $('#shift-list').dataTable({
                "bStateSave": false,
                "processing": true,
                "serverSide": true,
                "bProcessing": true,
                "iDisplayLength": 10,
                "bServerSide": true,
                "bPaginate": true,
                "sAjaxSource": adminUrl + '/reports-cancelled-paginate',
                "lengthChange": true,
                "fnServerParams": function (aoData) {
                    var acolumns = this.fnSettings().aoColumns,
                        columns = [];
                    $.each(acolumns, function (i, item) {
                        columns.push(item.data);
                    });
                    var terminal_id = $('#terminal_id').val();
                    var branch_id = $('#branch_id').val();
                    aoData.push({name: 'columns', value: columns});
                    aoData.push({name: 'terminal_id', value: terminal_id});
                    aoData.push({name: 'branch_id', value: branch_id});

                },
                "columns": [
                    {data: 'id'},
                    {data: 'invoice_no', sortables: false},
                    {data: "terminal_name"},
                    {data: "cashier"},
                    {data: "branch_name"},
                    {data: "reason"},
                    {data: "status"},
                    {data: "grand_total"},
                    {data: "updated_at"},
                ],
                "order": [[8, "desc"]],
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
                    $('body').css('min-height', ($('#shift-list tr').length * 50) + 200);
                    $(window).trigger('resize');
                    /* if ($('#branch_id').val().length == 1) {
                        oTable.fnSetColumnVis(3).visible( false ).adjust(false)
                        //oTable.column(3).visible(false);
                        oTable.columns.adjust().draw();
                    } */
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
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
                        "sortable": false
                    }, {
                        "render": function (data, type, row, meta) {
                            return [
                                parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1
                            ].join('');
                        },
                        "targets": $('#shift-list th#id').index(),
                        "orderable": false,
                        "sortable": false
                    },
                    {"searchable": true, "bSortable": false, "targets": [0]},
                ]
            });
            $('#btnSubmit').click(function () {
                oTable.fnDraw();
            });
        });
        function pressEnter(event) {
            if (event.keyCode === 13 || event.type == 'change') {
                $("#btnSubmit").click();
            }
        }
        $(document).ready(function() {
            $('#branch_id').select2({
                placeholder: "Select a Branch",
                allowClear: true
            });
        });
    </script>
    <style>
        ul.select2-selection__rendered {
            padding: .25rem !important;
            margin-top: 0 !important;
            vertical-align: middle;
        }
        .select2 > span.selection > span > ul > li > button{
            border-right: none !important;
        }
        .select2-container > span.selection > span {
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
                        <h1>{{trans('backend/common.shift_reports')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/common.shift_reports')}}</li>
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
                            <h3 class="card-title">{{trans('backend/common.list_shift')}}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-2">
                                @if(isset($availableBranch))
                                <div class="col-md-3 mt-2 select2-purple">
                                <select name="branch_id[]" id="branch_id" multiple
                                        class="form-control form-control-sm select2 js-example-basic-multiple js-states"
                                        data-placeholder="{{trans('backend/reports.select_branch')}}"
                                        required>
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
                                <div class="col-1 mt-2">
                                    <button type="button" id="btnSubmit" name="submit"
                                            class="btn btn btn-warning btn-sm">Filter
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="shift-list" class="table table-bordered table-hover dataTable dtr-inline">
                                    <thead>
                                    <tr>
                                        <th id="id">{{trans('backend/common.no')}}</th>
                                        <th id="invoice_no">{{trans('backend/order.invoice_no')}}</th>
                                        <th>{{trans('backend/common.terminal_name')}}</th>
                                        <th>{{trans('backend/common.cashier')}}</th>
                                        <th>{{trans('backend/common.branch_name')}}</th>
                                        <th>{{trans('backend/common.reason')}}</th>
                                        <th>{{trans('backend/common.status')}}</th>
                                        <th>{{trans('backend/common.total_amount')}}</th>
                                        <th id="data_time">{{trans('backend/common.date_and_time')}}</th>
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
