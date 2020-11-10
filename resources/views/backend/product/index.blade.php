@php
    $permission = new \App\Models\Permissions();
@endphp
@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/product.js')}}"></script>
    <script>
        $(function () {
            var oTable = $('#product-list').dataTable({
                "bStateSave": false,
                "processing": true,
                "serverSide": true,
                "bProcessing": true,
                "iDisplayLength": 10,
                "bServerSide": true,
                "bPaginate": true,
                "sAjaxSource": adminUrl + '/product-paginate',
                "bLengthChange": true,
                "fnServerParams": function (aoData) {
                    var acolumns = this.fnSettings().aoColumns,
                        columns = [];
                    $.each(acolumns, function (i, item) {
                        columns.push(item.data);
                    });

                    var name = $('#name').val();
                    var category_id = $('#category_id').val();
                    var sku = $('#sku').val();
                    var price = $('#price').val();
                    var price_opt = $('#price_opt').val();
                    var old_price = $('#old_price').val();
                    var old_price_opt = $('#old_price_opt').val();
                    var status = $('#status').val();
                    var from_date = $('[name="from_date"]').val();
                    var to_date = $('[name="to_date"]').val();

                    aoData.push({name: 'columns', value: columns});
                    aoData.push({name: 'name', value: name});
                    aoData.push({name: 'category_id', value: category_id});
                    aoData.push({name: 'sku', value: sku});
                    aoData.push({name: 'price', value: price});
                    aoData.push({name: 'price_opt', value: price_opt});
                    aoData.push({name: 'old_price', value: old_price});
                    aoData.push({name: 'old_price_opt', value: old_price_opt});
                    aoData.push({name: 'status', value: status});
                    aoData.push({name: 'from_date', value: from_date});
                    aoData.push({name: 'to_date', value: to_date});

                },
                "columns": [
                    {data: "product_id"},
                    {data: "name"},
                    {data: "sku"},
                    {data: "price"},
                    {data: "old_price"},
                    {data: "price_type_name"},
                    {data: "category_name"},
                    /*{data: "has_setmeal"},*/
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
                    $('body').css('min-height', ($('#product-list tr').length * 50) + 200);
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
                        "targets": $('#product-list th#status').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    /*{
                        "render": function (data, type, row) {
                            var html = '';
                            var has_setmeal = row.has_setmeal;

                            if (has_setmeal == 1) {
                                html = '<span class="badge badge-success">{{trans('backend/common.yes')}}</span>';
                            } else {
                                html = '<span class="badge badge-info">{{trans('backend/common.no')}}</span>';
                            }

                            return [html].join('');
                        },
                        "targets": $('#product-list th#has_setmeal').index(),
                        "orderable": false,
                        "searchable": false
                    },*/
                    {
                        "render": function (data, type, row) {
                            var btn = '';
                            var uuid = row.uuid;
                            @if($permission::checkActionPermission('view_product'))
                                btn += '<a href="' + adminUrl + '/product/' + uuid + '" class="btn btn-info btn-sm mr-1"><i class="fa fa-eye text-white"></i></a>';
                            @endif
                                    @if($permission::checkActionPermission('edit_product'))
                                btn += '<a href="' + adminUrl + '/product/' + uuid + '/edit" class="btn btn-warning btn-sm"><i class="fa fa-edit text-white"></i></a>';
                            @endif
                                    @if($permission::checkActionPermission('delete_product'))
                                btn += ' <a href="javascript:void(0);" onclick="deleteProduct(this,\'' + row.uuid + '\')" class="btn btn-danger btn-sm"><i class="fa fa-trash text-white"></i></a>';
                            @endif


                                return [btn].join('');
                        },
                        "targets": $('#product-list th#action').index(),
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "render": function (data, type, row, meta) {
                            return [
                                parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1
                            ].join('');
                        },
                        "targets": $('#product-list th#id').index(),
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
                        <h1>{{trans('backend/product.product')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('backend/product.product')}}</li>
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
                            <h3 class="card-title">{{trans('backend/product.list_product')}}</h3>
                            @if($permission::checkActionPermission('add_product'))
                            <div class="float-right">
                                <a href="{{route('admin.product.create')}}"
                                   class="btn btn-block btn-info">{{trans('backend/common.add')}}</a>

                            </div>
                            @endif
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="name" name="name"
                                           class="form-control form-control-sm"
                                           placeholder="{{trans('backend/common.name')}}"
                                           onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    <select name="category_id" id="category_id" class="form-control form-control-sm">
                                        <option value="">Select Category</option>
                                        {!! $categoryList !!}
                                    </select>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <input type="text" id="sku" name="sku" class="form-control form-control-sm"
                                           placeholder="{{trans('backend/product.sku')}}" onkeyup="pressEnter(event)">
                                </div>
                                <div class="col-md-3 mt-2">
                                    {!! Form::select('status',['1' => 'Active','0'=>'IN-active'],null, ["required",'class'=>'form-control form-control-sm','placeholder'=>trans('backend/voucher.status'),'id'=>"status"]) !!}
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-3 filter-dropdown mt-2">
                                    <input type="number" id="price" name="price" class="form-control form-control-sm price-padding"
                                           placeholder="{{trans('backend/product.price')}}" min="0"
                                           onkeyup="pressEnter(event)">
                                    <span id="number">
                                        <select name="price_opt" class="price_opt" id="price_opt">
                                            <option value="=">{{'='}}</option>
                                            <option value=">">{{'>'}}</option>
                                            <option value="<">{{'<'}}</option>
                                        </select>
                                    </span>
                                </div>
                                <div class="col-md-3 filter-dropdown mt-2">
                                    <input type="number" id="old_price" name="old_price" class="form-control form-control-sm price-padding"
                                           placeholder="{{trans('backend/product.old_price')}}" min="0"
                                           onkeyup="pressEnter(event)">
                                    <span id="number">
                                        <select name="old_price_opt" class="old_price_opt"
                                                id="old_price_opt">
                                            <option value="=">{{'='}}</option>
                                            <option value=">">{{'>'}}</option>
                                            <option value="<">{{'<'}}</option>
                                        </select>
                                    </span>
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
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 mt-2">
                                    <button type="button" id="btnSubmit" name="submit"
                                            class="btn btn btn-warning btn-sm">Filter
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="product-list" class="table table-bordered table-hover dataTable dtr-inline w-100">
                                    <thead>
                                    <tr>
                                        <th id="id">{{trans('backend/common.no')}}</th>
                                        <th>{{trans('backend/users.name')}}</th>
                                        <th>{{trans('backend/product.sku')}}</th>
                                        <th>{{trans('backend/product.price')}}</th>
                                        <th>{{trans('backend/product.old_price')}}</th>
                                        <th>{{trans('backend/product.price_type')}}</th>
                                        <th>{{trans('backend/category.category')}}</th>
                                        {{--<th id="has_setmeal">{{trans('backend/product.has_setmeal')}}</th>--}}
                                        <th id="status">{{trans('backend/common.status')}}</th>
                                        <th>{{trans('backend/common.action')}}</th>
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