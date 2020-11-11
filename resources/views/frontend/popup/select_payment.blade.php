@php
    $userData = \Illuminate\Support\Facades\Auth::guard('fronts')->user();
@endphp
<style>
    .list-group.list-group-root {
        padding: 0;
        overflow: hidden;
    }

    .list-group.list-group-root .list-group {
        margin-bottom: 0;
    }

    .list-group.list-group-root .list-group-item {
        border-radius: 0;
        border-width: 1px 0 0 0;
    }

    .list-group.list-group-root > .list-group-item:first-child {
        border-top-width: 0;
    }

    .list-group.list-group-root > .list-group > .list-group-item {
        padding-left: 60px;
    }

    .list-group.list-group-root > .list-group > .list-group > .list-group-item {
        padding-left: 45px;
    }

    .list-group-item .glyphicon {
        margin-right: 5px;
    }
</style>
<div id='loader'></div>
<div class="custom-modal modal fade" id="myModel" tabindex="-1" role="dialog" aria-labelledby="pop"
     aria-hidden="true"></div>
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="custom-modal-header modal-header">
            <h5 class="custom-modal-title modal-title" id="exampleModalLongTitle">Payment</h5>
            <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="list-group list-group-root well">
                @if(isset($paymentData) && !empty($paymentData))
                    @foreach($paymentData as $key => $value)
                        @if(count($value->sub_payment) > 0)
                        <a href="#item-{{$key}}" class="list-group-item list-group-item-action" data-id="{{$value->payment_id}}" id="livePayment" data-toggle="collapse">
                            <img src="{{asset($value->asset_path)}}" width="60"> <strong>{{$value->name}}</strong><span
                                    class="fa fa-chevron-right float-right mt-3"></span></a>

                            <div class="list-group collapse" id="item-{{$key}}">
                                @foreach($value->sub_payment as $skey => $svalue)
                                    <a href="javascript:void(0);" class="list-group-item list-group-item-action" data-id="{{$svalue->payment_id}}"
                                       onclick="createOrder(this,'{{$branchSlug}}','{{$mobile}}','{{$email}}');">
                                        <img src="{{asset($svalue->asset_path)}}" width="60">
                                        {{$svalue->name}}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <a href="javascript:void(0);" class="list-group-item list-group-item-action" data-id="{{$value->payment_id}}" id="livePayment"
                               onclick="createOrder(this,'{{$branchSlug}}','{{$mobile}}','{{$email}}');">
                                <img src="{{asset($value->asset_path)}}" width="60"> <strong>{{$value->name}}</strong><span
                                        class="fa fa-chevron-right float-right mt-3"></span></a>
                        @endif
                    @endforeach
                @endif

                {{--<a href="#item-1" class="list-group-item list-group-item-action" data-id="1" id="livePayment" data-toggle="collapse"><img
                            src="{{asset('uploads/payment/ipay88.png')}}" width="60"> <strong>Card</strong><span
                            class="fa fa-chevron-right float-right mt-3"></span></a>
                <div class="list-group collapse" id="item-1">

                    <a href="javascript:void(0);" class="list-group-item">
                        <i class="fa fa-chevron-right"></i> Visa
                    </a>
                    <a href="javascript:void(0);" class="list-group-item">
                        <i class="fa fa-chevron-right"></i>Master
                    </a>
                </div>

                <a href="#item-2" class="list-group-item list-group-item-action" data-id="2"
                   onclick="createOrder(this,'{{$branchSlug}}','{{$mobile}}','{{$email}}');">
                    <img src="{{asset('uploads/payment/cod88.png')}}"
                         width="60"> <strong>Cash</strong><span class="fa fa-chevron-right float-right mt-3"></span>
                </a>
                <div class="list-group collapse" id="item-2">

                    <a href="javascript:void(0);" class="list-group-item" data-toggle="collapse">
                        <i class="fa fa-chevron-right"></i>Item 2.1
                    </a>
                </div>--}}

            </div>

        </div>
        <div class="modal-footer justify-content-center">
            <div class="modal-add-btn">
                <div class="row">

                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('frontend/js/pages/checkout.js')}}"></script>
<script>
    $(window).load(function() {
        //$("#loader").fadeOut(1000);
    });
</script>



