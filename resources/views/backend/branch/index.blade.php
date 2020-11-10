@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/users.js')}}"></script>
    <script>
        $(function () {
            var oTable = $('#branch-list').dataTable({
                "bStateSave": false,
                "processing": true,
                "serverSide": true,
                "bProcessing": true,
                "iDisplayLength": 10,
                "bServerSide": true,
                "bPaginate": true,
                "sAjaxSource": adminUrl + '/branch-paginate',
                lengthChange: true,
                "fnServerParams": function (aoData) {
                    var acolumns = this.fnSettings().aoColumns,
                        columns = [];
                    $.each(acolumns, function (i, item) {
                        columns.push(item.data);
                    });
                    var name = $('#name').val();
                    var contact_no = $('#contact_no').val();
                    var email = $('#email').val();
                    var from_date = $('[name="from_date"]').val();
                    var to_date = $('[name="to_date"]').val();

                    aoData.push({name: 'columns', value: columns});
                    aoData.push({name: 'name', value: name});
                    aoData.push({name: 'contact_no', value: contact_no});
                    aoData.push({name: 'email', value: email});
                    aoData.push({name: 'from_date', value: from_date});
                    aoData.push({name: 'to_date', value: to_date});
                },
                "columns": [
                    {data: "branch_id"},
                    {data: "name"},
                    {data: "contact_no"},
                    {data: "email"},
                    {data: "open_from"},
                    {data: "closed_on"},
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
                    $('body').css('min-height', ($('#branch-list tr').length * 50) + 200);
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
                        "targets": $('#branch-list th#status').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row) {
                            var btn = '';
                            var uuid = row.uuid;
                            @if($permission::checkActionPermission('view_branch'))
                                btn += '<a href="' + adminUrl + '/branch/' + uuid + '" class="btn btn-info btn-sm mr-1"><i class="fa fa-eye text-white"></i></a>';
                            @endif
                                    @if($permission::checkActionPermission('edit_branch'))
                                btn += '<a href="' + adminUrl + '/branch/' + uuid + '/edit" class="btn btn-warning btn-sm"><i class="fa fa-edit text-white"></i></a>';
                            @endif
                                    @if($permission::checkActionPermission('delete_branch'))
                                btn += ' <a href="javascript:void(0);" onclick="deleteBranch(this,\'' + uuid + '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash text-white"></i></a>';
                            @endif

                                return [btn].join('');
                        },
                        "targets": $('#branch-list th#action').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row, meta) {
                            return [
                                parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1
                            ].join('');
                        },
                        "targets": $('#branch-list th#id').index(),
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
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/branch.branch')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/branch.branch')}}</li>
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
                            <h3 class="card-title">{{trans('backend/branch.list_branch')}}</h3>
                            <div class="float-right">
                                @if($permission::checkActionPermission('add_branch'))
                                    <a href="{{route('admin.branch.create')}}"
                                       class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>
                                @endif
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body ">
                            <div class="row mb-3">
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="name" name="name" class="form-control form-control-sm"
                                           placeholder="{{trans('backend/common.name')}}" onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    <input type="number" id="contact_no" name="contact_no"
                                           class="form-control form-control-sm"
                                           placeholder="{{trans('backend/branch.contact_no')}}" min="0"
                                           onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="email" name="email" class="form-control form-control-sm"
                                           placeholder="{{trans('backend/common.email')}}" onkeyup="pressEnter(event)">
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
                                <table id="branch-list" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                    <thead>
                                    <tr>
                                        <th id="id">{{trans('backend/common.no')}}</th>
                                        <th>{{trans('backend/branch.name')}}</th>
                                        <th>{{trans('backend/branch.contact_no')}}</th>
                                        <th>{{trans('backend/branch.email')}}</th>
                                        <th>{{trans('backend/branch.open_from')}}</th>
                                        <th>{{trans('backend/branch.closed_on')}}</th>
                                        <th id="status">{{trans('backend/common.status')}}</th>
                                        <th id="action">{{trans('backend/common.action')}}</th>
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