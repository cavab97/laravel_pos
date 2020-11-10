@if(isset($customerData))
    {{ Form::model($customerData, ['route' => ['admin.customer.addressupdate',$customerData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmCustomerAddress', 'method'=>'post', 'class'=>'form-horizontal' ]) }}
    @php
        $required = '';
    @endphp
    {{ Form::hidden('id', $customerData->id) }}
@else
    {{ Form::model(null, ['route' => 'admin.customer.addressstore', 'files' => true, 'role' => 'form', 'id'=>'frmCustomerAddress', 'method'=>'post', 'class'=>'form-horizontal']) }}
    @php
        $required = 'required';
    @endphp
@endif

{{--Multiple address block--}}
<input type="hidden" id="user_uuid" value="" name="user_uuid">
<input type="hidden" id="address_count"
       value="@if(isset($customerAddress)){{count($customerAddress)+1}}@else{{2}}@endif"/>
<input type="hidden" value="{{trans('backend/customer.address_line1')}}" id="address_line1">
<input type="hidden" value="{{trans('backend/customer.address_line2')}}" id="address_line2">
<input type="hidden" value="{{trans('backend/customer.is_default')}}" id="is_default">
<input type="hidden" value="{{trans('backend/customer.longitude')}}" id="longitude">
<input type="hidden" value="{{trans('backend/customer.latitude')}}" id="latitude">
<input type="hidden" value="{{trans('backend/common.status')}}" id="add_status">
<input type="hidden" value="{{trans('backend/customer.add_address')}}" id="add_address">
<input type="hidden" value="float-right" id="div_cls">
<input type="hidden" value="{{trans('backend/customer.remove')}}" id="remove_msg">

<input type="hidden" value="{{trans('backend/common.yes')}}" id="yes">
<input type="hidden" value="{{trans('backend/common.no')}}" id="no">
<input type="hidden" value="{{trans('backend/common.active')}}" id="active_status">
<input type="hidden" value="{{trans('backend/common.inactive')}}" id="deactive_status">

@if(isset($customerAddress))
    @if(count($customerAddress)>0)
        @foreach($customerAddress as $k1 => $v1)
            <div class="card card_address_div_{{$k1+1}}">
                <div class="card-header">
                    <h3 class="card-title">{{trans('backend/customer.add_address')}}</h3>
                    <div class="float-right">
                        @if($k1+1 == 1)
                            <button type="button" onclick="addAddress(this)"
                                    class="btn btn-info btn-sm">
                                <i class="fa fa-plus"></i> {{trans('backend/common.add_more')}}
                            </button>
                        @else
                            <button type="button" onclick="removeAddress({{$k1+1}})"
                                    class="btn btn-danger btn-sm">
                                <i class="fa fa-times"></i> {{trans('backend/customer.remove')}}
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="latitude_{{$k1+1}}">{{trans('backend/customer.latitude')}}</label>
                                <input type="text" name="latitude[{{$k1+1}}]" value="{{$v1->latitude}}"
                                       id="latitude_{{$k1+1}}"
                                       class="form-control form-control-sm"
                                       onblur="checkLatitude(this.value,{{$k1+1}})"
                                       placeholder="{{trans('backend/customer.latitude')}}" >
                                <span id="lat_error_{{$k1+1}}" style="display: none">Latitude format has error.</span>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="longitude_{{$k1+1}}">{{trans('backend/customer.longitude')}}</label>
                                <input type="text" name="longitude[{{$k1+1}}]" value="{{$v1->longitude}}"
                                       id="longitude_{{$k1+1}}"
                                       class="form-control form-control-sm"
                                       onblur="checkLongitude(this.value,{{$k1+1}})"
                                       placeholder="{{trans('backend/customer.longitude')}}" >
                                <span id="long_error_{{$k1+1}}"
                                      style="display: none">Longitude format has error.</span>
                            </div>
                        </div>
                        {{--<div class="com-md-2 mt-2">
                            <button type="button" style="margin-top: 25px;padding: 7px;"
                                    onclick="getFullAddress({{$k1+1}})" class="btn btn-info btn-sm">
                                Lookup
                            </button>
                        </div>--}}
                    </div>
                    <div class="row">
                        <div class="col-md-6 required">
                            <div class="form-group">
                                <label for="address_line1_{{$k1+1}}">{{trans('backend/customer.address_line1')}}</label>
                                <input type="text" name="address_line1[{{$k1+1}}]" value="{{$v1->address_line1}}"
                                       id="address_line1_{{$k1+1}}" class="form-control form-control-sm"
                                       placeholder="{{trans('backend/customer.address_line1')}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address_line2_{{$k1+1}}">{{trans('backend/customer.address_line2')}}</label>
                                <input type="text" name="address_line2[{{$k1+1}}]" value="{{$v1->address_line2}}"
                                       id="address_line2_{{$k1+1}}"
                                       class="form-control form-control-sm"
                                       placeholder="{{trans('backend/customer.address_line2')}}">
                            </div>
                        </div>
                    </div>


                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="is_default_{{$k1+1}}">{{trans('backend/customer.is_default')}}</label>
                                <span class="radio-left">
                        <input type="radio" id="is_default_{{$k1+1}}" name="is_default[{{$k1+1}}]" class="radio-yes"
                               onclick="checkDefault(this)"
                               value="1"
                               @if(isset($customerData)){{ ($v1->is_default=="1")? "checked" : "" }}@else
                               checked
                                    @endif
                        />{{trans('backend/common.yes')}}
                        </span>
                                <span class="radio-right">
                        <input type="radio" id="is_default_{{$k1+1}}" name="is_default[{{$k1+1}}]" class="radio-no"
                               onclick="checkDefault(this)"
                               value="0"
                               @if(isset($customerData)){{ ($v1->is_default=="0")? "checked" : "" }}@endif
                        />{{trans('backend/common.no')}}
                        </span>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_{{$k1+1}}">{{trans('backend/common.status')}}</label>
                                <select name="status[{{$k1+1}}]" class="form-control form-control-sm chosen-select"
                                        id="status_{{$k1+1}}">
                                    <option value="{{$k1+1}}" @if(isset($customerAddress)) {{ ($v1->status == 1)
                                            ?
                                    'selected':'' }} @endif>
                                        {{trans('backend/common.active')}}
                                    </option>
                                    <option value="0" @if(isset($customerAddress)) {{ ($v1->status == 0) ? 'selected':''  }} @endif>
                                        {{trans('backend/common.inactive')}}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    @else
        <div class="card_address_div_1">
            <div class="card-header">
                <h3 class="card-title">{{trans('backend/customer.add_address')}}</h3>
                <div class="float-right">
                    <button type="button" onclick="addAddress(this)"
                            class="btn btn-info btn-sm">
                        <i class="fa fa-plus"></i> {{trans('backend/common.add_more')}}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="latitude_1">{{trans('backend/customer.latitude')}}</label>
                            <input type="text" name="latitude[1]" value="" id="latitude_1" class="form-control form-control-sm"
                                   onblur="checkLatitude(this.value,1)"
                                   placeholder="{{trans('backend/customer.latitude')}}" >
                            <span id="lat_error_1" style="display: none">Latitude format has error.</span>

                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="longitude_1">{{trans('backend/customer.longitude')}}</label>
                            <input type="text" name="longitude[1]" value="" id="longitude_1" class="form-control form-control-sm"
                                   placeholder="{{trans('backend/customer.longitude')}}"
                                   onblur="checkLongitude(this.value,1)"
                                   >
                            <span id="long_error_1" style="display: none">Longitude format has error.</span>
                        </div>
                    </div>
                    {{--<div class="com-md-2 mt-2">
                        <button type="button" style="margin-top: 25px;padding: 7px;"
                                onclick="getFullAddress(1)" class="btn btn-info btn-sm">
                            Lookup
                        </button>
                    </div>--}}
                </div>
                <div class="row">
                    <div class="col-md-6 required">
                        <div class="form-group">
                            <label for="address_line1_1">{{trans('backend/customer.address_line1')}}</label>
                            <input type="text" name="address_line1[1]" value="" id="address_line1_1"
                                   class="form-control form-control-sm"
                                   placeholder="{{trans('backend/customer.address_line1')}}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address_line2_1">{{trans('backend/customer.address_line2')}}</label>
                            <input type="text" name="address_line2[1]" value="" id="address_line2_1"
                                   class="form-control form-control-sm"
                                   placeholder="{{trans('backend/customer.address_line2')}}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="is_default_1">{{trans('backend/customer.is_default')}}</label>
                            <span class="radio-left">
                    <input type="radio" id="y_1" name="is_default[1]" class="radio-yes"
                           onclick="checkDefault(this,'Yes')"
                           value="1"
                           checked
                    />{{trans('backend/common.yes')}}
                    </span>
                            <span class="radio-right">
                    <input type="radio" id="n_1" name="is_default[1]" class="radio-no"
                           onclick="checkDefault(this,'No')"
                           value="0"
                    />{{trans('backend/common.no')}}
                    </span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status_1">{{trans('backend/common.status')}}</label>
                            <select name="status[1]" class="form-control form-control-sm chosen-select" id="status_1">
                                <option value="1" @if(isset($customerData)) {{ ($customerData->status == 1) ? 'selected':''  }} @endif>
                                    {{trans('backend/common.active')}}
                                </option>
                                <option value="0" @if(isset($customerData)) {{ ($customerData->status == 0) ? 'selected':''  }} @endif>
                                    {{trans('backend/common.inactive')}}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
@else
    <div class=" card_address_div_1">
        <div class="card-header">
            <h3 class="card-title">{{trans('backend/customer.add_address')}}</h3>
            <div class="float-right">
                <button type="button" onclick="addAddress(this)"
                        class="btn btn-info btn-sm">
                    <i class="fa fa-plus"></i> {{trans('backend/common.add_more')}}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 ">
                    <div class="form-group">
                        <label for="latitude_1">{{trans('backend/customer.latitude')}}</label>
                        <input type="text" name="latitude[1]" value="" id="latitude_1" class="form-control form-control-sm"
                               onblur="checkLatitude(this.value,1)"
                               placeholder="{{trans('backend/customer.latitude')}}" >
                        <span id="lat_error_1" style="display: none">Latitude format has error.</span>

                    </div>
                </div>
                <div class="col-md-6 ">
                    <div class="form-group">
                        <label for="longitude_1">{{trans('backend/customer.longitude')}}</label>
                        <input type="text" name="longitude[1]" value="" id="longitude_1" class="form-control form-control-sm"
                               placeholder="{{trans('backend/customer.longitude')}}"
                               onblur="checkLongitude(this.value,1)"
                               >
                        <span id="long_error_1" style="display: none">Longitude format has error.</span>

                    </div>
                </div>
                {{--<div class="com-md-2 mt-2">
                    <button type="button" style="margin-top: 25px;padding: 7px;"
                            onclick="getFullAddress({{1}})" class="btn btn-info btn-sm">
                        Lookup
                    </button>
                </div>--}}
            </div>
            <div class="row">
                <div class="col-md-6 required">
                    <div class="form-group">
                        <label for="address_line1_1">{{trans('backend/customer.address_line1')}}</label>
                        <input type="text" name="address_line1[1]" value="" id="address_line1_1" class="form-control form-control-sm"
                               placeholder="{{trans('backend/customer.address_line1')}}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address_line2_1">{{trans('backend/customer.address_line2')}}</label>
                        <input type="text" name="address_line2[1]" value="" id="address_line2_1" class="form-control form-control-sm"
                               placeholder="{{trans('backend/customer.address_line2')}}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_default_1">{{trans('backend/customer.is_default')}}</label>
                        <span class="radio-left">
                    <input type="radio" id="y_1" name="is_default[1]" class="radio-yes"
                           onclick="checkDefault(this,'Yes')"
                           value="1"
                           checked
                    />{{trans('backend/common.yes')}}
                    </span>
                        <span class="radio-right">
                    <input type="radio" id="n_1" name="is_default[1]" class="radio-no"
                           onclick="checkDefault(this,'No')"
                           value="0"
                    />{{trans('backend/common.no')}}
                    </span>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status_1">{{trans('backend/common.status')}}</label>
                        <select name="status[1]" class="form-control form-control-sm chosen-select" id="status_1">
                            <option value="1" @if(isset($customerData)) {{ ($customerData->status == 1) ? 'selected':''  }} @endif>
                                {{trans('backend/common.active')}}
                            </option>
                            <option value="0" @if(isset($customerData)) {{ ($customerData->status == 0) ? 'selected':''  }} @endif>
                                {{trans('backend/common.inactive')}}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endif
<div id="multi-addressBlock">

</div>

    <div class="card-header ui-sortable-handle" style="cursor: move;">
        <div class="card-tools">
            <div class="form-group">

                {{ Form::button(trans('backend/common.submit'),['type'=>'submit','class'=>'btn btn-info','id'=>'btnSubmitAdd', 'data-text'=>'Submit', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading...','data-original-text'=>trans('backend/common.submit')])}}
                &nbsp;&nbsp;
                <a href="{{ route('admin.customer.index') }}"
                   class="btn btn-danger">{{trans('backend/common.back')}}</a>

            </div>
        </div>
    </div>

{{ Form::close()}}