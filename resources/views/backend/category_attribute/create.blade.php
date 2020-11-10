<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">{{trans('backend/attributes.add_category_attribute')}}
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::model(null, ['route' => ['admin.category_attribute.store'], 'files' => true, 'role' => 'form', 'id'=>'frmAttributes', 'method'=>'post']) }}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/attributes.name')}}</label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" autocomplete="off"
                               placeholder="Enter name" required/>
                    </div>
                </div>
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="role_status">{{trans('backend/common.status')}}</label>
                        <select name="status" id="status" class="form-control form-control-sm" required>
                            <option value="1">{{trans('backend/common.active')}}</option>
                            <option value="0">{{trans('backend/common.inactive')}}</option>
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
<script src="{{asset('backend/dist/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/dist/js/pages/category_attribute.js')}}"></script>
