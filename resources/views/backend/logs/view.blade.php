<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">View Logs
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        {{trans('backend/common.details')}}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                <strong>{{trans('backend/logs.type')}}</strong>:
                                @if($logsData->type == 1)
                                    <span>Admin</span>
                                @elseif($logsData->type == 2)
                                    <span>Web</span>
                                @else
                                    <span>App</span>
                                @endif
                            </p>
                            <p><strong>{{trans('backend/logs.file_name')}}</strong>:
                               <span>{{$logsData->file_name}}</span>
                            </p>
                            <p><strong>{{trans('backend/logs.function')}}</strong>:
                                <span>{{$logsData->function}}</span>
                            </p>
                            <p><strong>{{trans('backend/logs.ip_address')}}</strong>:
                                <span>{{$logsData->ip_address}}</span>
                            </p>
                            <p><strong>{{trans('backend/common.created_by')}}</strong>:
                                <span>{{$logsData->user_name}}</span>
                            </p>
                            <p><strong>{{trans('backend/common.created_at')}}</strong>:
                                <span>{{$logsData->created_at}}</span>
                            </p>
                        </div>

                        <div class="col-md-12">
                            <p><strong>{{trans('backend/common.description')}}</strong></p>
                            <span id="description_data">{!!($logsData->details)!!}</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
