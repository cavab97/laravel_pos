@php
    $permission = new \App\Models\Permissions();
    $lang = \App\Models\Languages::getBackLang();
@endphp

@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/dist/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/order.js')}}"></script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/order.order')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.order.index')}}">{{trans('backend/order.order')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{trans('backend/common.view')}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="custom-content content">
            <div class="row">
                <div id="display_message" class="col-12">
                    <div class="alert alert-success"></div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/order.view_order')}}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fa fa-bars" aria-hidden="true"></i>
                                                {{trans('backend/order.order_details')}}</h3>
                                            <input type='hidden'
                                                   value="float-right"
                                                   id="flot_type">
                                            <div id="order_status_name">
                                                <?php
                                                $order_status_badge = '';
                                                $order_status = '';
                                                if ($orderData->order_status == 1) {
                                                    $order_status_badge = 'badge badge-info';
                                                    $order_status = 'New';
                                                } elseif ($orderData->order_status == 2) {
                                                    $order_status = 'Ongoing';
                                                    $order_status_badge = 'badge badge-info';
                                                } elseif ($orderData->order_status == 3) {
                                                    $order_status_badge = 'badge badge-danger';
                                                    $order_status = 'Cancelled';
                                                } elseif ($orderData->order_status == 4) {
                                                    $order_status_badge = 'badge badge-success';
                                                    $order_status = 'Completed';
                                                } elseif ($orderData->order_status == 5) {
                                                    $order_status_badge = 'badge badge-info';
                                                    $order_status = 'Refunded';
                                                }
                                                ?>
                                                <span class="{{$order_status_badge}} float-right"
                                                      id="order_status_name">{{$order_status}}</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/order.invoice_no')}}</strong>: <span
                                                                id="order_name">{{$orderData->invoice_no}}</span>
                                                    </p>
                                                    <p><strong>{{trans('backend/order.customer_name')}}</strong>
                                                        : {{$orderData->customer_name}}
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/order.payment_status')}}</strong>:
                                                        @if($orderData->payment_status == 1)
                                                            <span class="badge badge-success">{{trans('backend/order.complete')}}</span>
                                                        @elseif($orderData->payment_status == 2)
                                                            <span class="badge badge-danger">{{trans('backend/order.failed')}}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{trans('backend/order.pending')}}</span>
                                                        @endif
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/order.payment')}}</strong>:
                                                        {{$orderData->payment_name}}
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/order.order_source')}}</strong>: {{($orderData->order_source == 1)?'Web':'App'}}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">

                                                    <p>
                                                        <strong>{{trans('backend/order.order_date')}}</strong>:
                                                        <span>@if($orderData->order_date){{date('d-m-Y g:i A', strtotime($orderData->order_date))}}@endif</span>
                                                    </p>
                                                    @if(!empty($orderData->remark))
                                                        <p>
                                                            <strong>{{trans('backend/order.remark')}}</strong>:
                                                            {{$orderData->remark}}
                                                        </p>
                                                    @endif
                                                    @if(!empty($orderData->approval_code))
                                                        <p>
                                                            <strong>{{trans('backend/order.approval_code')}}</strong>:
                                                            {{$orderData->approval_code}}
                                                        </p>
                                                    @endif
                                                    @if(!empty($orderData->reference_number))
                                                        <p>
                                                            <strong>{{trans('backend/order.reference_number')}}</strong>:
                                                            {{$orderData->reference_number}}
                                                        </p>
                                                    @endif
                                                </div>
                                                @if($orderData->order_status_id == 3)
                                                    <div class="col-md-6">
                                                        <p>
                                                            <strong>{{trans('backend/order.cancel_reason')}}</strong>:
                                                            {{$orderData->cancel_reason}}
                                                        </p>
                                                    </div>
                                                @endif

                                                @if(!empty($orderData->cancel_comment))
                                                    <div class="col-md-6">
                                                        <p>
                                                            <strong>{{trans('backend/order.comment')}}</strong>:
                                                            {{$orderData->cancel_comment}}
                                                        </p>
                                                    </div>
                                                @endif
                                                <div class="col-md-12">
                                                    @if($orderData->payment_method_id == 4)
                                                        <p>
                                                            <strong>{{trans('backend/order.payment_type')}}</strong>:
                                                            <span
                                                            >{{trans('backend/order.payment_type_cash')}}</span>
                                                        </p>

                                                    @elseif($orderData->payment_method_id != 0 && $orderData->payment_method_id != 4)
                                                        <p>
                                                            <strong>{{trans('backend/order.payment_type')}}</strong>:
                                                            <span>
														<a href="javascript:void(0);"
                                                           onclick="viewPaymentResponse('{{$orderData->uuid}}')">{{trans('backend/order.payment_type_card')}}</a>
                                                            </span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="{{($lang == 'ar') ? 'fab fa fa-product-hunt':'fab fa-product-hunt'}} nav-icon"
                                                   aria-hidden="true"></i>
                                                {{trans('backend/order.product_details')}}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="table-responsive">
                                                    <table id="productList" class="table">
                                                        <thead>
                                                        <tr>
                                                            <th id="id">{{trans('backend/common.no')}}</th>
                                                            <th>{{trans('backend/common.name')}}</th>
                                                            <th>{{trans('backend/order.quantity')}}</th>
                                                            <th>{{trans('backend/order.price')}}</th>
                                                            <th>{{trans('backend/order.discount')}}</th>
                                                            <th>{{trans('backend/order.total')}}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @php $discount_price_sum = 0; $total = 0; @endphp
                                                        @foreach($orderDetails as $key => $value)
                                                            <tr>
                                                                <td>{{++$key}}</td>
                                                                <td>{{$value['product_name']}}</td>
                                                                <td>{{$value['detail_qty']}}</td>
                                                                <td>
                                                                    {{number_format($value['product_price'],2)}}
                                                                </td>

                                                                <td>
                                                                    {{number_format($value['product_discount'],2)}}

                                                                </td>
                                                                <td>
                                                                    {{number_format($value['detail_amount'],2)}}
                                                                </td>
                                                            </tr>

                                                        @endforeach
                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <td><strong>{{trans('backend/order.sub_total')}}</strong>
                                                            </td>
                                                            <td>
                                                                <strong>{{number_format($orderData->sub_total,2)}}</strong>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="border-0" colspan="4"></td>
                                                            <td>
                                                                <strong>{{trans('backend/order.voucher_discount')}}</strong>
                                                            </td>
                                                            <td>
                                                                <strong>{{number_format($orderData->voucher_amount,2)}}</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="border-0" colspan="4"></td>
                                                            <td>
                                                                <strong>{{trans('backend/order.sub_total_after_discount')}}</strong>
                                                            </td>
                                                            <td>
                                                                <strong>{{number_format($orderData->sub_total_after_discount,2)}}</strong>
                                                            </td>
                                                        </tr>
                                                        @if(isset($orderData->taxDetail) && !empty($orderData->taxDetail))

                                                            @foreach($orderData->taxDetail as $key=>$value)
                                                                <tr>
                                                                    <td class="border-0" colspan="4"></td>

                                                                    <td>
                                                                        <strong>{{$value['taxCode']}}({{$value['rate']}}
                                                                            %)</strong>
                                                                    </td>
                                                                    <td>
                                                                        <strong>{{number_format($value['taxAmount'],2)}}</strong>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        <tr>
                                                            <td class="border-0" colspan="4"></td>

                                                            <td><strong>{{trans('backend/order.total')}}</strong>
                                                            </td>
                                                            <td>
                                                                <strong>{{number_format($orderData->grand_total,2)}}</strong>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
