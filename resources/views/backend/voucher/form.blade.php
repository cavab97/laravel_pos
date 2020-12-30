@if(isset($voucherData))
    {{ Form::model($voucherData, ['route' => ['admin.voucher.update',$voucherData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmVoucher', 'method'=>'put', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('voucher_id', $voucherData->voucher_id) }}
@else
    {{ Form::model(null, ['route' => 'admin.voucher.store', 'files' => true, 'role' => 'form', 'id'=>'frmVoucher', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif
<div class="form-sec">

    <div class="row">
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('voucher_name',trans('backend/voucher.voucher_name')) }}
                {{ Form::text('voucher_name', old('voucher_name'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/voucher.voucher_name'),"id"=>"voucher_name"]) }}
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('voucher_code',trans('backend/voucher.voucher_code')) }}
                {{ Form::text('voucher_code', old('voucher_code'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/voucher.voucher_code'),"id"=>"voucher_code"]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 required">
            <div class="form-group">
                {{ Form::label('voucher_discount_type',trans('backend/voucher.voucher_discount_type')) }}
                    {!! Form::select('voucher_discount_type',['2' => 'Fix','1'=>'Percentage'],empty($voucherData->voucher_discount_type) ?  1 : old('voucher_discount_type'), ["required",'class'=>'form-control form-control-sm','placeholder'=>trans('backend/voucher.voucher_select')]) !!}

            </div>
        </div>
        <div class="col-md-3 required">
            <div class="form-group">
                {{ Form::label('voucher_discount',trans('backend/voucher.voucher_discount')) }}
                    {!! Form::number('voucher_discount',old('voucher_discount'),["min"=>0,"required",'class'=>'form-control form-control-sm','placeholder'=>trans('backend/voucher.voucher_discount'),'onBlur'=>'checkPercentage(this)']) !!}
            </div>
        </div>
        <div class="col-md-3 required">
            <div class="form-group">
                <label>{{trans('backend/voucher.applicable_form')}}</label>
                <div class="input-group date" id="voucher_applicable_from" data-target-input="nearest">
                        {{ Form::text('voucher_applicable_from', old('voucher_applicable_from'), ["required","class"=>"form-control form-control-sm datetimepicker-input","placeholder"=>trans('backend/voucher.applicable_form'),"id"=>"voucher_applicable_from","data-target"=>"#voucher_applicable_from"]) }}
                    <div class="input-group-append" data-target="#voucher_applicable_from"
                         data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 required">
            <div class="form-group">
                <label>{{trans('backend/voucher.applicable_to')}}</label>
                <div class="input-group date" id="voucher_applicable_to" data-target-input="nearest">
                        {{ Form::text('voucher_applicable_to', old('voucher_applicable_to'), ["required","class"=>"form-control form-control-sm datetimepicker-input","placeholder"=>trans('backend/voucher.applicable_to'),"id"=>"voucher_applicable_to","data-target"=>"#voucher_applicable_to"]) }}
                    <div class="input-group-append" data-target="#voucher_applicable_to"
                         data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 required">
            <div class="form-group">
                {{ Form::label('minimum_amount',trans('backend/voucher.minimum_amount')) }}
                {{ Form::number('minimum_amount', empty($voucherData->maximum_amount) ?  0 : old('minimum_amount'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/voucher.minimum_amount'),"id"=>"minimum_amount","name"=>"minimum_amount",'min'=>0]) }}
            </div>
        </div>
        <div class="col-md-3 required">

            <div class="form-group">
                {{ Form::label('maximum_amount',trans('backend/voucher.maximum_amount')) }}
                {{ Form::number('maximum_amount', empty($voucherData->maximum_amount) ?  0 : old('maximum_amount'), ["required","class"=>"form-control form-control-sm","placeholder"=>trans('backend/voucher.maximum_amount'),"id"=>"maximum_amount","name"=>"maximum_amount",'min'=>0]) }}
            </div>

        </div>
        <div class="col-md-3">

            <div class="form-group">
                {{ Form::label('uses_total',trans('backend/voucher.uses_total')) }}
                {{ Form::number('uses_total', empty($voucherData->uses_total) ?  0 :old('uses_total'), ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/voucher.uses_total'),"id"=>"uses_total","name"=>"uses_total",'min'=>0]) }}
            </div>

        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('uses_customer',trans('backend/voucher.uses_customer')) }}
                {{ Form::number('uses_customer', empty($voucherData->uses_customer) ?  0 :old('uses_customer'), ["class"=>"form-control form-control-sm","placeholder"=>trans('backend/voucher.uses_customer'),"id"=>"uses_customer","name"=>"uses_customer",'min'=>0]) }}
            </div>
        </div>
    </div>
    <div class="row">
        <?php
        $categoryData = '';
        if (isset($voucherData->voucher_categories)) {
            $categoryData = explode(',', $voucherData->voucher_categories);
        }
        ?>
        <div class="col-md-6">
            <div class="form-group select2-purple">
                {{ Form::label('voucher_categories',trans('backend/category.category')) }}
                <select name="voucher_categories[]" id="voucher_categories" multiple="multiple"
                            class="form-control form-control-sm category_select2 select2"
                        data-placeholder="{{trans('backend/category.category')}}">
                    @if(isset($categoryList))

                        @foreach($categoryList as $value)
                            @php
                                $selected = '';
                                if (!empty($voucherData) && in_array($value->category_id, $categoryData)) {
                                $selected = 'selected';
                                }
                            @endphp
                            <option value="{{$value->category_id}}" {{$selected}}>{{$value->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <?php
        $productData = '';
        if (isset($voucherData->voucher_products)) {
            $productData = explode(',', $voucherData->voucher_products);
        }
        ?>
        <div class="col-md-6">
            <div class="form-group select2-purple">
                {{ Form::label('voucher_products',trans('backend/product.product')) }}
                <select name="voucher_products[]" id="voucher_products" multiple="multiple"
                            class="form-control form-control-sm products_select2 select2"
                        data-placeholder="{{trans('backend/product.product')}}">
                    @if(isset($productList))

                        @foreach($productList as $value)
                            @php
                                $selected = '';
                                if (!empty($voucherData) && in_array($value->product_id, $productData)) {
                                $selected = 'selected';
                                }
                            @endphp
                            <option value="{{$value->product_id}}" {{$selected}}>{{$value->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 required">
                {{ Form::label('voucher_banner',trans('backend/category.icon')) }}
                {{ Form::file('voucher_banner', [$required, "accept"=>"image/*", "onChange"=>"previewImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"voucher_banner"]) }}
            <div class="col-3 mt-2" id="icon_uploaded">
                @if(!empty($voucherData->voucher_banner))
                    <img src="{{asset($voucherData->voucher_banner)}}" width="80px" height="80px">
                @endif
            </div>
            <div class="col-3 mt-2" id="icon_preview" style="display:none">
                <img src="" id="profile_preview" width="80px" height="80px"/>
            </div>
        </div>
        <div class="col-md-6 required">
            <div class="form-group">
                {{ Form::label('status',trans('backend/voucher.status')) }}
                {!! Form::select('status',['1' => 'Active','2'=>'IN-active'], empty($voucherData->status) ?  1 :old('status'), ["required",'class'=>'form-control form-control-sm','placeholder'=>trans('backend/voucher.status')]) !!}
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
            <a href="{{ route('admin.voucher.index')}}"
               class="btn btn-danger">{{trans('backend/common.back')}}</a>
        </div>
    </div>
</div>

{{ Form::close() }}
