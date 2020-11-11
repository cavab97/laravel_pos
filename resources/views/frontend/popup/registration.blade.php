<div class="mw-400 modal-dialog modal-dialog-centered">
    <div class="modal-content">

            <div class="custom-modal-header modal-header">
                <h5 class="custom-modal-title modal-title" id="exampleModalLongTitle">User Register</h5>
                <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        {{ Form::model(null, ['route' => ['signup.post'], 'files' => true, 'role' => 'form', 'class'=>'login-form',  'id'=>'frmRegister', 'method'=>'post']) }}
            <div class="modal-body text-left">
                <div class="modal-logindata">
                    <div class="custom-input-box form-group">
                        <i class="input-icon fas fa-envelope  prefix grey-text"></i>
                        <input type="email" name="email" id="email" class="custom-input form-control"
                               required placeholder="Enter Email">
                    </div>
                    <div class="custom-input-box form-group">
                        <i class="input-icon fas fa-user  prefix grey-text"></i>
                        <input type="text" class="custom-input form-control" name="username" id="username" required="required"
                               placeholder="Enter Username">
                    </div>
                    <div class="custom-input-box form-group">
                        <i class="input-icon fas fa-user  prefix grey-text"></i>
                        <input type="text" class="custom-input form-control" name="name" id="name" required="required"
                               placeholder="Enter Name">
                    </div>
                    <div class="custom-input-box form-group">
                        <i class="input-icon fas fa-mobile-alt  prefix grey-text"></i>
                        <input type="text" name="mobile" id="mobile" class="custom-input form-control" maxlength="10"
                               placeholder="Enter Phone Number" required onkeypress = "return onlyNumberKey(event)">
                    </div>
                    <div class="custom-input-box form-group">
                        <i class="input-icon fas fa-lock  prefix grey-text"></i>
                        <input type="password" class="custom-input form-control" name="reg_password" id="reg_password" required
                               placeholder="Enter Password" minlength="8">
                    </div>
                    <div class="custom-input-box form-group">
                        <i class="input-icon fas fa-lock  prefix grey-text"></i>
                        <input type="password" name="confirm_password" id="confirm_password"
                               class="custom-input form-control" required placeholder="Confirm Password" minlength="8">
                    </div>
                    {{--<div class="form-group">
                        <div class="remember-txt chiller_cb">
                            <div class="custom-checkbox-sec">
                                <input id="myCheckbox1" type="checkbox" name="terms" required>
                                <div class="checkbox-name">
                                    <label for="myCheckbox1">
                                        <small> I agree with the <a href="javascript:;" class="clr-red">Terms &amp;
                                                Conditions</a> for Registration.
                                        </small>
                                    </label>
                                </div>
                                <span></span>
                            </div>
                        </div>
                    </div>--}}
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                {{ Form::button(trans('frontend/common.register'),['type'=>'submit','class'=>'btn btn-login','id'=>'btnLogin', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('frontend/common.register')])}}
            </div>
        {{ Form::close() }}
    </div>
</div>
<script src="{{asset('frontend/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('frontend/js/pages/register.js')}}"></script>

