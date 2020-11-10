<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">{{trans('backend/rac.add_box')}}
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::model(null, ['route' => ['admin.box.store'], 'files' => true, 'role' => 'form', 'id'=>'frmBox', 'method'=>'post']) }}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/rac.branch')}}</label>
                        <select name="branch_id" id="branch_id"
                                class="form-control form-control-sm" required onchange="getRac(this.value)">
                            <option value="">{{trans('backend/rac.select_branch')}}</option>
                            @foreach($branchList as $value)
                                <option value="{{$value->branch_id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/rac.rac')}}</label>
                        <select name="rac_id" id="rac_id"
                                class="form-control form-control-sm" required>
                            <option value="">{{trans('backend/rac.select_rac')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/product.product')}}</label>
                        <select name="product_id" id="product_id"
                                class="form-control form-control-sm" required>
                            <option value="">{{trans('backend/product.select_product')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/rac.name')}}</label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" autocomplete="off"
                               placeholder="Enter name" required/>
                    </div>
                </div>
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="role_status">{{trans('backend/common.box_for')}}</label>
                        <select name="box_for" id="box_for" class="form-control form-control-sm" required>
                            <option value="">{{trans('backend/common.box_for')}}</option>
                            <option value="2">{{trans('backend/common.beer')}}</option>
                            <option value="1">{{trans('backend/common.other')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 required display-none" id="box_for_wine">
                    {{--<div class="form-group">
                        <label for="name">{{trans('backend/rac.quantity')}}</label>
                        <input type="number" name="wine_qty" id="wine_qty" class="form-control form-control-sm"
                               autocomplete="off"
                               placeholder="{{trans('backend/rac.quantity')}}" required min="0"/>
                    </div>--}}
                    <label for="wine_qty">{{trans('backend/rac.quantity')}}</label>
                    <div class="input-group mb-3">
                        <input type="number" name="wine_qty" id="wine_qty" class="form-control form-control-sm"
                               autocomplete="off" placeholder="{{trans('backend/rac.quantity')}}" required min="0"/>
                        <div class="input-group-append">
                            <span class="input-group-text form-control-sm" id="basic-addon1">cm</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 required display-none" id="box_for_beer">
                    <div class="form-group">
                        <label for="name">{{trans('backend/rac.box_limit')}}</label>
                        <input type="number" name="box_limit" id="box_limit" class="form-control form-control-sm"
                               autocomplete="off"
                               placeholder="{{trans('backend/rac.box_limit')}}" required min="0"/>
                    </div>
                </div>
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="role_status">{{trans('backend/common.status')}}</label>
                        <select name="status" id="status" class="form-control form-control-sm" required>
                            <option value="1">{{trans('backend/common.active')}}</option>
                            <option value="0">{{trans('backend/common.inactive')}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success"></div>
                    <div class="alert alert-danger"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm"
                    data-dismiss="modal">{{trans('backend/common.close')}}</button>
            <button type="submit" class="btn btn-info btn-sm" id="btnSubmit"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> loading..."
                    data-original-text="{{trans('backend/common.submit')}}">{{trans('backend/common.submit')}}
            </button>
        </div>
        {{ Form::close() }}
    </div>
</div>
<script src="{{asset('backend/dist/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/dist/js/pages/box.js')}}"></script>
