@if(isset($setmealData))
    {{ Form::model($setmealData, ['route' => ['admin.setmeal.update',$setmealData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmSetmeal', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('setmeal_id', $setmealData->setmeal_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.setmeal.store', 'files' => true, 'role' => 'form', 'id'=>'frmSetmeal', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('name',trans('backend/setmeal.name')) }}
                {{ Form::text('name', old('name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/setmeal.name'),"id"=>"name","name"=>"name"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('price',trans('backend/product.price')) }}
                {{ Form::number('price', old('price'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/product.price'),"id"=>"price","name"=>"price",'min'=>0]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('setmeal_image',trans('backend/product.image')) }}
                {{ Form::file('setmeal_image', [$required, "accept"=>"image/*", "onChange"=>"previewImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"setmeal_image","name"=>"setmeal_image"]) }}
                <div class="col-3 mt-2" id="icon_uploaded">
                    @if(!empty($setmealData->setmeal_image))
                        <img src="{{asset($setmealData->setmeal_image)}}" width="80px" height="80px">
                    @endif
                </div>
                <div class="col-3 mt-2" id="icon_preview" style="display:none">
                    <img src="" id="profile_preview" width="80px" height="80px"/>
                </div>
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                <label for="status">{{trans('backend/category.status')}}</label>
                <select name="status" id="status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($setmealData)) {{ ($setmealData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($setmealData)) {{ ($setmealData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>

        <div class="col-12 mt-2">
            <div class="card ">
                <div class="card-header bg-secondary border mb-2">
                    <input type="hidden" value="{{trans('backend/common.product')}}" id="product">
                    <input type="hidden" value="{{trans('backend/common.status')}}" id="is_enabled">
                    <input type="hidden" value="{{trans('backend/common.no')}}" id="no">
                    <input type="hidden" value="{{trans('backend/common.yes')}}" id="yes">
                    <input type="hidden" value="{{trans('backend/product.quantity')}}" id="prod_qty">
                    <h3 class="card-title">
                        <span class=""> {{trans('backend/common.product')}}</span>
                    </h3>
                    <div class="float-right d-flex">
                        <div class="col-md-8" style="width: 200px;">
                            <select class="form-control form-control-sm product select2" id="product_id" name="add_product">
                                <option value="" hidden>{{trans('backend/product.select_product')}}</option>
                                @if(isset($productList))
                                    @foreach($productList as $k1 => $v1)
                                        <option value="{{$v1->product_id}}"
                                                id="product_id_{{$k1+1}}">{{$v1->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div id="select_prod_error" style="display: none;color: red">Please select product</div>
                            <div id="product_exists_error" style="display: none;color: red">Product already exists.</div>
                        </div>

                        <button type="button" onclick="addProduct(this)"
                                class="btn btn-info btn-sm" style="height: 37px;">
                             {{trans('backend/common.add_more')}}
                        </button>
                        <input type="hidden" id="product_count"
                               value="@if(isset($setmealData)){{count($setmealData->setmeal_product)+1}}@else{{1}}@endif"/>

                    </div>

                </div>
                <div class="card-body p-1 mb-0">
                    <div class="row" id="multi-attributeBlock">
                        <?php
                        $mealProData = '';
                        if (isset($setmealData->setmeal_product)) {
                            $mealProData = explode(',', $setmealData->setmeal_product);
                        }
                        ?>
                        @if (isset($setmealData->setmeal_product))
                            @foreach($setmealData->setmeal_product as $ak1 => $av1)
                                <div class="col-md-4 table-responsive" id="product_detail_div_{{$ak1+1}}">
                                    <table class="table table-sm table-bordered mb-2">
                                        <thead class="thead-light">
                                        <tr class="text-center">
                                            <th colspan="2">{{$av1->name}}
                                                <a href="javascript:void(0);" class="text-dark"> <i
                                                            onclick="removeProduct({{$ak1+1}})"
                                                            class="fa fa-times float-right"
                                                            aria-hidden="true"></i></a>
                                                <input type="hidden" class="prod_id" id="prod_id_{{$ak1+1}}"
                                                       value="{{$av1->product_id}}"
                                                       name="product_id[{{$ak1+1}}]">
                                                <input type="hidden" id="selectedText_prod{{$ak1+1}}"
                                                       value="{{$av1->name}}">
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody style="border-bottom: 2px solid #dee2e6;">
                                        <tr>
                                            <td>
                                                <input type="number" name="prod_qty[{{$ak1+1}}]"
                                                       value="{{$av1->quantity}}"
                                                       id="prod_qty_{{$ak1+1}}" class="form-control form-control-sm"
                                                       placeholder="{{trans('backend/product.quantity')}}" required
                                                       min="0">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach

                        @endif
                    </div>
                </div>


            </div>
        </div>

        @if(isset($branchList) && count($branchList) > 0)
            <div class="col-12 mt-2">
                <div class="card">
                    <div class="card-header bg-secondary border">
                        <h3 class="card-title">{{trans('backend/category.assign_branch')}}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($branchList as $key=>$value)
                                @php
                                    $checked = '';
                                    $checked_requires = '';
                                    $display_order = '';
                                    $branchArray = array();
                                    if (isset($setmealData->branchData)) {
                                    foreach ($setmealData->branchData as $k=>$v){
                                         if($value['branch_id']== $v->branch_id && ($v->status == 1)){
                                                array_push($branchArray,$v->branch_id);
                                                $checked_requires = 'required';
                                            }
                                        }
                                    }
                                 if(in_array($value['branch_id'],$branchArray)){
                                    $checked = 'checked';
                                }
                                @endphp
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="branch_id">{{$value['name']}}</label>
                                        <br>
                                        <label class="switch">
                                            <input type="checkbox"
                                                   id="customSwitch{{$key}}"
                                                   name="branch_id[]" {{$checked}}
                                                   value="{{$value['branch_id']}}">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
            <a href="{{ route('admin.setmeal.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
