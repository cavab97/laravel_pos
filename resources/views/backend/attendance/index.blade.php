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
            var oTable = $('#attendance-list').dataTable({
                "bStateSave": false,
                "processing": true,
                "serverSide": true,
                "bProcessing": true,
                "iDisplayLength": 10,
                "bServerSide": true,
                "bPaginate": true,
                "sAjaxSource": adminUrl + '/attendance-paginate',
                lengthChange: false,
                "fnServerParams": function (aoData) {
                    var acolumns = this.fnSettings().aoColumns,
                        columns = [];
                    $.each(acolumns, function (i, item) {
                        columns.push(item.data);
                    });
                    var name = $('#name').val();
                    var branch = $('#branch').val();
                    var terminal = $('#terminal').val();
                    var in_out = $('#in_out').val();
                    var from_date = $('[name="from_date"]').val();
                    var to_date = $('[name="to_date"]').val();

                    aoData.push({name: 'columns', value: columns});
                    aoData.push({name: 'name', value: name});
                    aoData.push({name: 'branch', value: branch});
                    aoData.push({name: 'terminal', value: terminal});
                    aoData.push({name: 'in_out', value: in_out});
                    aoData.push({name: 'from_date', value: from_date});
                    aoData.push({name: 'to_date', value: to_date});

                },
                "columns": [
                    {data: "id"},
                    {data: "in_out_datetime"},
                    {data: "employee"},
                    {data: "branch"},
                    {data: "in_out"},
                    {data: "terminal"}
                ],
                "order": [[0, "desc"]],
                "oLanguage": {
                    "sLengthMenu": "_MENU_"
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
                    $('body').css('min-height', ($('#attendance-list tr').length * 50) + 200);
                    $(window).trigger('resize');
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            var html = '';
                            var in_out = row.in_out;

                            if (in_out == 1) {
                                html = '<span>{{trans('backend/attendance.in')}}</span>';
                            } else {
                                html = '<span>{{trans('backend/attendance.out')}}</span>';
                            }

                            return [html].join('');
                        },
                        "targets": $('#attendance-list th#in_out').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row, meta) {
                            return [
                                parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1
                            ].join('');
                        },
                        "targets": $('#attendance-list th#id').index(),
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

        /* function exportCSV() {
             var name = $('#name').val();
             var branch = $('#branch').val();
             var terminal = $('#terminal').val();
             var in_out = $('#in_out').val();
             var from_date = $('[name="from_date"]').val();
             var to_date = $('[name="to_date"]').val();
             var csrf_token = '{{csrf_token()}}';

            $.post(adminUrl + '/attendance-export', {
                _token: csrf_token,
                name: name,
                branch: branch,
                terminal: terminal,
                in_out: in_out,
                from_date: from_date,
                to_date: to_date
            });
        }*/
    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/attendance.attendance')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/attendance.attendance')}}</li>
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
                            <h3 class="card-title">{{trans('backend/attendance.list_attendance')}}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            {{ Form::model(null, ['route' => ['admin.attendance.export'], 'files' => true, 'role' => 'form', 'method'=>'post']) }}

                            <div class="row">
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="name" name="name" class="form-control form-control-sm"
                                           placeholder="{{trans('backend/common.name')}}" onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    <select id="branch" name="branch" class="form-control form-control-sm">
                                        <option value="">{{trans('backend/printer.select_branch')}}</option>
                                        <?php
                                        foreach ($branchList as $key => $value) {
                                            echo '<option value="' . $value['branch_id'] . '" >' . $value['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mt-2">
                                    {!! Form::select('in_out',['1' => 'In','0'=>'Out'],null, ['class'=>'form-control form-control-sm','placeholder'=>trans('backend/attendance.in_out_select'),'id'=>"in_out"]) !!}
                                </div>
                                <div class="col-md-3 mt-2">
                                    <select id="terminal" name="terminal" class="form-control form-control-sm">
                                        <option value="">{{trans('backend/attendance.terminal_select')}}</option>
                                        <?php
                                        foreach ($terminalList as $key => $value) {
                                            echo '<option value="' . $value['terminal_id'] . '" >' . $value['terminal_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mt-2">
                                    <div class="input-group date" id="from_date" data-target-input="nearest">
                                        {{ Form::text('from_date', old('from_date'), ["class"=>"form-control form-control-sm datetimepicker-input","placeholder"=>trans('backend/common.from_date'),"id"=>"from_date","data-target"=>"#from_date"]) }}
                                        <div class="input-group-append" data-target="#from_date"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <div class="input-group date" id="to_date" data-target-input="nearest">
                                        {{ Form::text('to_date', old('to_date'), ["class"=>"form-control form-control-sm datetimepicker-input","placeholder"=>trans('backend/common.to_date'),"id"=>"to_date","data-target"=>"#to_date"]) }}
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
                                <div class="col-md-3 mt-2">
                                    <button type="submit" id="btnSubmit" name="submit"
                                            class="btn btn btn-warning btn-sm">Export
                                    </button>
                                    {{--<a href="javascript:void(0)" onclick="exportCSV()"
                                       class="btn btn btn-warning btn-sm">Export
                                    </a>--}}
                                </div>
                            </div>
                            {{ Form::close() }}
                            <div class="table-responsive">
                                <table id="attendance-list"
                                       class="table table-bordered table-hover dataTable dtr-inline w-100">
                                    <thead>
                                    <tr>
                                        <th id="id">{{trans('backend/common.no')}}</th>
                                        <th>{{trans('backend/attendance.date')}}</th>
                                        <th>{{trans('backend/attendance.employee')}}</th>
                                        <th>{{trans('backend/attendance.branch')}}</th>
                                        <th id="in_out">{{trans('backend/attendance.in_out')}}</th>
                                        <th>{{trans('backend/attendance.terminal')}}</th>
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