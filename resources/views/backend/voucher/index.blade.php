@php
    $permission = new \App\Models\Permissions();
    $lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/voucher.js')}}"></script>
    <script>
        $(function () {
            var oTable = $('#voucher-list').dataTable({
                "bStateSave": false,
                "processing": true,
                "serverSide": true,
                "bProcessing": true,
                "iDisplayLength": 10,
                "bServerSide": true,
                "bPaginate": true,
                "sAjaxSource": adminUrl + '/voucher-paginate',
                lengthChange: true,
                "fnServerParams": function (aoData) {
                    var acolumns = this.fnSettings().aoColumns,
                        columns = [];
                    $.each(acolumns, function (i, item) {
                        columns.push(item.data);
                    });
                    var voucher_name_code = $('#voucher_name_code').val();
                    var voucher_discount_type = $('#voucher_discount_type').val();
                    var voucher_discount = $('#voucher_discount').val();
                    var status = $('#status').val();
                    var from_date = $('[name="from_date"]').val();
                    var to_date = $('[name="to_date"]').val();

                    aoData.push({name: 'columns', value: columns});
                    aoData.push({name: 'voucher_name_code', value: voucher_name_code});
                    aoData.push({name: 'voucher_discount_type', value: voucher_discount_type});
                    aoData.push({name: 'voucher_discount', value: voucher_discount});
                    aoData.push({name: 'status', value: status});
                    aoData.push({name: 'from_date', value: from_date});
                    aoData.push({name: 'to_date', value: to_date});

                },
                "columns": [
                    {data: "voucher_id"},
                    {data: "voucher_name"},
                    {data: "voucher_code"},
                    {data: "voucher_discount_type"},
                    {data: "voucher_discount"},
                    {data: "voucher_applicable_from"},
                    {data: "voucher_applicable_to"},
                    {data: "status"},
                    {data: "action"},
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
                    $('body').css('min-height', ($('#voucher-list tr').length * 50) + 200);
                    $(window).trigger('resize');
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            var html = '';
                            var status = row.status;

                            if (status == 1) {
                                html = '<span class="badge badge-success">{{trans('backend/common.active')}}</span>';
                            } else {
                                html = '<span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>';
                            }

                            return [html].join('');
                        },
                        "targets": $('#voucher-list th#status').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row) {
                            var html = '';
                            var voucher_discount_type = row.voucher_discount_type;

                            if (voucher_discount_type == 1) {
                                html = '<span class="badge badge-danger">{{trans('backend/voucher.percentage')}}</span>';
                            } else {
                                html = '<span class="badge badge-success">{{trans('backend/voucher.fix')}}</span>';
                            }

                            return [html].join('');
                        },
                        "targets": $('#voucher-list th#voucher_discount_type').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row) {
                            var btn = '';
                            var uuid = row.uuid;

                            @if($permission::checkActionPermission('edit_voucher'))
                                btn += '<a href="' + adminUrl + '/voucher/' + uuid + '/edit" class="btn btn-warning btn-sm"><i class="fa fa-edit text-white"></i></a>';
                            @endif
                                    @if($permission::checkActionPermission('delete_voucher'))
                                btn += ' <a href="javascript:void(0);" onclick="deleteVoucher(this,\'' + uuid + '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash text-white"></i></a>';
                            @endif

                                return [btn].join('');
                        },
                        "targets": $('#voucher-list th#action').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row, meta) {
                            return [
                                parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1
                            ].join('');
                        },
                        "targets": $('#voucher-list th#id').index(),
                        "orderable": false,
                        "bSortable": false
                    },
                ]
            });
            $('#btnSubmit').click(function () {
                oTable.fnDraw();
            })
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
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/voucher.voucher')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/voucher.voucher')}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="custom-content content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/voucher.list_voucher')}}</h3>
                            @if($permission::checkActionPermission('add_voucher'))
                                <div class="float-right">
                                    <a href="{{route('admin.voucher.create')}}"
                                       class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="voucher_name_code" name="voucher_name_code"
                                           class="form-control form-control-sm"
                                           placeholder="{{trans('backend/voucher.voucher_name_code')}}"
                                           onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    {!! Form::select('voucher_discount_type',['2' => 'Fix','1'=>'Percentage'],null, ["required",'class'=>'form-control form-control-sm','placeholder'=>trans('backend/voucher.voucher_select'),'id'=>"voucher_discount_type"]) !!}
                                </div>
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="voucher_discount" name="voucher_discount"
                                           class="form-control form-control-sm"
                                           placeholder="{{trans('backend/voucher.voucher_discount')}}"
                                           onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    {!! Form::select('status',['1' => 'Active','0'=>'IN-active'],null, ["required",'class'=>'form-control form-control-sm','placeholder'=>trans('backend/voucher.status'),'id'=>"status"]) !!}
                                </div>
                            </div>
                            <div class="row mb-3">
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
                                <table id="voucher-list" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                    <thead>
                                    <tr>
                                        <th id="id">{{trans('backend/common.no')}}</th>
                                        <th>{{trans('backend/voucher.voucher_name')}}</th>
                                        <th>{{trans('backend/voucher.voucher_code')}}</th>
                                        <th id="voucher_discount_type">{{trans('backend/voucher.voucher_discount_type')}}</th>
                                        <th>{{trans('backend/voucher.voucher_discount')}}</th>
                                        <th>{{trans('backend/voucher.applicable_form')}}</th>
                                        <th>{{trans('backend/voucher.applicable_to')}}</th>
                                        <th id="status">{{trans('backend/common.status')}}</th>
                                        <th id="action">{{trans('backend/common.action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
