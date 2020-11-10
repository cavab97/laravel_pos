@if(isset($cityData))
    {{ Form::model($cityData, ['route' => ['admin.city.update',$cityData->city_id], 'files' => true, 'role' => 'form', 'id'=>'frmCity', 'method'=>'put', 'class'=>'forms-sample']) }}
@else
    {{ Form::model(null, ['route' => 'admin.city.store', 'files' => true, 'role' => 'form', 'id'=>'frmCity', 'method'=>'post', 'class'=>'forms-sample']) }}
@endif
<input type="hidden" id="city_id" value="0"/>
<div class="modal-body">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('country_id',trans('backend/city.country')) }}
                <input type="text" class="form-control" value="{{$countries->name}}" readonly/>
                <input type="hidden" name="country_id" value="{{$countries->country_id}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 required">
            <div class="form-group">
                {{ Form::label('state',trans('backend/city.select_state')) }}
                <select id="state" name="state" class="form-control" required>
                    <option value="">{{trans('backend/city.select_state')}}</option>
                    @php
                        foreach($stateList as $key => $value) {
                         $selected = '';
                         if(isset($cityData)){
                          if($value->state_id == $cityData->state_id){
                                    $selected = 'selected';
                                }
                         }
                            echo '<option value="'.$value->state_id.'" '.$selected.' >'.$value->name.'</option>';
                        }
                    @endphp
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 required">
            <div class="form-group">
                {{ Form::label('name',trans('backend/city.name')) }}
                {{ Form::text('name', old('name'), ["required","class"=>"form-control","placeholder"=>trans('backend/city.enter_name'),"id"=>"name","name"=>"name","maxlength"=>100]) }}
            </div>
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