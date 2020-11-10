@php
    $permission = new \App\Models\Permissions();
    $lang = \App\Models\Languages::getBackLang();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script>
        function viewLogs(e, id) {
            var modelId = $('#myModal');
            $.get(adminUrl + '/logs/' + id + '', function (response) {
                modelId.html(response);
                modelId.modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });
        }

        $(function () {
            $('#logs-list').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                iDisplayLength: 10,
                "ajax": {
                    url: '{{route('admin.logs.paginate')}}',
                    type: 'POST',
                    data: function (d) {
                        d._token = '{{csrf_token()}}';
                    }
                },
                bPaginate: true,
                columns: [
                    {data: "index", sortable: false},
                    {data: "type", sortable: false},
                    {data: "file_name", sortable: false},
                    {data: "function", sortable: false},
                    {data: "ip_address", sortable: false},
                    {data: "action", sortable: false},
                ],
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            var html = '';
                            var type = row.type;

                            if (type == 1) {
                                html = '<span>Admin</span>';
                            } else if (type == 2) {
                                html = '<span>Web</span>';
                            } else {
                                html = '<span>App</span>';
                            }

                            return [html].join('');
                        },
                        "targets": $('#logs-list th#type').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row) {
                            var btn = '';
                            var log_id = row.log_id;

                            @if($permission::checkActionPermission('delete_logs'))
                                btn += ' <a href="javascript:void(0);" onclick="viewLogs(this,\'' + log_id + '\')" class="btn btn-info btn-sm"><i class="fa fa-eye text-white"></i></a>';
                            @endif

                                return [btn].join('');
                        },
                        "targets": $('#logs-list th#action').index(),
                        "orderable": false,
                        "searchable": false
                    }
                ]
            });
        });

    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('backend/logs.logs')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                    href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/logs.logs')}}</li>
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
                            <h3 class="card-title">{{trans('backend/logs.list_logs')}}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="logs-list" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                <thead>
                                <tr>
                                    <th>{{trans('backend/common.no')}}</th>
                                    <th id="type">{{trans('backend/logs.type')}}</th>
                                    <th>{{trans('backend/logs.file_name')}}</th>
                                    <th>{{trans('backend/logs.function')}}</th>
                                    <th id="profile">{{trans('backend/logs.ip_address')}}</th>
                                    <th>{{trans('backend/common.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
