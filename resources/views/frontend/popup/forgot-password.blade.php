<div class="mw-400 modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="custom-modal-header modal-header">
            <h5 class="custom-modal-title modal-title" id="exampleModalLongTitle">Forgot Password</h5>
            <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {{ Form::model(null, ['route' => ['forgotPassword.post'], 'files' => true, 'role' => 'form', 'class'=>'login-form',  'id'=>'frmForgotPsw', 'method'=>'post']) }}
        <div class="modal-body text-left">
            <div class="modal-logindata">
                <div class="custom-input-box form-group">
                    <i class="input-icon fas fa-user prefix grey-text"></i>
                    <input type="email" name="email" id="email" class="custom-input form-control check-email" placeholder="Enter Email" required>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="javascript:void(0);" class="forgot-txt float-right text-muted" onclick="loginPopup()">
                        <small>Login</small>
                    </a>
                </div>
            </div>
        </div>
        <div class="modal-footer justify-content-end">
            {{ Form::button(trans('frontend/common.submit'),['type'=>'submit','class'=>'btn btn-login','id'=>'btnForgot', 'data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> loading','data-original-text'=>trans('frontend/common.submit')])}}
        </div>
        {{ Form::close() }}

    </div>
</div>

<script src="{{asset('frontend/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('frontend/js/pages/forgot-password.js')}}"></script>
