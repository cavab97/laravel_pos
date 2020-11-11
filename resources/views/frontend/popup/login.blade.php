<div class="mw-400 modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="custom-modal-header modal-header">
            <h5 class="custom-modal-title modal-title" id="exampleModalLongTitle">User Login</h5>
            <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {{ Form::model(null, ['route' => ['login.post'], 'files' => true, 'role' => 'form', 'class'=>'login-form',  'id'=>'frmLogin', 'method'=>'post']) }}
        <div class="modal-body text-left">
            <div class="modal-logindata">
                <div class="custom-input-box form-group">
                    <i class="input-icon fas fa-user prefix grey-text"></i>
                    <input type="text" name="username" id="username" class="custom-input form-control check-email" placeholder="Enter Username OR Email" required>
                </div>
                <div class="custom-input-box form-group">
                    <i class="input-icon fas fa-lock  prefix grey-text"></i>
                    <input type="password" name="password" id="password" class="custom-input form-control check-pass" placeholder="Your Password" required>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="javascript:void(0);" class="forgot-txt float-right text-muted" onclick="forgotForm()">
                        <small>Forgot Password?</small>
                    </a>
                </div>
            </div>
        </div>
        <div class="modal-footer justify-content-between">
            <div class="remember-txt chiller_cb">
                {{--<div class="custom-checkbox-sec">
                    <input id="myCheckbox" type="checkbox">
                    <div class="checkbox-name">
                        <label for="myCheckbox">Remember me</label>
                    </div>
                    <span></span>
                </div>--}}
            </div>
            {{ Form::button(trans('frontend/common.login'),['type'=>'submit','class'=>'btn btn-login','id'=>'btnLogin', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('frontend/common.login')])}}
        </div>
        {{ Form::close() }}

    </div>
</div>

<script src="{{asset('frontend/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('frontend/js/pages/login.js')}}"></script>
