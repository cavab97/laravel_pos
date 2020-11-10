<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">{{trans('backend/payment.edit_payment_type')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::model($paymentTypeData, ['route' => ['admin.payment-type.update',$paymentTypeData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmPaymenttype', 'method'=>'put']) }}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/payment.name')}}</label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" autocomplete="off"
                               placeholder="Enter role name" value="{{$paymentTypeData->name}}" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {{ Form::label('is_parent',trans('backend/payment.sub_payment')) }}
                        <select id="is_parent" name="is_parent" class="form-control form-control-sm">
                            <option value="">{{trans('backend/payment.sub_payment')}}</option>
                            <?php
                            foreach ($parentPaymentList as $key => $value) {
                                $selected = '';
                                if (isset($paymentTypeData)) {
                                    if ($value['payment_id'] == $paymentTypeData->is_parent) {
                                        $selected = 'selected';
                                    }
                                }
                                echo '<option value="' . $value['payment_id'] . '" ' . $selected . '>' . $value['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 required">
                    {{ Form::label('payment_icon',trans('backend/payment.icon')) }}
                    {{ Form::file('payment_icon', ["accept"=>"image/*", "onChange"=>"previewImage(this)" ,"class"=>"file-input form-control form-control-sm","id"=>"payment_icon","name"=>"payment_icon"]) }}
                    <div class="col-3 mt-2" id="icon_uploaded">
                        @if(!empty($paymentTypeData->payment_icon))
                            <img src="{{asset($paymentTypeData->payment_icon)}}" width="80px" height="80px">
                        @endif
                    </div>
                    <div class="col-3 mt-2" id="icon_preview" style="display:none">
                        <img src="" id="profile_preview" width="80px" height="80px"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="role_status">{{trans('backend/common.status')}}</label>
                        <select name="status" id="status" class="form-control form-control-sm" required>
                            <option value="1" @if(isset($paymentTypeData)) {{ ($paymentTypeData->status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                            <option value="0" @if(isset($paymentTypeData)) {{ ($paymentTypeData->status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
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
<script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/dist/js/pages/payment_type.js')}}"></script>
