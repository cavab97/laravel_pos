@php
    $permission = new \App\Models\Permissions();
    $lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('scripts')
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
                "sAjaxSource": adminUrl + '/reports-shift-paginate',
                lengthChange: true,
                "fnServerParams": function (aoData) {
                    var acolumns = this.fnSettings().aoColumns,
                        columns = [];
                    $.each(acolumns, function (i, item) {
                        columns.push(item.data);
                    });
                    var terminal_id = $('#terminal_id').val();

                    aoData.push({name: 'columns', value: columns});
                    aoData.push({name: 'terminal_id', value: terminal_id});

                },
                "columns": [
                    {"data": 'shift_id'},
                    {data: "terminal_name"},
                    {data: "branch_name"},
                    {data: "user_name"},
                    {data: "start_amount"},
                    {data: "end_amount"},
                    {data: "updated_at"},
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
                    $('body').css('min-height', ($('#shift-list tr').length * 50) + 200);
                    $(window).trigger('resize');
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row, meta) {
                            return [
                                parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1
                            ].join('');
                        },
                        "targets": $('#shift-list th#id').index(),
                        "orderable": false,
                        sortable: false
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
    </script>
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
                                <div class="col-md-3 mt-2">
                                    <select class="form-control form-control-sm" id="terminal_id" name="terminal_id">
                                        <option value="">{{trans('backend/common.select_terminal')}}</option>
                                        @if(isset($terminalList))
                                            @foreach($terminalList as $value)
                                                <option value="{{$value->terminal_id}}">{{$value->terminal_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3 mt-2">
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
                                        <th>{{trans('backend/common.terminal_name')}}</th>
                                        <th>{{trans('backend/common.branch_name')}}</th>
                                        <th>{{trans('backend/common.user_name')}}</th>
                                        <th>{{trans('backend/common.start_amount')}}</th>
                                        <th>{{trans('backend/common.end_amount')}}</th>
                                        <th>{{trans('backend/common.date_and_time')}}</th>
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
