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
<style>
.upload-message{
    margin-bottom: 1rem;
}
</style>
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
        <div class="upload-message">
            <div style="display:none" class="uploading"><i class="fa fa-spinner fa-pulse fa-fw"></i> {{trans('backend/product.file_uploading')}}</div>
            <div style="display:none" class="uploaded"><i class="fa fa-spinner fa-pulse fa-fw"></i> {{trans('backend/product.checking_data')}}</div>
            <div style="display:none" class="importing"><i class="fa fa-spinner fa-pulse fa-fw"></i> {{trans('backend/product.product_importing')}}</div>
            <div style="display:none" class="done"><i style="color:green" class="fa fa-check fa-fw"></i> {{trans('backend/product.product_imported')}}</div>
            <div style="display:none" class="redirect"><i class="fa fa-spinner fa-pulse fa-fw"></i> {{trans('backend/product.auto_redirect')}}</div>
            <div style="display:none" class="error"><i style="color:red" class="fa fa-times fa-fw"></i><span>Invalid</span></div>
        </div>
        </div>
        <div class="row justify-content-center">
        <div class="btn-group">
            <button class="popper btn btn-secondary" data-toggle="popover" type="button">{{trans('backend/product.download_template')}}</button>
            <div class="popper-content d-none card">
                <div class="card-header">{{trans('backend/product.select_format')}}</div>
                <div class="card-body">
                    <div class="row btn-group">
                        <a href="{{ isset($xls_path) ? $xls_path : '#' }}" class="btn btn-default" data-toggle="tooltip" title="For Microsoft Office 2007 and older">.xls</a>
                        <a href="{{ isset($xlsx_path) ? $xlsx_path : '#' }}" class="btn btn-default" data-toggle="tooltip" title="Standard Microsoft Office use">.xlsx</a>
                    </div>
                </div>
            </div>
            <label style="margin-bottom:0;" class="btn btn-default" for="file-upload">
            <span>{{trans('backend/product.upload_file')}}</span>
            <input style="display:none" type="file" id="file-upload" name="uploadedFile"
            accept="
                application/vnd.ms-excel,
                application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
            "
            ></label>
            
            <!-- <button class="popper btn btn-primary" type="button">{{trans('backend/product.upload_file')}}</button> -->
            </div>
        </div>
        <div class="row justify-content-center py-2 text-secondary">{{trans('backend/product.format_support')}} (.xls and xlsx)</div>
    </div>
</div>

<div class="form-group " style="float: right;">

<a href="{{ route('admin.product.create')}}"
        class="btn btn-info">{{trans('backend/common.submit')}}</a>
    <!-- {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info disabled','id'=>'btnSubmit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('backend/common.submit')])}}
    &nbsp;&nbsp; -->
    <a href="{{ route('admin.product.create')}}"
        class="btn btn-danger">{{trans('backend/common.back')}}</a>
</div>

<script src="{{asset('frontend/js/jquery.min.js')}}"></script>
<script>
$(document).on('change','#file-upload',function(){
    var property = document.getElementById('file-upload').files[0];
    var image_name = property.name;
    var image_extension = image_name.split('.').pop().toLowerCase();
    var server_response = false;
    if($.inArray(image_extension,['xls','xlsx']) == -1){
        alert("Invalid excel file");
        return;
    } else{
        var form_data = new FormData();
        form_data.append("file",property);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            url: "{{url('/'.config('constants.admin').'/upload/import-product-1')}}",
            method:'POST',
            data:form_data,
            contentType:false,
            cache:false,
            processData:false,
            timeout: 10000, //10 seconds
            beforeSend:function(){
                $('.upload-message').find('.uploading').fadeIn();
                $('.upload-message').find('.error').slideUp();
                $('#file-upload').parent().prepend("<i class='fa fa-spinner fa-pulse fa-fw'></i> ");
                $('#file-upload').parent().find("span").text("{{trans('backend/product.file_uploading')}}");
            },
            error: error_handle,
        })
        .done(function(data){
            $('.upload-message').find('.uploading').slideUp(); 
            console.log(data);
            server_response = true;
            if (typeof data === "object") {
                switch (data.status) {
                        case 200:
                            $('.upload-message').find('.uploaded').fadeIn();
                            upload_array(data.data);
                            break;
                        case 422:
                            $('.upload-message').find('.error').fadeIn();
                            $('.upload-message').find('.error > span').html(
                                'Data mismatch'+
                                '<hr style="margin: .5rem 0;border: 1px solid darkgray;">'+
                                data.err_message);
                            stopUploadLoadingandClear();
                            break;
                        case 404:
                            $('.upload-message').find('.error').fadeIn();
                            $('.upload-message').find('.error > span').text('File no found or failed to upload. Try again later.');
                            stopUploadLoadingandClear();
                            break;
                        default:
                            $('.upload-message').find('.error').fadeIn();
                            $('.upload-message').find('.error > span').text(data.message);
                            stopUploadLoadingandClear();
                            break;
                }
                if (typeof data.data === "string") 
                {} else {}
            } else { //if (typeof data === "string") {
                $('.upload-message').find('.error').fadeIn();
                $('.upload-message').find('.error > span').text('Product Import Failed. Unexpected error.');
                stopUploadLoadingandClear();
            }
        });
    }
});
function upload_array(dataArray) {
    console.log(dataArray);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
        },
        url: "{{url('/'.config('constants.admin').'/upload/import-product-2')}}",
        method:'POST',
        data: {products: dataArray},
        beforeSend:function(){
            setTimeout(function(){ 
                $( "#file-upload" ).prop( "disabled", true );
                $('.upload-message').find('.uploaded').slideUp(); 
                $('.upload-message').find('.importing').fadeIn();
            }, 1500);
        },
        error: error_handle,
        success:function(data){},
        complete: stopUploadLoadingandClear
    })
    .done(function(data){
        server_response = true;
        console.log(Date.now())
        console.log(data);
        if (typeof data === "object") {
            switch (data.status) {
                    case 200:
                        setTimeout(function(){ 
                            $('.upload-message').find('.importing').slideUp();
                            $('.upload-message').find('.done').fadeIn();    
                            setTimeout(function(){ 
                                $('.upload-message').find('.redirect').fadeIn();
                            }, 3000);    
                            setTimeout(function(){ 
                                //window.location = "{{url('/'.config('constants.admin').'/product')}}";
                            }, 5000);
                        }, 2000);
                        break;
                    case 404:
                        $('.upload-message').find('.error').fadeIn();
                        $('.upload-message').find('.error > span').text('File failed to upload. Network disconnect.');
                        break;
                    default:
                        $('.upload-message').find('.error').fadeIn();
                        $('.upload-message').find('.error > span').text('Product Import Failed. Unexpected error.');
                        break;
            }
            if (typeof data.data === "string") 
            {} else {}
        } else { //if (typeof data === "string") {
            $('.upload-message').find('.error').fadeIn();
            $('.upload-message').find('.error > span').text('Product Import Failed. Unexpected error.');
        }
    });
}
function error_handle(XMLHttpRequest, textStatus, errorThrown) {
    console.log(XMLHttpRequest, textStatus, errorThrown);
    $('.upload-message').find('.error').fadeIn();
    if (XMLHttpRequest.status == 404) {
        $('.upload-message').find('.error > span').text('Upload port is closed, Please contact adminstrator.');
    } else if (XMLHttpRequest.status == 0 && XMLHttpRequest.statusText == "timeout") {
        $('.upload-message').find('.error > span').text('Response timeout, please try again later.');
    } else if (XMLHttpRequest.status == 405 || XMLHttpRequest.status == 500) {
        $('.upload-message').find('.error > span').text('Invalid. Please contact adminstrator.');
    } else if (XMLHttpRequest.status == 419) {
        $('.upload-message').find('.error > span').text('Token mismatch. Please contact adminstrator.');
    } else {
        $('.upload-message').find('.error > span').text('Service temporarily unavailable.');
    }
}
function stopUploadLoadingandClear() {
    setTimeout(function(){ 
        $('#file-upload').parent().find("i").remove();
        $('#file-upload').parent().find("span").text("{{trans('backend/product.upload_file')}}");
        $('#file-upload').val('');
        $( "#file-upload" ).prop( "disabled", false );
    }, 2000);
}
</script>
{{ Form::close() }}
