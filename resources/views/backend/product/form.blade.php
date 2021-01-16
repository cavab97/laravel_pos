@php
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp
@if(isset($productData))
    {{ Form::model($productData, ['route' => ['admin.product.update',$productData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmProduct', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('product_id', $productData->product_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.product.store', 'files' => true, 'role' => 'form', 'id'=>'frmProduct', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
@php
    $stock_level_requires = "";
       $stock_level = \App\Models\Helper::getSettingValue('warning_stock_level');
       if($stock_level == 'true')
       {
           $stock_level_requires = 'disabled';
       }
@endphp
<script src="{{asset('backend/plugins/jquery/jquery.min.js')}}"></script>
<script>
$(function() {
    $('#import_excel_url').attr('href', '{{ route('admin.product.excel-import') }}');
});
</script>
<div class="card card-secondary">
    <div class="row col-12 justify-content-end">
    <div style="padding:1rem .5rem" class="row">
        <p>{{ trans('backend/common.try_excel_import') }}</p>
        <a id="import_excel_url" style="margin-left: .3rem">{{ trans('backend/common.import_excel') }}</a>
    </div>
    </div>
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            <span class="text-white">{{trans('backend/product.product_info')}}</span>
        </h3>

    </div>
    <div class="card-body">
        <div class="row">

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 required">
                        <div class="form-group">
                            {{ Form::label('name',trans('backend/common.name')) }}
                            {{ Form::text('name', old('name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/common.name'),"id"=>"name","name"=>"name"]) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('name_2',trans('backend/common.name2')) }}
                            {{ Form::text('name_2', old('name_2'), ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/common.name2'),"id"=>"name2","name"=>"name2"]) }}
                        </div>
                    </div>
                    <div class="col-md-4 required">
                        <div class="form-group">
                            {{ Form::label('sku',trans('backend/product.sku')) }}
                            {{ Form::text('sku', old('sku'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/product.sku'),"id"=>"sku","name"=>"sku"]) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 required">
                        <div class="form-group">
                            {{ Form::label('price',trans('backend/product.price')) }}
                            {{ Form::number('price', old('price'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/product.price'),"id"=>"price","name"=>"price",'min'=>0]) }}
                        </div>
                    </div>
                    <p>{{old('price')}}</p>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('old_price',trans('backend/product.old_price')) }}
                            {{ Form::number('old_price', old('old_price'), ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/product.old_price'),"id"=>"old_price","name"=>"old_price",'min'=>0, "disabled"=>"old('old_price').is_nan"]) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 required">
                        <div class="form-group">
                            {{ Form::label('price_type_id',trans('backend/product.unit_type')) }}
                            <select name="price_type_id" id="price_type_id"
                                    class="form-control form-control-sm" required>
                                <option value="">{{trans('backend/product.select_unit_type')}}</option>
                                @foreach($priceTypeList as $value)
                                    @php
                                        $selected = '';
                                        if(isset($productData)){
                                        if($value->pt_id == $productData->price_type_id){
                                        $selected = 'selected';
                                        }
                                        }
                                    @endphp
                                    <option value="{{$value->pt_id}}" {{$selected}}>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 required">
                        <div class="form-group">
                            {{ Form::label('price_type_value',trans('backend/product.unit_type_value')) }}
                            {{ Form::text('price_type_value', old('price_type_value') ?? 1, ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/product.unit_type_value'),"id"=>"price_type_value","name"=>"price_type_value"]) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">{{trans('backend/category.has_rac_managemant')}}</label>
                            <div>
                                <label class="switch">
                                    <input type="checkbox"
                                           onclick="hasRac(this)"
                                           name="has_rac_managemant"
                                           value="1" @if(isset($productData)) {{ ($productData->has_rac_managemant == 1) ? 'checked':''  }} @endif>
                                    <span class="slider round"></span>

                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">{{trans('backend/product.has_inventory')}}</label>
                            <div>
                                <label class="switch">
                                    <input type="checkbox" name="has_inventory"
                                           value="1" @if(isset($productData)) {{ ($productData->has_inventory == 1) ? 'checked':''  }} @endif>
                                    <span class="slider round"></span>

                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">{{trans('backend/product.has_setmeal')}}</label>
                            <div>
                                <label class="switch">
                                    <input type="checkbox" name="has_setmeal"
                                           value="1" @if(isset($productData)) {{ ($productData->has_setmeal == 1) ? 'checked':''  }} @endif>
                                    <span class="slider round"></span>

                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 required">
                        <div class="form-group select2-purple without_rac"
                             style="display:@if(isset($productData)) {{ ($productData->has_rac_managemant == 0) ? 'block':'none'  }} @else block @endif">
                            {{ Form::label('category',trans('backend/product.category')) }}
                            <select name="category_id[]" id="category_id" multiple
                                    class="form-control form-control-sm category_select2 select2 without_rac_category"
                                    data-placeholder="{{trans('backend/product.select_category')}}"
                                    required>
                                {!! $categoryProductList !!}
                            </select>
                        </div>
                        <div class="form-group with_rac"
                             style="display:@if(isset($productData)) {{ ($productData->has_rac_managemant == 0) ? 'none':'block'  }} @else none @endif">
                            {{ Form::label('category',trans('backend/product.category')) }}
                            <select name="has_category_id[]" id="has_category_id"
                                    class="form-control form-control-sm select2 with_rac_category"
                                    data-placeholder="{{trans('backend/product.select_category')}}"
                                    required>
                                <option value="">Select Category</option>
                                {!! $categoryProductListHasRac !!}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 required">
                        <div class="form-group">
                            <label for="status">{{trans('backend/common.status')}}</label>
                            <select name="status" id="status" class="form-control form-control-sm" required>
                                <option value="1" @if(isset($productData)) {{ ($productData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                                <option value="0" @if(isset($productData)) {{ ($productData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 required">
                        <div class="form-group">
                            {{ Form::label('description',trans('backend/common.description')) }}
                            {{ Form::textarea('description', old('description'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/common.description'),"id"=>"description","name"=>"description","rows"=>"3"]) }}
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

{{--Start Image Block--}}
<div class="card card-secondary">
    <div class="card-header ui-sortable-handle" style="cursor: move;">
        <h3 class="card-title">
            <i class="fa fa-image"></i>
            <span class="text-white">{{trans('backend/product.image')}}</span>
        </h3>
        <div class="float-right">
            <button type="button" class="btn btn-info btn-sm"
                    onclick="addImage()">
                <i class="fa fa-plus"></i> {{trans('backend/product.add_image')}}
            </button>
        </div>

        <input type="hidden" class="product_image_count" id="product_image_count"
               value="@if(isset($productData)){{count($productData->imagesData)+1}}@else{{0}}@endif">
    </div>
    <div class="card-body">

        @if (isset($productData))
            <div class="row">
                @foreach($productData->imagesData as $key => $value)
                    <div class="col-md-3 validateImage" id="image_{{$key+1}}">
                        <div class="form-group">
                            <div class="">
                                <img class="img-thumbnail productImgPrev" src="{{asset($value->asset_path)}}"/>
                            </div>
                            <div id="product_image_preview_{{$key+1}}">
                                <button type="button" class="btn btn-danger btn-sm btn-sm btn-block"
                                        onclick="removeEditImage('{{$value->asset_id}}','{{$value->asset_path}}','{{$key+1}}')">
                                    <span class="fa fa-picture-o"></span>
                                    Remove
                                </button>
                            </div>
                        </div>
                        <div class="input-error partimageerror{{$key+1}}"></div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="row" id="multi-imageBlock">
        </div>
    </div>
</div>
{{--End Image Block--}}

{{--Start attribute Block--}}
<div class="card card-secondary">
    <div class="card-header ui-sortable-handle mb-2" style="cursor: move;">
        <input type="hidden" value="{{trans('backend/common.attribute')}}" id="attribute">
        <input type="hidden" value="{{trans('backend/common.status')}}" id="is_enabled">
        <input type="hidden" value="{{trans('backend/common.no')}}" id="no">
        <input type="hidden" value="{{trans('backend/common.yes')}}" id="yes">
        <input type="hidden" value="{{trans('backend/product.price')}}" id="att_price">
        <h3 class="card-title">
            <i class="fas fa-list-alt"></i>
            <span class="text-white"> {{trans('backend/common.attribute')}}</span>
        </h3>
        <div class="float-right d-flex">
            <div class="col-md-8" style="width: 200px;">
                <select class="form-control form-control-sm att_category_id" id="att_category_id">
                    <option value="" hidden>{{trans('backend/product.select_attribute')}}</option>
                    @if(isset($categoryAttributeList))
                        @foreach($categoryAttributeList as $k1 => $v1)
                            <option value="{{$v1->ca_id}}" id="att_cat_id_{{$k1+1}}">{{$v1->name}}</option>
                        @endforeach
                    @endif
                </select>
                <div id="select_att_error" style="display: none;color: red">please select attribute</div>
                <div id="exists_error" style="display: none;color: red">Attribute already exists.</div>
            </div>

            <button type="button" onclick="addAttribute(this)"
                    class="btn btn-info btn-sm" style="height: 37px;">
                <i class="fa fa-plus"></i> {{trans('backend/common.add_more')}}
            </button>
            <input type="hidden" id="attribute_count"
                   value="@if(isset($productData)){{count($productData->attribute)+1}}@else{{1}}@endif"/>

        </div>

    </div>
    <div class="card-body p-0 mb-0">
        <div class="row" id="multi-attributeBlock">
            <?php
            $attProData = '';
            if (isset($productData->attribute)) {
                $attProData = explode(',', $productData->attribute);
            }
            ?>
            @if (isset($productData->attribute))
                @foreach($productData->attribute as $caMainKey => $cv1)
                    <div class="col-md-6 table-responsive" id="attribute_detail_div_{{$caMainKey+1}}">
                        <table class="table table-sm table-bordered mb-2">
                            <thead class="thead-light">
                            <tr class="text-center">
                                <th colspan="2">{{$cv1->name}}
                                    <a href="javascript:void(0);" class="text-dark"><i
                                                onclick="removeAtt({{$caMainKey+1}})"
                                                class="fa fa-times float-right" aria-hidden="true"></i></a>
                                    <input type="hidden" class="cat_att_id" id="cat_att_id_{{$caMainKey+1}}"
                                           value="{{$cv1->ca_id}}" name="cat_attribute_id[]">
                                    <input type="hidden" id="selectedText_{{$caMainKey+1}}" value="{{$cv1->name}}">
                                </th>
                            </tr>
                            </thead>
                            <tbody style="border-bottom: 2px solid #dee2e6;">

                            @foreach($cv1->attribute as $ak1 => $av1)
                                <tr>
                                    <td>{{$av1->name}}</td>
                                    <td><input type="number" name="att_price_{{$av1->attribute_id}}[{{$cv1->ca_id}}][]"
                                               value="{{$av1->price}}"
                                               id="att_price_{{$cv1->ca_id}}{{$ak1}}"
                                               class="form-control form-control-sm att_price"
                                               placeholder="{{trans('backend/product.price')}}" required min="0">
                                        <input type="hidden" class="att_id" id="att_id_{{$cv1->ca_id}}"
                                               value="{{$av1->attribute_id}}"
                                               name="attribute_id_[{{$cv1->ca_id}}][]">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

            @endif
        </div>
    </div>


</div>
{{--End Attribute Block--}}

{{--Start Modifier Block--}}
<div class="card card-secondary">
    <div class="card-header ui-sortable-handle mb-2" style="cursor: move;">
        <input type="hidden" value="{{trans('backend/common.modifier')}}" id="modifier">
        <input type="hidden" value="{{trans('backend/common.status')}}" id="is_enabled">
        <input type="hidden" value="{{trans('backend/common.no')}}" id="no">
        <input type="hidden" value="{{trans('backend/common.yes')}}" id="yes">
        <input type="hidden" value="{{trans('backend/product.price')}}" id="mod_price">
        <h3 class="card-title">
            <i class="fas fa-list-alt"></i>
            <span class="text-white"> {{trans('backend/common.modifier')}}</span>
        </h3>
        <div class="float-right d-flex">
            <div class="col-md-8" style="width: 200px;">
                <select class="form-control form-control-sm modifier" id="modifier_id">
                    <option value="" hidden>{{trans('backend/product.select_modifier')}}</option>
                    @if(isset($modifierList))
                        @foreach($modifierList as $k1 => $v1)
                            <option value="{{$v1->modifier_id}}" id="mod_id_{{$k1+1}}">{{$v1->name}}</option>
                        @endforeach
                    @endif
                </select>
                <div id="select_mod_error" style="display: none;color: red">please select modifier</div>
                <div id="mod_exists_error" style="display: none;color: red">Modifier already exists.</div>
            </div>

            <button type="button" onclick="addModifier(this)"
                    class="btn btn-info btn-sm" style="height: 37px;">
                <i class="fa fa-plus"></i> {{trans('backend/common.add_more')}}
            </button>
            <input type="hidden" id="modifier_count"
                   value="@if(isset($productData)){{count($productData->modifier)+1}}@else{{count($globalModifierList) + 1}}@endif"/>
        </div>
    </div>
    <div class="card-body p-0 mb-0">
        <div class="row" id="multi-modifierBlock">
            <?php
            $modProData = '';
            if (isset($productData->modifier)) {
                $modProData = explode(',', $productData->modifier);
            }
            ?>
            @if (isset($productData->modifier))
                @foreach($productData->modifier as $ak1 => $av1)
                    <div class="col-md-6 table-responsive" id="modifier_detail_div_{{$ak1+1}}">
                        <table class="table table-sm table-bordered mb-2">
                            <thead class="thead-light">
                            <tr class="text-center">
                                <th colspan="2">{{$av1->name}}
                                        <a href="javascript:void(0);" class="text-dark"> <i
                                                    onclick="removeMod({{$ak1+1}})"
                                                    class="fa fa-times float-right"
                                                    aria-hidden="true"></i></a>
                                    <input type="hidden" class="mod_id" id="mod_id_{{$ak1+1}}"
                                           value="{{$av1->modifier_id}}"
                                           name="modifier_id[{{$ak1+1}}]">
                                    <input type="hidden" id="selectedText_mod{{$ak1+1}}" value="{{$av1->name}}">
                                </th>
                            </tr>
                            </thead>
                            <tbody style="border-bottom: 2px solid #dee2e6;">
                            <tr>
                                <td><label for="mod_price_{{$ak1+1}}">{{trans('backend/product.price')}}</label></td>
                                <td>
                                    <input type="number" name="mod_price[{{$ak1+1}}]"
                                           value="{{$av1->price}}"
                                           id="mod_price_{{$ak1+1}}" class="form-control form-control-sm"
                                           placeholder="{{trans('backend/product.price')}}" required min="0">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="is_enabled_{{$ak1+1}}">{{trans('backend/common.status')}}</label></td>
                                <td>
                                    <span class="radio-left ml-2">
                                <input type="radio" name="is_enabled_mod[{{$ak1+1}}]"
                                       value="1" {{ ($av1->status=="1")? "checked" : "" }}/>{{trans('backend/common.yes')}}
                                </span>
                                    <span class="radio-right ml-2">
                                <input type="radio" name="is_enabled_mod[{{$ak1+1}}]"
                                       value="0" {{ ($av1->status=="0")? "checked" : "" }}/>{{trans('backend/common.no')}}
                                </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @else

                @if (isset($globalModifierList))
                    @foreach($globalModifierList as $ak1 => $av1)
                        <div class="col-md-6 table-responsive" id="modifier_detail_div_{{$ak1+1}}">
                            <table class="table table-sm table-bordered mb-2">
                                <thead class="thead-light">
                                <tr class="text-center">
                                    <th colspan="2">{{$av1->name}}
                                        <a href="javascript:void(0);" class="text-dark">
                                            <i onclick="removeMod({{$ak1+1}})" class="fa fa-times float-right" aria-hidden="true"></i>
                                        </a>
                                        <input type="hidden" class="mod_id" id="mod_id_{{$ak1+1}}"
                                               value="{{$av1->modifier_id}}"
                                               name="modifier_id[{{$ak1+1}}]">
                                        <input type="hidden" id="selectedText_mod{{$ak1+1}}" value="{{$av1->name}}">
                                    </th>
                                </tr>
                                </thead>
                                <tbody style="border-bottom: 2px solid #dee2e6;">
                                <tr>
                                    <td><label for="mod_price_{{$ak1+1}}">{{trans('backend/product.price')}}</label>
                                    </td>
                                    <td>
                                        <input type="number" name="mod_price[{{$ak1+1}}]"
                                               value=""
                                               id="mod_price_{{$ak1+1}}" class="form-control form-control-sm"
                                               placeholder="{{trans('backend/product.price')}}" required min="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="is_enabled_{{$ak1+1}}">{{trans('backend/common.status')}}</label>
                                    </td>
                                    <td>
                                    <span class="radio-left ml-2">
                                <input type="radio" name="is_enabled_mod[{{$ak1+1}}]"
                                       value="1" checked/>{{trans('backend/common.yes')}}
                                </span>
                                        <span class="radio-right ml-2">
                                <input type="radio" name="is_enabled_mod[{{$ak1+1}}]"
                                       value="0"/>{{trans('backend/common.no')}}
                                </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>

    </div>
</div>
{{--End modifier Block--}}

    {{--Start Branch Block--}}
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa fa-code-fork"></i>
                {{trans('backend/category.assign_branch')}}
            </h3>

        </div>
        <div class="card-body">
            <div class="row">
                @foreach($branchList as $key=>$value)
                    @php
                        $checked = '';
                        $checked_requires = '';
                        $display_order = '';
                        $stockLevel = '';
                        $branch_status = 1;
                        $is_enabled = 0;
                        $printer = '';
                        if (isset($productData->branchData)) {
                        foreach ($productData->branchData as $k=>$v){
                             if($value['branch_id'] == $v->branch_id){
                                    $stockLevel = $v->warningStockLevel;
                                    $display_order = $v->display_order;
                                    $branch_status = $v->status;
                                    $checked = 'checked';
                                    $checked_requires = 'required';
                                    $is_enabled = 1;
                                    $printer = $v->printer_id;
                                } else {
                                    $checked = '';
                                    $is_enabled = 0;
                                }
                            }
                        }
                    @endphp
                    <div class="col-md-3">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4 class="card-title">{{$value['name']}}</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    {{--<label for="branch_id{{$key}}">{{$value['name']}}</label>
                                    <br>--}}
                                    <label class="switch">
                                        <input type="checkbox" onclick="switchDisable(this,'{{$value["branch_id"]}}')"
                                               id="customSwitch{{$key}}"
                                               name="branch_id[]"
                                               value="{{$value['branch_id']}}"
                                               @if(isset($productData)) {{ (in_array($value["branch_id"],explode(',',$productData->branch))) ? 'checked':''  }}
                                               @else {{count($branchList) == 1 ? 'checked':''}}
                                               @endif>
                                        <span class="slider round"></span>
                                    </label>
                                    <input type="number" placeholder="{{trans('backend/common.display_order')}}"
                                           name="display_order[{{$value['branch_id']}}]"
                                           id="display_order_{{$value["branch_id"]}}"
                                           value="{{$display_order}}" {{$checked_requires}} min="1"
                                           class="form-control form-control-sm"
                                           @if(isset($productData)) {{ (!in_array($value["branch_id"],explode(',',$productData->branch))) ? 'disabled':'' }}
                                           else {{count($branchList) == 1 ? '':'disabled'}} @endif>

                                    <input type="number"
                                           placeholder="{{trans('backend/inventory.warning_stock_level')}}"
                                           name="warning_stock_level[{{$value['branch_id']}}]"
                                           id="warning_stock_level_{{$value["branch_id"]}}"
                                           value="{{$stockLevel}}" {{$checked_requires}} min="0"
                                           class="form-control form-control-sm mt-2"

                                           @if(isset($productData)) {{ (!in_array($value["branch_id"],explode(',',$productData->branch))) ? 'disabled':$stock_level_requires }} @else disabled @endif
                                    >
                                    <input type="hidden" name="stock_level_required" value="{{$stock_level}}"
                                           id="stock_level_requires">
                                </div>

                                <div class="form-group">
                                    <select class="form-control form-control-sm" id="printer_id{{$value["branch_id"]}}"
                                            name="printer_id[{{$value["branch_id"]}}]"
                                            @if(isset($productData)) {{ (!in_array($value["branch_id"],explode(',',$productData->branch))) ? 'disabled':'' }}
                                            @else {{count($branchList) == 1 ? '':'disabled'}}
                                            @endif required>
                                        <option value="">{{trans('backend/product.select_printer')}}</option>
                                        @if(isset($value['printer']))
                                            @foreach($value['printer'] as $pk1 => $pv1)
                                                <option value="{{$pv1['printer_id']}}"
                                                        @if($pv1['printer_id'] == $printer) selected @endif>{{$pv1['printer_name']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="is_enabled_{{$key}}">{{trans('backend/common.status')}}</label>
                                    <span class="radio-left">
                                    <input type="radio" name="is_enabled_status[{{$value['branch_id']}}]"
                                           value="1"
                                           {{ ($branch_status==1)? "checked" : "" }} @if(isset($productData)) {{ (!in_array($value["branch_id"],explode(',',$productData->branch))) ? 'disabled':'' }} @else disabled @endif/>{{trans('backend/common.yes')}}
                                    </span>
                                    <span class="radio-right">
                                    <input type="radio" name="is_enabled_status[{{$value['branch_id']}}]"
                                           value="0"
                                           {{ ($branch_status==0)? "checked" : "" }} @if(isset($productData)) {{ (!in_array($value["branch_id"],explode(',',$productData->branch))) ? 'disabled':'' }} @else disabled @endif/>{{trans('backend/common.no')}}
                                    </span>
                                </div>


                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{--End Branch Block--}}

<!-- <div class="card">
    <div class="card-header ui-sortable-handle" style="cursor: move;">
        <div class="card-tools"> -->
            <div class="form-group " style="float: right;">
                {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('backend/common.submit')])}}
                &nbsp;&nbsp;
                <a href="{{ route('admin.product.index')}}"
                   class="btn btn-danger">{{trans('backend/common.back')}}</a>
            </div>
 <!--        </div>
    </div>
</div> -->
{{ Form::close() }}
