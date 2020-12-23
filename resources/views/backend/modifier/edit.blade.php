<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">{{trans('backend/modifier.edit_modifier')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::model($modifierData, ['route' => ['admin.modifier.update',$modifierData->uuid], 'files' => true, 'role' => 'form', 'id'=>'frmModifier', 'method'=>'put']) }}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="name">{{trans('backend/modifier.name')}}</label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" autocomplete="off"
                               placeholder="Enter role name" value="{{$modifierData->name}}" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 required">
                    <div class="form-group">
                        <label for="role_status">{{trans('backend/common.status')}}</label>
                        <select name="status" id="status" class="form-control form-control-sm" required>
                            <option value="1" @if(isset($modifierData)) {{ ($modifierData->role_status=="1")? "selected" : "" }}@endif>{{trans('backend/common.active')}}</option>
                            <option value="0" @if(isset($modifierData)) {{ ($modifierData->role_status=="0")? "selected" : "" }}@endif>{{trans('backend/common.inactive')}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="name">{{trans('backend/modifier.is_default')}}</label>
                        <br>
                        <label class="switch">
                            <input type="checkbox" name="is_default" value="1"
                                   @if(isset($modifierData) && $modifierData->is_default == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="name">{{trans('backend/modifier.is_global')}}</label>
                        <br>
                        <label class="switch">
                            <input type="checkbox" name="is_global" value="1"
                                   @if(isset($modifierData) && $modifierData->is_global == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
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
<script src="{{asset('backend/dist/js/pages/modifier.js')}}"></script>
