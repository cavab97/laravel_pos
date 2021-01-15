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
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa fa-upload" aria-hidden="true"></i>
            <span class="text-white">{{trans('backend/product.upload_file')}}</span>
        </h3>

    </div>
    <div id="content-body-div" class="card-body">
        <div class="row justify-content-center py-2 "><b>{{trans('backend/product.upload_excel_file')}}</b></div>
        <div class="row justify-content-center">
        <div class="btn-group">
            <button class="popper btn btn-secondary" data-toggle="popover" type="button">{{trans('backend/product.download_template')}}</button>
            <div class="popper-content d-none card">
                <div class="card-header">{{trans('backend/product.select_format')}}</div>
                <div class="card-body">
                    <div class="row btn-group">
                        <a href="{{ isset($xls_path) ? $xls_path : '' }}" class="btn btn-default">.xls</a>
                        <a href="{{ isset($xlsx_path) ? $xlsx_path : '' }}" class="btn btn-default">.xlsx</a>
                    </div>
                </div>
            </div>
            <button class="popper btn btn-primary" data-toggle="popover" type="button">{{trans('backend/product.upload_file')}}</button>
            </div>
        </div>
        <div class="row justify-content-center py-2 text-secondary">{{trans('backend/product.format_support')}} (.xls and xlsx)</div>
    </div>
</div>

<div class="form-group " style="float: right;">
    {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('backend/common.submit')])}}
    &nbsp;&nbsp;
    <a href="{{ route('admin.product.create')}}"
        class="btn btn-danger">{{trans('backend/common.back')}}</a>
</div>
{{ Form::close() }}
