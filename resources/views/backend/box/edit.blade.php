<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">{{trans('backend/rac.edit_box')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::model($boxData, ['route' => ['admin.box.update',$boxData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmBox', 'method'=>'put']) }}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/rac.branch')}}</label>
                        <select name="branch_id" id="branch_id"
                                class="form-control form-control-sm" required onchange="getRac(this.value)">
                            <option value="">{{trans('backend/rac.select_branch')}}</option>
                            @foreach($branchList as $value)
                                @php
                                    $selected = '';
                                    if(isset($boxData)){
                                        if($value->branch_id == $boxData->branch_id){
                                            $selected = 'selected';
                                        }
                                    }
                                @endphp
                                <option value="{{$value->branch_id}}" {{$selected}}>{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/rac.rac')}}</label>
                        <select name="rac_id"
                                id="rac_id"
                                class="form-control form-control-sm" required>
                            <option value="">{{trans('backend/rac.select_rac')}}</option>
                            @if(isset($boxData->racList))
                                @foreach($boxData->racList as $value)
                                    @php
                                        $selected = '';
                                        if($value->rac_id == $boxData->rac_id){
                                            $selected = 'selected';
                                        }
                                    @endphp
                                    <option value="{{$value->rac_id}}" {{$selected}}>{{$value->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/product.product')}}</label>
                        <select name="product_id" id="product_id"
                                class="form-control form-control-sm" required>
                            <option value="">{{trans('backend/product.select_product')}}</option>
                            @if(isset($boxData->productList))
                                @foreach($boxData->productList as $value)
                                    @php
                                        $selected = '';
                                        if($value->product_id == $boxData->product_id){
                                            $selected = 'selected';
                                        }
                                    @endphp
                                    <option value="{{$value->product_id}}" {{$selected}}>{{$value->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>


                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/rac.name')}}</label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" autocomplete="off"
                               placeholder="Enter role name" value="{{$boxData->name}}" required/>
                    </div>
                </div>


                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="role_status">{{trans('backend/common.box_for')}}</label>
                        <select name="box_for" id="box_for" class="form-control form-control-sm" required>
                            <option value="">{{trans('backend/common.box_for')}}</option>
                            <option value="2" @if(isset($boxData)) {{ ($boxData->box_for=="2")? "selected" : "" }}@endif>{{trans('backend/common.beer')}}</option>
                            <option value="1" @if(isset($boxData)) {{ ($boxData->box_for=="1")? "selected" : "" }}@endif>{{trans('backend/common.other')}}</option>
                        </select>
                    </div>
                </div>


                <div class="col-md-12 required @if(isset($boxData)) {{ ($boxData->box_for=="1")? "" : "display-none" }}@endif"
                     id="box_for_wine">
                    {{--<div class="form-group">
                        <label for="name">{{trans('backend/rac.quantity')}}</label>
                        <input type="number" name="wine_qty" id="wine_qty" class="form-control form-control-sm"
                               autocomplete="off" value="{{$boxData->wine_qty}}"
                               placeholder="{{trans('backend/rac.quantity')}}" required min="0"/>
                    </div>--}}
                    <label for="wine_qty">{{trans('backend/rac.quantity')}}</label>
                    <div class="input-group mb-3">
                        <input type="number" name="wine_qty" id="wine_qty" class="form-control form-control-sm"
                               autocomplete="off" value="{{$boxData->wine_qty}}" placeholder="{{trans('backend/rac.quantity')}}" required min="0"/>
                        <div class="input-group-append">
                            <span class="input-group-text form-control-sm" id="basic-addon1">cm</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 required @if(isset($boxData)) {{ ($boxData->box_for=="2")? "" : "display-none" }}@endif"
                     id="box_for_beer">
                    <div class="form-group">
                        <label for="name">{{trans('backend/rac.box_limit')}}</label>
                        <input type="number" name="box_limit" id="box_limit" class="form-control form-control-sm"
                               autocomplete="off" value="{{$boxData->box_limit}}"
                               placeholder="{{trans('backend/rac.box_limit')}}" required min="0"/>
                    </div>
                </div>

                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="role_status">{{trans('backend/common.status')}}</label>
                        <select name="status" id="status" class="form-control form-control-sm" required>
                            <option value="1" @if(isset($boxData)) {{ ($boxData->role_status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                            <option value="0" @if(isset($boxData)) {{ ($boxData->role_status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
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
