@if(isset($inventoryData))
    {{ Form::model($inventoryData, ['route' => ['admin.product_inventory.update',$inventoryData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmInventory', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('inventory_id', $inventoryData->inventory_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.product_inventory.store', 'files' => true, 'role' => 'form', 'id'=>'frmInventory', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif

<div class="form-sec">

    <div class="row">
        <div class="col-md-4 required">
            <div class="form-group">
                {{ Form::label('product_id',trans('backend/product.product')) }}
                <select name="product_id" id="product_id" onchange="getProductBranch(this);"
                        class="form-control product_id select2" required>
                    <option value="">{{trans('backend/inventory.select_product')}}</option>
                    @foreach($productList as $value)
                        @php
                            $selected = '';
                            if(isset($inventoryData)){
                                if($value->product_id == $inventoryData->product_id){
                                    $selected = 'selected';
                                }
                            }
                        @endphp
                        <option value="{{$value->product_id}}" {{$selected}}>{{$value->name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="has_rac_product" id="hac_rac_product" value="0">
            </div>
        </div>
        <div class="col-md-4 required">
            <div class="form-group">
                {{ Form::label('branch_id',trans('backend/branch.branch')) }}
                <select name="branch_id" id="branch_id" onchange="getBranchRac(this);"
                        class="form-control " required>
                    <option value="">{{trans('backend/inventory.select_branch')}}</option>
                    @if(isset($branchList) && !empty($branchList))
                    @foreach($branchList as $value)
                        @php
                            $selected = '';
                            if(isset($inventoryData)){
                                if($value->branch_id == $inventoryData->branch_id){
                                    $selected = 'selected';
                                }
                            }
                        @endphp
                        <option value="{{$value->branch_id}}" {{$selected}}>{{$value->name}}</option>
                    @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-4 required " id="rac_select" style="display: none;">
            <div class="form-group">
                {{ Form::label('rac_id',trans('backend/rac.rac')) }}
                <select name="rac_id" id="rac_id" onchange="getRacBox(this)"
                        class="form-control " required>
                    <option value="">{{trans('backend/rac.select_rac')}}</option>
                    {{--@foreach($racList as $value)
                        <option value="{{$value->rac_id}}" {{$selected}}>{{$value->name}}</option>
                    @endforeach--}}
                </select>
            </div>
        </div>
        <div class="col-md-4 required " id="rac_box_select" style="display: none;">
            <div class="form-group">
                {{ Form::label('box_id',trans('backend/rac.box')) }}
                <select name="box_id" id="box_id"
                        class="form-control " required>
                    <option value="">{{trans('backend/rac.select_box')}}</option>
                    {
                </select>
            </div>
        </div>
        <div class="col-md-4 required">
            <div class="form-group">
                {{ Form::label('qty',trans('backend/inventory.qty')) }}
                {{ Form::number('qty', old('qty'), ["required","class"=>"form-control ","placeholder"=>trans('backend/inventory.qty'),"id"=>"qty","name"=>"qty",'min'=>0]) }}
            </div>
        </div>
    </div>
    <div class="row">
        {{--<div class="col-md-4 required">
            <div class="form-group">
                {{ Form::label('warningStockLevel',trans('backend/inventory.warning_stock_level')) }}
                {{ Form::number('warningStockLevel', old('warningStockLevel'), ["required","class"=>"form-control","placeholder"=>trans('backend/inventory.warning_stock_level'),"id"=>"warning_stock_level","name"=>"warning_stock_level",'min'=>0]) }}
            </div>
        </div>--}}
        <div class="col-md-4 required">
            <div class="form-group">
                <label for="role_status">{{trans('backend/branch.status')}}</label>
                <select name="status" id="status" class="form-control " required>
                    <option value="1" @if(isset($inventoryData)) {{ ($inventoryData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($inventoryData)) {{ ($inventoryData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>
    </div>
</div>


<div id="display_message" class="col-md-12 display-none">
    <div class="alert display-none alert-success"></div>
    <div class="alert display-none alert-danger"></div>
</div>
<div class="card-header ui-sortable-handle" style="cursor: move;">
    <div class="card-tools">
        <div class="form-group ">
            {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('backend/common.submit')])}}
            &nbsp;&nbsp;
            <a href="{{ route('admin.product_inventory.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}