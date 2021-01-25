@if(isset($categoryData))
    {{ Form::model($categoryData, ['route' => ['admin.category.update',$categoryData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmCategory', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('category_id', $categoryData->category_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.category.store', 'files' => true, 'role' => 'form', 'id'=>'frmCategory', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

    <div class="row">
        <div class="col-md-3 required">
            <div class="form-group">
                {{ Form::label('name',trans('backend/category.name')) }}
                {{ Form::text('name', old('name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/category.name'),"id"=>"name","name"=>"name"]) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('parent_id',trans('backend/category.parent_category')) }}
                <select id="parent_id" name="parent_id" class="form-control form-control-sm">
                    <option value="">{{trans('backend/category.parent_category')}}</option>
                    <?php
                    foreach ($parentCategoryList as $key => $value) {
                        $selected = '';
                        if (isset($categoryData)) {
                            if ($value['category_id'] == $categoryData->parent_id) {
                                $selected = 'selected';
                            }
                        }
                        echo '<option value="' . $value['category_id'] . '" ' . $selected . ' >' . $value['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php
        $se_checked = 'checked';
        if (isset($categoryData->is_for_web)) {
            if ($categoryData->is_for_web == 1) {
                $se_checked = 'checked';
            } else {
                $se_checked = '';
            }
        }?>
        <div class="col-md-3">
            <div class="form-group">
                <label for="is_for_web">{{trans('backend/category.is_for')}}
                    {{trans('backend/category.web_back')}}</label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="is_for_web" {{$se_checked}} value="1">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <?php
        $rac_checked = '';
        if (isset($categoryData->has_rac_managemant)) {
            if ($categoryData->has_rac_managemant == 1) {
                $rac_checked = 'checked';
            } else {
                $rac_checked = '';
            }
        }?>
        <div class="col-md-3">
            <div class="form-group">
                <label for="has_rac_managemant">{{trans('backend/category.has_rac_managemant')}}
                </label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="has_rac_managemant" {{$rac_checked}} value="1">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            {{ Form::label('category_icon',trans('backend/category.icon')) }}
            {{ Form::file('category_icon', ["accept"=>"image/*", "onChange"=>"previewImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"category_icon","name"=>"category_icon"]) }}
            <div class="col-3 mt-2" id="icon_uploaded">
                @if(!empty($categoryData->category_icon))
                    <img src="{{asset($categoryData->category_icon)}}" width="80px" height="80px">
                @endif
            </div>
            <div class="col-3 mt-2" id="icon_preview" style="display:none">
                <img src="" id="profile_preview" width="80px" height="80px"/>
            </div>
        </div>
        <div class="col-md-3 required">
            <div class="form-group">
                <label for="status">{{trans('backend/category.status')}}</label>
                <select name="status" id="status" class="form-control form-control-sm" required>
                    <option value="1" @if(isset($categoryData)) {{ ($categoryData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                    <option value="0" @if(isset($categoryData)) {{ ($categoryData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <?php
            $is_setmeal = '';
            if (isset($categoryData->is_setmeal)) {
                if ($categoryData->is_setmeal == 1) {
                    $is_setmeal = 'checked';
                } else {
                    $is_setmeal = '';
                }
            }?>
            <div class="form-group">
                <label for="is_setmeal">{{trans('backend/category.is_setmeal')}}
                </label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="is_setmeal" {{$is_setmeal}} value="1">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        @if(isset($branchList) && count($branchList) > 0)
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-header bg-white border">
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
                                    if (isset($categoryData->branchData)) {
                                    foreach ($categoryData->branchData as $k=>$v){
                                         if($value['branch_id']== $v->branch_id && ($v->status == 1)){
                                                array_push($branchArray,$v->branch_id);
                                                $display_order = $v->display_order;
                                                $checked_requires = 'required';
                                            }
                                        }
                                    }
                                 if(in_array($value['branch_id'],$branchArray)){
                                    $checked = 'checked';
                                }
                                else if (count($branchList) == 1) {
                                    $checked = 'checked';
                                    $display_order = 1;
                                }
                                @endphp
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="branch_id">{{$value['name']}}</label>
                                        <br>
                                        <label class="switch {{ (count($branchList) == 1 ? 'd-none' : '') }}"">
                                            <input type="checkbox" onclick="switchCash(this,'{{$value["branch_id"]}}')"
                                                   id="customSwitch{{$key}}"
                                                   name="branch_id[]" {{$checked}}
                                                   value="{{$value['branch_id']}}">
                                            <span class="slider round"></span>
                                        </label>
                                        <input type="number" placeholder="{{trans('backend/category.display_order')}}"
                                               name="display_order[{{$value['branch_id']}}]"
                                               id="display_order_{{$value["branch_id"]}}"
                                               value="{{$display_order}}" {{$checked_requires}} min="1"
                                               class="form-control form-control-sm" required>
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
            <a href="{{ route('admin.category.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
