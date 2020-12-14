<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\CartSubDetail;
use App\Models\Customer;
use App\Models\CustomerLiquorInventory;
use App\Models\CustomerLiquorInventoryLog;
use App\Models\Helper;
use App\Models\Order;
use App\Models\OrderAttributes;
use App\Models\OrderCancel;
use App\Models\OrderDetail;
use App\Models\OrderModifier;
use App\Models\OrderPayment;
use App\Models\ProductStoreInventory;
use App\Models\ProductStoreInventoryLog;
use App\Models\Shift;
use App\Models\ShiftDetails;
use App\Models\Terminal;
use App\Models\TerminalLog;
use App\Models\VoucherHistory;
use App\User;
use http\Url;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SyncOrderController extends Controller
{
    /*
     * @method   : Creatbulkorders
     * @params   : JSON
     * @respose  : as like request with server id
     */

    public function createBulkOrders(Request $request, $locale)
    {
        Helper::log('Bulk Order AppOrderData Synch : Start');
        App::setLocale($locale);
        DB::beginTransaction();
        try {
            $getOrders = $request->orders;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;

            if (empty($getOrders)) {
                Helper::log('AppOrderData Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } elseif (empty($terminalId)) {
                Helper::log('AppOrderData Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($branchId)) {
                Helper::log('AppOrderData Table Synch : Branch Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            } else {
                $timeStart = microtime(true);
                if (\GuzzleHttp\json_decode($getOrders, true)) {
                    $getOrdersArray = \GuzzleHttp\json_decode($getOrders, true);  // convert to array
                    if (is_array($getOrdersArray)) {  // valid array
                        $orders = new Order();
                        $ordersitems = new OrderDetail();
                        $ordersitemssub = new OrderModifier();
                        $ordersitemssubAt = new OrderAttributes();
                        $productStock = new ProductStoreInventory();
                        $ordersvoucher = new VoucherHistory();

                        $pushOrders = [];
                        foreach ($getOrdersArray as $setOrdersArray) {
                            $isExistingOrder = array_key_exists("server_id", $setOrdersArray);
                            $orderFlag = 1;
                            if ($isExistingOrder) {
                                $serverId = $setOrdersArray['server_id'];
                                $orders = Order::where('order_id', $serverId)->first();
                                if (empty($orders)) {
                                    $orders = new Order();

                                    $orderStatus = $setOrdersArray['order_status'];
                                    $branch_id = $setOrdersArray['branch_id'];
                                    $terminal_id = $setOrdersArray['terminal_id'];

                                    $orders->uuid = Helper::getUuid();
                                    $orders->customer_id = ($setOrdersArray['customer_id'] != "") ? $setOrdersArray['customer_id'] : NULL;
                                    $orders->branch_id = $branch_id;
                                    $orders->terminal_id = $setOrdersArray['terminal_id'];
                                    $orders->app_id = $setOrdersArray['app_id'];
                                    //$orders->table_no = $setOrdersArray['table_no'];
                                    $orders->table_id = $setOrdersArray['table_id'];
                                    $orders->pax = $setOrdersArray['pax'];
                                    $orders->invoice_no = $setOrdersArray['invoice_no'];
                                    $orders->tax_percent = $setOrdersArray['tax_percent'];
                                    $orders->tax_amount = $setOrdersArray['tax_amount'];
                                    $orders->tax_json = $setOrdersArray['tax_json'];
                                    $orders->service_charge_percent = $setOrdersArray['service_charge_percent'];
                                    $orders->service_charge = $setOrdersArray['service_charge'];
                                    $orders->voucher_id = $setOrdersArray['voucher_id'];
                                    $orders->voucher_amount = $setOrdersArray['voucher_amount'];
                                    $orders->sub_total = $setOrdersArray['sub_total'];
                                    $orders->sub_total_after_discount = $setOrdersArray['sub_total_after_discount'];
                                    $orders->grand_total = $setOrdersArray['grand_total'];
                                    $orders->rounding_amount = $setOrdersArray['rounding_amount'];
                                    $orders->order_source = isset($setOrdersArray['order_source']) ? $setOrdersArray['order_source'] : "2";
                                    $orders->order_status = $setOrdersArray['order_status'];
                                    $orders->order_item_count = $setOrdersArray['order_item_count'];
                                    $orders->order_date = $setOrdersArray['order_date'];
                                    $orders->order_by = $setOrdersArray['order_by'];
                                    $orders->updated_at = $setOrdersArray['updated_at'];
                                    $orders->updated_by = ($setOrdersArray['updated_by'] != 0) ? $setOrdersArray['updated_by'] : NULL;

                                    $orders = Order::create($orders->toArray());
                                    $ordersId = $orders->order_id;  //get order id

                                    $setOrdersArray['server_id'] = $ordersId;
                                    $pushOrders[] = $ordersId;
                                    //add order items
                                    $getOrdersDetails = $setOrdersArray['order_detail'];
                                    if (!empty($getOrdersDetails) && is_array($getOrdersDetails) && $ordersId != '') {
                                        foreach ($getOrdersDetails as $setOrdersItem) {

                                            $productId = $setOrdersItem['product_id'];
                                            $categoryId = $setOrdersItem['category_id'];
                                            $detail_qty = $setOrdersItem['detail_qty'];
                                            $ordersitems->uuid = Helper::getUuid();
                                            $ordersitems->order_id = $ordersId;
                                            $ordersitems->branch_id = $branch_id;
                                            $ordersitems->terminal_id = $terminal_id;
                                            $ordersitems->app_id = $setOrdersItem['app_id'];
                                            $ordersitems->order_app_id = $setOrdersItem['order_app_id'];
                                            $ordersitems->product_id = $productId;
                                            $ordersitems->category_id = $categoryId;
                                            $ordersitems->product_price = $setOrdersItem['product_price'];
                                            $ordersitems->product_old_price = $setOrdersItem['product_old_price'];
                                            $ordersitems->product_discount = $setOrdersItem['product_discount'];
                                            $ordersitems->product_detail = $setOrdersItem['product_detail'];
                                            $ordersitems->category_id = $setOrdersItem['category_id'];
                                            $ordersitems->detail_amount = ($setOrdersItem['detail_amount'] != null) ? $setOrdersItem['detail_amount'] : 0;
                                            $ordersitems->detail_qty = $setOrdersItem['detail_qty'];
                                            $ordersitems->issetMeal = $setOrdersItem['issetMeal'];
                                            $ordersitems->setmeal_product_detail = $setOrdersItem['setmeal_product_detail'];
                                            $ordersitems->detail_status = ($setOrdersItem['detail_status'] != null) ? $setOrdersItem['detail_status'] : 1;
                                            $ordersitems->detail_datetime = $setOrdersItem['detail_datetime'];
                                            $ordersitems->detail_by = $setOrdersItem['detail_by'];
                                            $ordersitems->updated_at = $setOrdersItem['updated_at'];
                                            $ordersitems->updated_by = ($setOrdersItem['updated_by'] != 0) ? $setOrdersItem['updated_by'] : NULL;

                                            $ordersitems = OrderDetail::create($ordersitems->toArray());

                                            $getProvariants = $setOrdersItem['order_modifier'];
                                            if (is_array($getProvariants) && !empty($getProvariants)) {
                                                foreach ($getProvariants as $setProvariants) {

                                                    $ordersitemssub->uuid = Helper::getUuid();
                                                    $ordersitemssub->detail_id = $ordersitems->detail_id;
                                                    $ordersitemssub->app_id = $setOrdersItem['app_id']; // application auto inc id
                                                    $ordersitemssub->order_app_id = $setOrdersItem['order_app_id'];
                                                    $ordersitemssub->detail_app_id = $setOrdersItem['detail_app_id'];
                                                    $ordersitemssub->order_id = $ordersId;
                                                    $ordersitemssub->terminal_id = $terminal_id;
                                                    $ordersitemssub->product_id = $setOrdersItem['product_id'];
                                                    $ordersitemssub->modifier_id = $setProvariants['modifier_id'];
                                                    $ordersitemssub->om_amount = $setProvariants['om_amount'];
                                                    $ordersitemssub->om_status = $setProvariants['om_status'];
                                                    $ordersitemssub->om_datetime = $setProvariants['om_datetime'];
                                                    $ordersitemssub->om_by = $setProvariants['om_by'];
                                                    $ordersitemssub->updated_at = $setProvariants['updated_at'];
                                                    $ordersitemssub->updated_by = ($setProvariants['updated_by'] != 0) ? $setProvariants['updated_by'] : NULL;

                                                    $ordersitemssub = OrderModifier::create($ordersitemssub->toArray());
                                                }
                                            }

                                            $getProvariantsAtt = $setOrdersItem['order_attributes'];
                                            if (is_array($getProvariantsAtt) && !empty($getProvariantsAtt)) {
                                                foreach ($getProvariantsAtt as $setProvariantsAtt) {

                                                    $ordersitemssubAt->uuid = Helper::getUuid();
                                                    $ordersitemssubAt->detail_id = $ordersitems->detail_id;
                                                    $ordersitemssubAt->app_id = $setProvariantsAtt['app_id']; // application auto inc id
                                                    $ordersitemssubAt->order_app_id = $setProvariantsAtt['order_app_id'];
                                                    $ordersitemssubAt->detail_app_id = $setProvariantsAtt['detail_app_id'];
                                                    $ordersitemssubAt->order_id = $ordersId;
                                                    $ordersitemssubAt->terminal_id = $terminal_id;
                                                    $ordersitemssubAt->product_id = $setOrdersItem['product_id'];
                                                    $ordersitemssubAt->attribute_id = $setProvariantsAtt['attribute_id'];
                                                    $ordersitemssubAt->attr_price = $setProvariantsAtt['attr_price'];
                                                    $ordersitemssubAt->ca_id = $setProvariantsAtt['ca_id'];
                                                    $ordersitemssubAt->oa_status = $setProvariantsAtt['oa_status'];
                                                    $ordersitemssubAt->oa_datetime = $setProvariantsAtt['oa_datetime'];
                                                    $ordersitemssubAt->oa_by = $setProvariantsAtt['oa_by'];
                                                    $ordersitemssubAt->updated_at = $setProvariantsAtt['updated_at'];

                                                    $ordersitemssubAt = OrderAttributes::create($ordersitemssubAt->toArray());
                                                }
                                            }

                                            /* Update Inventory */
                                            $productInventory = ProductStoreInventory::where(['product_id'=>$productId,'branch_id'=>$branchId])->first();
                                            if(!empty($productInventory)) {
                                                $main_qty = $productInventory->qty;
                                                $final_total_qty = ($main_qty - $detail_qty);
                                                $updateStock = [
                                                    'qty' => $final_total_qty,
                                                    'updated_at' => config('constants.date_time')
                                                ];
                                                ProductStoreInventory::where(['branch_id' => $branchId, 'product_id' => $productId])->update($updateStock);
                                            }
                                        }
                                    }

                                    // add payemnt
                                    $getPaymentdetail = $setOrdersArray['order_payment'];
                                    if (!empty($getPaymentdetail) && is_array($getPaymentdetail)) {
                                        foreach ($getPaymentdetail as $setPaymentdetail) {
                                            $orderspayments = new OrderPayment();
                                            $isExistingOrderItem = array_key_exists("server_id", $setPaymentdetail);
                                            if ($isExistingOrderItem) {
                                                $serverId = $setPaymentdetail['server_id'];
                                                $orderspayments = OrderPayment::where('op_id', $serverId)->first();
                                                if (empty($orderspayments)) {

                                                    $orderspayments = new OrderPayment();

                                                    $orderspayments->uuid = Helper::getUuid();
                                                    $orderspayments->order_id = $ordersId;
                                                    $orderspayments->branch_id = $branch_id;
                                                    $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                                    $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                    $orderspayments->order_app_id = $setPaymentdetail['order_app_id'];
                                                    $orderspayments->is_split = $setPaymentdetail['is_split'];
                                                    $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                                    $orderspayments->remark = $setPaymentdetail['remark'];
                                                    $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                                    $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                                    $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                                    $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                                    $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                                    $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                                    $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                                    $orderspayments->op_status = $setPaymentdetail['op_status'];
                                                    $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                                    $orderspayments->op_by = $setPaymentdetail['op_by'];
                                                    $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                    $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                                    $orderspayments = OrderPayment::create($orderspayments->toArray());
                                                    $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                                    $setPaymentdetail['server_id'] = $orderspaymentsId;
                                                } else {
                                                    $orderspayments = new OrderPayment();
                                                    $orderspayments->order_id = $ordersId;
                                                    $orderspayments->branch_id = $branch_id;
                                                    $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                                    $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                    $orderspayments->order_app_id = $setPaymentdetail['order_app_id']; // application auto inc id
                                                    $orderspayments->is_split = $setPaymentdetail['is_split'];
                                                    $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                                    $orderspayments->remark = $setPaymentdetail['remark'];
                                                    $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                                    $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                                    $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                                    $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                                    $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                                    $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                                    $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                                    $orderspayments->op_status = $setPaymentdetail['op_status'];
                                                    $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                                    $orderspayments->op_by = $setPaymentdetail['op_by'];
                                                    $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                    $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                                    OrderPayment::where('op_id',$serverId)->update($orderspayments->toArray());
                                                    $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                                    $setPaymentdetail['server_id'] = $orderspaymentsId;
                                                }
                                            } else {
                                                $orderspayments = new OrderPayment();
                                                $orderspayments->uuid = Helper::getUuid();
                                                $orderspayments->order_id = $ordersId;
                                                $orderspayments->branch_id = $branch_id;
                                                $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                                $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                $orderspayments->order_app_id = $setPaymentdetail['order_app_id']; // application auto inc id
                                                $orderspayments->is_split = $setPaymentdetail['is_split'];
                                                $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                                $orderspayments->remark = $setPaymentdetail['remark'];
                                                $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                                $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                                $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                                $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                                $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                                $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                                $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                                $orderspayments->op_status = $setPaymentdetail['op_status'];
                                                $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                                $orderspayments->op_by = $setPaymentdetail['op_by'];
                                                $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                                $orderspayments = OrderPayment::create($orderspayments->toArray());
                                                $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                                $setPaymentdetail['server_id'] = $orderspaymentsId;
                                            }
                                        }
                                    }

                                    // Voucher History
                                    $getVoucherdetail = $setOrdersArray['voucher_history'];
                                    if(!empty($getVoucherdetail) && is_array($getVoucherdetail)){
                                        foreach ($getVoucherdetail as $setVoucherdetail) {
                                            $ordersvoucher = new VoucherHistory();
                                            $isExistingVoucherItem = array_key_exists("server_id", $setVoucherdetail);
                                            if ($isExistingVoucherItem) {
                                                $serverId = $setVoucherdetail['server_id'];
                                                $ordersvoucher = VoucherHistory::where('voucher_history_id', $serverId)->first();
                                                if (empty($ordersvoucher)) {

                                                    $ordersvoucher = new VoucherHistory();

                                                    $ordersvoucher->uuid = Helper::getUuid();
                                                    $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                                    $ordersvoucher->order_id = $ordersId;
                                                    $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                                    $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                                    $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                                    $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                                    $ordersvoucher->amount = $setVoucherdetail['amount'];
                                                    $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                                    $ordersvoucher = VoucherHistory::create($ordersvoucher->toArray());
                                                    $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                                    $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                                } else {
                                                    $ordersvoucher->order_id = $ordersId;
                                                    $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                                    $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                                    $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                                    $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                                    $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                                    $ordersvoucher->amount = $setVoucherdetail['amount'];
                                                    $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                                    VoucherHistory::where('voucher_history_id',$serverId)->update($ordersvoucher->toArray());
                                                    $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                                    $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                                }
                                            } else {
                                                $ordersvoucher->uuid = Helper::getUuid();
                                                $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                                $ordersvoucher->order_id = $ordersId;
                                                $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                                $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                                $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                                $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                                $ordersvoucher->amount = $setVoucherdetail['amount'];
                                                $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                                $ordersvoucher = VoucherHistory::create($ordersvoucher->toArray());
                                                $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                                $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                            }
                                        }
                                    }

                                } else {

                                    $orderStatus = $setOrdersArray['order_status'];
                                    $branch_id = $setOrdersArray['branch_id'];
                                    $terminal_id = $setOrdersArray['terminal_id'];

                                    $orders->customer_id = ($setOrdersArray['customer_id'] != "") ? $setOrdersArray['customer_id'] : NULL;
                                    $orders->branch_id = $branch_id;
                                    $orders->terminal_id = $setOrdersArray['terminal_id'];
                                    $orders->app_id = $setOrdersArray['app_id'];
                                    //$orders->table_no = $setOrdersArray['table_no'];
                                    $orders->table_id = $setOrdersArray['table_id'];
                                    $orders->pax = $setOrdersArray['pax'];
                                    $orders->invoice_no = $setOrdersArray['invoice_no'];
                                    $orders->tax_percent = $setOrdersArray['tax_percent'];
                                    $orders->tax_amount = $setOrdersArray['tax_amount'];
                                    $orders->tax_json = $setOrdersArray['tax_json'];
                                    $orders->service_charge_percent = $setOrdersArray['service_charge_percent'];
                                    $orders->service_charge = $setOrdersArray['service_charge'];
                                    $orders->voucher_id = $setOrdersArray['voucher_id'];
                                    $orders->voucher_amount = $setOrdersArray['voucher_amount'];
                                    $orders->sub_total = $setOrdersArray['sub_total'];
                                    $orders->sub_total_after_discount = $setOrdersArray['sub_total_after_discount'];
                                    $orders->grand_total = $setOrdersArray['grand_total'];
                                    $orders->rounding_amount = $setOrdersArray['rounding_amount'];
                                    $orders->order_source = isset($setOrdersArray['order_source']) ? $setOrdersArray['order_source'] : "2";
                                    $orders->order_status = $setOrdersArray['order_status'];
                                    $orders->order_item_count = $setOrdersArray['order_item_count'];
                                    $orders->order_date = $setOrdersArray['order_date'];
                                    $orders->order_by = $setOrdersArray['order_by'];
                                    $orders->updated_at = $setOrdersArray['updated_at'];
                                    $orders->updated_by = ($setOrdersArray['updated_by'] != 0) ? $setOrdersArray['updated_by'] : NULL;

                                    Order::where('order_id',$serverId)->update($orders->toArray());
                                    $ordersId = $orders->order_id;  //get order id

                                    $setOrdersArray['serverId'] = $ordersId;
                                    $pushOrders[] = $ordersId;
                                    //add order items
                                    $getOrdersDetails = $setOrdersArray['order_detail'];
                                    if (!empty($getOrdersDetails) && is_array($getOrdersDetails) && $ordersId != '') {
                                        foreach ($getOrdersDetails as $setOrdersItem) {
                                            $isExistingOrderItem = array_key_exists("server_id", $setOrdersItem);
                                            if ($isExistingOrderItem) {
                                                $serverId = $setOrdersItem['server_id'];
                                                $ordersitems = OrderDetail::where('order_id', $serverId)->first();
                                                if (empty($ordersitems)) {
                                                    $ordersitems = new OrderDetail();
                                                }
                                            }

                                            $productId = $setOrdersItem['product_id'];
                                            $categoryId = $setOrdersItem['category_id'];
                                            $detail_qty = $setOrdersItem['detail_qty'];
                                            $ordersitems->order_id = $ordersId;
                                            $ordersitems->branch_id = $branch_id;
                                            $ordersitems->terminal_id = $terminal_id;
                                            $ordersitems->app_id = $setOrdersItem['app_id'];
                                            $ordersitems->order_app_id = $setOrdersItem['order_app_id'];
                                            $ordersitems->product_id = $productId;
                                            $ordersitems->category_id = $categoryId;
                                            $ordersitems->product_price = $setOrdersItem['product_price'];
                                            $ordersitems->product_old_price = $setOrdersItem['product_old_price'];
                                            $ordersitems->product_discount = $setOrdersItem['product_discount'];
                                            $ordersitems->product_detail = $setOrdersItem['product_detail'];
                                            $ordersitems->category_id = $setOrdersItem['category_id'];
                                            $ordersitems->detail_amount = ($setOrdersItem['detail_amount'] != null) ? $setOrdersItem['detail_amount'] : 0;
                                            $ordersitems->detail_qty = $setOrdersItem['detail_qty'];
                                            $ordersitems->detail_status = ($setOrdersItem['detail_status'] != null) ? $setOrdersItem['detail_status'] : 1;
                                            $ordersitems->detail_datetime = $setOrdersItem['detail_datetime'];
                                            $ordersitems->detail_by = $setOrdersItem['detail_by'];
                                            $ordersitems->updated_at = $setOrdersItem['updated_at'];
                                            $ordersitems->updated_by = ($setOrdersItem['updated_by'] != 0) ? $setOrdersItem['updated_by'] : NULL;

                                            OrderDetail::where('detail_id',$setOrdersItem['detailId'])->update($ordersitems->toArray());

                                            $getProvariants = $setOrdersItem['order_modifier'];
                                            if (is_array($getProvariants) && !empty($getProvariants)) {
                                                foreach ($getProvariants as $setProvariants) {
                                                    $isExistingOrderItem = array_key_exists("server_id", $setProvariants);
                                                    if ($isExistingOrderItem) {
                                                        $serverId = $setProvariants['server_id'];
                                                        $ordersitemssub = OrderModifier::where('order_id', $serverId)->first();
                                                        if (empty($ordersitemssub)) {
                                                            $ordersitemssub = new OrderModifier();
                                                        }
                                                    }

                                                    $ordersitemssub->detail_id = $ordersitems->detail_id;
                                                    $ordersitemssub->app_id = $setOrdersItem['app_id']; // application auto inc id
                                                    $ordersitemssub->order_app_id = $setOrdersItem['order_app_id'];
                                                    $ordersitemssub->detail_app_id = $setOrdersItem['detail_app_id'];
                                                    $ordersitemssub->order_id = $ordersId;
                                                    $ordersitemssub->terminal_id = $terminal_id;
                                                    $ordersitemssub->product_id = $setOrdersItem['product_id'];
                                                    $ordersitemssub->modifier_id = $setProvariants['modifier_id'];
                                                    $ordersitemssub->om_amount = $setProvariants['om_amount'];
                                                    $ordersitemssub->om_status = $setProvariants['om_status'];
                                                    $ordersitemssub->om_datetime = $setProvariants['om_datetime'];
                                                    $ordersitemssub->om_by = $setProvariants['om_by'];
                                                    $ordersitemssub->updated_at = $setProvariants['updated_at'];
                                                    $ordersitemssub->updated_by = ($setProvariants['updated_by'] != 0) ? $setProvariants['updated_by'] : NULL;

                                                    OrderModifier::where('om_id',$setOrdersItem['om_id'])->update($ordersitemssub->toArray());
                                                }
                                            }

                                            $getProvariantsAtt = $setOrdersItem['order_attributes'];
                                            if (is_array($getProvariantsAtt) && !empty($getProvariantsAtt)) {
                                                foreach ($getProvariantsAtt as $setProvariantsAtt) {
                                                    $isExistingOrderItem = array_key_exists("server_id", $setProvariantsAtt);
                                                    if ($isExistingOrderItem) {
                                                        $serverId = $setProvariantsAtt['server_id'];
                                                        $ordersitemssubAt = OrderAttributes::where('order_id', $serverId)->first();
                                                        if (empty($ordersitemssubAt)) {
                                                            $ordersitemssubAt = new OrderAttributes();
                                                        }
                                                    }


                                                    $ordersitemssubAt->detail_id = $ordersitems->detail_id;
                                                    $ordersitemssubAt->app_id = $setProvariantsAtt['app_id']; // application auto inc id
                                                    $ordersitemssubAt->order_app_id = $setOrdersItem['order_app_id'];
                                                    $ordersitemssubAt->detail_app_id = $setOrdersItem['detail_app_id'];
                                                    $ordersitemssubAt->order_id = $ordersId;
                                                    $ordersitemssubAt->terminal_id = $terminal_id;
                                                    $ordersitemssubAt->product_id = $setOrdersItem['product_id'];
                                                    $ordersitemssubAt->attribute_id = $setProvariantsAtt['attribute_id'];
                                                    $ordersitemssubAt->attr_price = $setProvariantsAtt['attr_price'];
                                                    $ordersitemssubAt->ca_id = $setProvariantsAtt['ca_id'];
                                                    $ordersitemssubAt->oa_status = $setProvariantsAtt['oa_status'];
                                                    $ordersitemssubAt->oa_datetime = $setProvariantsAtt['oa_datetime'];
                                                    $ordersitemssubAt->oa_by = $setProvariantsAtt['oa_by'];
                                                    $ordersitemssubAt->updated_at = $setProvariantsAtt['updated_at'];
                                                    $ordersitemssubAt->updated_by = ($setProvariantsAtt['updated_by'] != 0) ? $setProvariantsAtt['updated_by'] : NULL;

                                                    OrderAttributes::where('oa_id',$setProvariantsAtt['oa_id'])->update($ordersitemssubAt->toArray());
                                                }
                                            }

                                            /* Update Inventory */
                                            $productInventory = ProductStoreInventory::where(['product_id'=>$productId,'branch_id'=>$branchId])->first();
                                            if(!empty($productInventory)) {
                                                $main_qty = $productInventory->qty;
                                                $final_total_qty = ($main_qty - $detail_qty);
                                                $updateStock = [
                                                    'qty' => $final_total_qty,
                                                    'updated_at' => config('constants.date_time')
                                                ];
                                                //ProductStoreInventory::where(['branch_id'=>$branchId,'product_id'=>$productId])->update($updateStock);
                                            }
                                        }
                                    }

                                    // add payemnt
                                    $getPaymentdetail = $setOrdersArray['order_payment'];
                                    if (!empty($getPaymentdetail) && is_array($getPaymentdetail)) {
                                        foreach ($getPaymentdetail as $setPaymentdetail) {
                                            $orderspayments = new OrderPayment();
                                            $isExistingOrderItem = array_key_exists("server_id", $setPaymentdetail);
                                            if ($isExistingOrderItem) {
                                                $serverId = $setPaymentdetail['server_id'];
                                                $orderspayments = OrderPayment::where('op_id', $serverId)->first();
                                                if (empty($orderspayments)) {

                                                    $orderspayments = new OrderPayment();

                                                    $orderspayments->uuid = Helper::getUuid();
                                                    $orderspayments->order_id = $ordersId;
                                                    $orderspayments->branch_id = $branch_id;
                                                    $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                                    $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                    $orderspayments->order_app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                    $orderspayments->is_split = $setPaymentdetail['is_split'];
                                                    $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                                    $orderspayments->remark = $setPaymentdetail['remark'];
                                                    $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                                    $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                                    $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                                    $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                                    $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                                    $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                                    $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                                    $orderspayments->op_status = $setPaymentdetail['op_status'];
                                                    $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                                    $orderspayments->op_by = $setPaymentdetail['op_by'];
                                                    $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                    $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                                    $orderspayments = OrderPayment::create($orderspayments->toArray());
                                                    $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                                    $setPaymentdetail['server_id'] = $orderspaymentsId;
                                                } else {
                                                    $orderspayments = new OrderPayment();
                                                    $orderspayments->order_id = $ordersId;
                                                    $orderspayments->branch_id = $branch_id;
                                                    $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                                    $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                    $orderspayments->order_app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                    $orderspayments->is_split = $setPaymentdetail['is_split'];
                                                    $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                                    $orderspayments->remark = $setPaymentdetail['remark'];
                                                    $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                                    $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                                    $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                                    $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                                    $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                                    $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                                    $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                                    $orderspayments->op_status = $setPaymentdetail['op_status'];
                                                    $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                                    $orderspayments->op_by = $setPaymentdetail['op_by'];
                                                    $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                    $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                                    OrderPayment::where('op_id',$serverId)->update($orderspayments->toArray());
                                                    $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                                    $setPaymentdetail['server_id'] = $orderspaymentsId;
                                                }
                                            } else {
                                                $orderspayments = new OrderPayment();
                                                $orderspayments->uuid = Helper::getUuid();
                                                $orderspayments->order_id = $ordersId;
                                                $orderspayments->branch_id = $branch_id;
                                                $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                                $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                $orderspayments->order_app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                $orderspayments->is_split = $setPaymentdetail['is_split'];
                                                $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                                $orderspayments->remark = $setPaymentdetail['remark'];
                                                $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                                $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                                $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                                $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                                $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                                $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                                $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                                $orderspayments->op_status = $setPaymentdetail['op_status'];
                                                $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                                $orderspayments->op_by = $setPaymentdetail['op_by'];
                                                $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                                $orderspayments = OrderPayment::create($orderspayments->toArray());
                                                $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                                $setPaymentdetail['server_id'] = $orderspaymentsId;
                                            }
                                        }
                                    }

                                    // Voucher History
                                    $getVoucherdetail = $setOrdersArray['voucher_history'];
                                    if(!empty($getVoucherdetail) && is_array($getVoucherdetail)){
                                        foreach ($getVoucherdetail as $setVoucherdetail) {
                                            $ordersvoucher = new VoucherHistory();
                                            $isExistingVoucherItem = array_key_exists("server_id", $setVoucherdetail);
                                            if ($isExistingVoucherItem) {
                                                $serverId = $setVoucherdetail['server_id'];
                                                $ordersvoucher = VoucherHistory::where('voucher_history_id', $serverId)->first();
                                                if (empty($ordersvoucher)) {

                                                    $ordersvoucher = new VoucherHistory();

                                                    $ordersvoucher->uuid = Helper::getUuid();
                                                    $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                                    $ordersvoucher->order_id = $ordersId;
                                                    $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                                    $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                                    $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                                    $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                                    $ordersvoucher->amount = $setVoucherdetail['amount'];
                                                    $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                                    $ordersvoucher = VoucherHistory::create($ordersvoucher->toArray());
                                                    $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                                    $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                                } else {
                                                    $ordersvoucher->order_id = $ordersId;
                                                    $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                                    $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                                    $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                                    $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                                    $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                                    $ordersvoucher->amount = $setVoucherdetail['amount'];
                                                    $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                                    VoucherHistory::where('voucher_history_id',$serverId)->update($ordersvoucher->toArray());
                                                    $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                                    $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                                }
                                            } else {
                                                $ordersvoucher->uuid = Helper::getUuid();
                                                $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                                $ordersvoucher->order_id = $ordersId;
                                                $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                                $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                                $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                                $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                                $ordersvoucher->amount = $setVoucherdetail['amount'];
                                                $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                                $ordersvoucher = VoucherHistory::create($ordersvoucher->toArray());
                                                $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                                $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                            }
                                        }
                                    }
                                }

                            } else {
                                $orders = new Order();

                                $orderStatus = $setOrdersArray['order_status'];
                                $branch_id = $setOrdersArray['branch_id'];
                                $terminal_id = $setOrdersArray['terminal_id'];

                                $orders->uuid = Helper::getUuid();
                                $orders->customer_id = ($setOrdersArray['customer_id'] != "") ? $setOrdersArray['customer_id'] : NULL;
                                $orders->branch_id = $branch_id;
                                $orders->terminal_id = $setOrdersArray['terminal_id'];
                                $orders->app_id = $setOrdersArray['app_id'];
                                //$orders->table_no = $setOrdersArray['table_no'];
                                $orders->table_id = $setOrdersArray['table_id'];
                                $orders->pax = $setOrdersArray['pax'];
                                $orders->invoice_no = $setOrdersArray['invoice_no'];
                                $orders->tax_percent = $setOrdersArray['tax_percent'];
                                $orders->tax_amount = $setOrdersArray['tax_amount'];
                                $orders->tax_json = \GuzzleHttp\json_encode($setOrdersArray['tax_json']);
                                $orders->service_charge_percent = $setOrdersArray['service_charge_percent'];
                                $orders->service_charge = $setOrdersArray['service_charge'];
                                $orders->voucher_id = $setOrdersArray['voucher_id'];
                                $orders->voucher_amount = $setOrdersArray['voucher_amount'];
                                $orders->voucher_detail = $setOrdersArray['voucher_detail'];
                                $orders->sub_total = $setOrdersArray['sub_total'];
                                $orders->sub_total_after_discount = $setOrdersArray['sub_total_after_discount'];
                                $orders->grand_total = $setOrdersArray['grand_total'];
                                $orders->rounding_amount = $setOrdersArray['rounding_amount'];
                                $orders->order_source = isset($setOrdersArray['order_source']) ? $setOrdersArray['order_source'] : "2";
                                $orders->order_status = $setOrdersArray['order_status'];
                                $orders->order_item_count = $setOrdersArray['order_item_count'];
                                $orders->order_date = $setOrdersArray['order_date'];
                                $orders->order_by = $setOrdersArray['order_by'];
                                $orders->updated_at = $setOrdersArray['updated_at'];
                                $orders->updated_by = ($setOrdersArray['updated_by'] != 0) ? $setOrdersArray['updated_by'] : NULL;

                                $orders = Order::create($orders->toArray());
                                $ordersId = $orders->order_id;  //get order id

                                $setOrdersArray['server_id'] = $ordersId;
                                $pushOrders[] = $ordersId;
                                //add order items
                                $getOrdersDetails = $setOrdersArray['order_detail'];
                                if (!empty($getOrdersDetails) && is_array($getOrdersDetails) && $ordersId != '') {
                                    foreach ($getOrdersDetails as $setOrdersItem) {
                                        $isExistingOrderItem = array_key_exists("server_id", $setOrdersItem);
                                        if ($isExistingOrderItem) {
                                            $serverId = $setOrdersItem['server_id'];
                                            $ordersitems = OrderDetail::where('order_id', $serverId)->first();
                                            if (empty($ordersitems)) {
                                                $ordersitems = new OrderDetail();
                                            }
                                        }

                                        $productId = $setOrdersItem['product_id'];
                                        $categoryId = $setOrdersItem['category_id'];
                                        $detail_qty = $setOrdersItem['detail_qty'];
                                        $ordersitems->uuid = Helper::getUuid();
                                        $ordersitems->order_id = $ordersId;
                                        $ordersitems->branch_id = $branch_id;
                                        $ordersitems->terminal_id = $terminal_id;
                                        $ordersitems->app_id = $setOrdersItem['app_id'];
                                        $ordersitems->order_app_id = $setOrdersItem['order_app_id'];
                                        $ordersitems->product_id = $productId;
                                        $ordersitems->category_id = $categoryId;
                                        $ordersitems->product_price = $setOrdersItem['product_price'];
                                        $ordersitems->product_old_price = $setOrdersItem['product_old_price'];
                                        $ordersitems->product_discount = $setOrdersItem['product_discount'];
                                        $ordersitems->product_detail = $setOrdersItem['product_detail'];
                                        $ordersitems->category_id = $setOrdersItem['category_id'];
                                        $ordersitems->detail_amount = ($setOrdersItem['detail_amount'] != null) ? $setOrdersItem['detail_amount'] : 0;
                                        $ordersitems->detail_qty = $setOrdersItem['detail_qty'];
                                        $ordersitems->detail_status = ($setOrdersItem['detail_status'] != null) ? $setOrdersItem['detail_status'] : 1;
                                        $ordersitems->detail_datetime = $setOrdersItem['detail_datetime'];
                                        $ordersitems->detail_by = $setOrdersItem['detail_by'];
                                        $ordersitems->updated_at = $setOrdersItem['updated_at'];
                                        $ordersitems->updated_by = ($setOrdersItem['updated_by'] != 0) ? $setOrdersItem['updated_by'] : NULL;

                                        $ordersitems = OrderDetail::create($ordersitems->toArray());

                                        $getProvariants = $setOrdersItem['order_modifier'];
                                        if (is_array($getProvariants) && !empty($getProvariants)) {
                                            foreach ($getProvariants as $setProvariants) {
                                                $isExistingOrderItem = array_key_exists("server_id", $setProvariants);
                                                if ($isExistingOrderItem) {
                                                    $serverId = $setProvariants['server_id'];
                                                    $ordersitemssub = OrderModifier::where('order_id', $serverId)->first();
                                                    if (empty($ordersitemssub)) {
                                                        $ordersitemssub = new OrderModifier();
                                                    }
                                                }

                                                $ordersitemssub->uuid = Helper::getUuid();
                                                $ordersitemssub->detail_id = $ordersitems->detail_id;
                                                $ordersitemssub->app_id = $setOrdersItem['app_id']; // application auto inc id
                                                $ordersitemssub->order_app_id = $setOrdersItem['order_app_id'];
                                                $ordersitemssub->detail_app_id = $setOrdersItem['detail_app_id'];
                                                $ordersitemssub->order_id = $ordersId;
                                                $ordersitemssub->terminal_id = $terminal_id;
                                                $ordersitemssub->product_id = $setOrdersItem['product_id'];
                                                $ordersitemssub->modifier_id = $setProvariants['modifier_id'];
                                                $ordersitemssub->om_amount = $setProvariants['om_amount'];
                                                $ordersitemssub->om_status = $setProvariants['om_status'];
                                                $ordersitemssub->om_datetime = $setProvariants['om_datetime'];
                                                $ordersitemssub->om_by = $setProvariants['om_by'];
                                                $ordersitemssub->updated_at = $setProvariants['updated_at'];
                                                $ordersitemssub->updated_by = ($setProvariants['updated_by'] != 0) ? $setProvariants['updated_by'] : NULL;

                                                $ordersitemssub = OrderModifier::create($ordersitemssub->toArray());
                                            }
                                        }

                                        $getProvariantsAtt = $setOrdersItem['order_attributes'];
                                        if (is_array($getProvariantsAtt) && !empty($getProvariantsAtt)) {
                                            foreach ($getProvariantsAtt as $setProvariantsAtt) {
                                                $isExistingOrderItem = array_key_exists("server_id", $setProvariantsAtt);
                                                if ($isExistingOrderItem) {
                                                    $serverId = $setProvariantsAtt['server_id'];
                                                    $ordersitemssubAt = OrderAttributes::where('order_id', $serverId)->first();
                                                    if (empty($ordersitemssubAt)) {
                                                        $ordersitemssubAt = new OrderAttributes();
                                                    }
                                                }

                                                $ordersitemssubAt->uuid = Helper::getUuid();
                                                $ordersitemssubAt->detail_id = $ordersitems->detail_id;
                                                $ordersitemssubAt->app_id = $setProvariantsAtt['app_id']; // application auto inc id
                                                $ordersitemssubAt->order_app_id = $setProvariantsAtt['order_app_id'];
                                                $ordersitemssubAt->detail_app_id = $setProvariantsAtt['detail_app_id'];
                                                $ordersitemssubAt->order_id = $ordersId;
                                                $ordersitemssubAt->terminal_id = $terminal_id;
                                                $ordersitemssubAt->product_id = $setOrdersItem['product_id'];
                                                $ordersitemssubAt->attribute_id = $setProvariantsAtt['attribute_id'];
                                                $ordersitemssubAt->attr_price = $setProvariantsAtt['attr_price'];
                                                $ordersitemssubAt->ca_id = $setProvariantsAtt['ca_id'];
                                                $ordersitemssubAt->oa_status = $setProvariantsAtt['oa_status'];
                                                $ordersitemssubAt->oa_datetime = $setProvariantsAtt['oa_datetime'];
                                                $ordersitemssubAt->oa_by = $setProvariantsAtt['oa_by'];
                                                $ordersitemssubAt->updated_at = $setProvariantsAtt['updated_at'];
                                                $ordersitemssubAt->updated_by = ($setProvariantsAtt['updated_by'] != 0) ? $setProvariantsAtt['updated_by'] : NULL;

                                                $ordersitemssubAt = OrderAttributes::create($ordersitemssubAt->toArray());
                                            }
                                        }

                                        /* Update Inventory */
                                        $productInventory = ProductStoreInventory::where(['product_id'=>$productId,'branch_id'=>$branchId])->first();
                                        if(!empty($productInventory)) {
                                            $main_qty = $productInventory->qty;
                                            $final_total_qty = ($main_qty - $detail_qty);
                                            $updateStock = [
                                                'qty' => $final_total_qty,
                                                'updated_at' => config('constants.date_time')
                                            ];
                                            ProductStoreInventory::where(['branch_id' => $branchId, 'product_id' => $productId])->update($updateStock);
                                        }
                                    }
                                }

                                // add payemnt
                                $getPaymentdetail = $setOrdersArray['order_payment'];
                                if (!empty($getPaymentdetail) && is_array($getPaymentdetail)) {
                                    foreach ($getPaymentdetail as $setPaymentdetail) {
                                        $orderspayments = new OrderPayment();
                                        $isExistingOrderItem = array_key_exists("server_id", $setPaymentdetail);
                                        if ($isExistingOrderItem) {
                                            $serverId = $setPaymentdetail['server_id'];
                                            $orderspayments = OrderPayment::where('op_id', $serverId)->first();
                                            if (empty($orderspayments)) {

                                                $orderspayments = new OrderPayment();

                                                $orderspayments->uuid = Helper::getUuid();
                                                $orderspayments->order_id = $ordersId;
                                                $orderspayments->branch_id = $branch_id;
                                                $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                                $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                $orderspayments->order_app_id = $setPaymentdetail['order_app_id']; // application auto inc id
                                                $orderspayments->is_split = $setPaymentdetail['is_split'];
                                                $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                                $orderspayments->remark = $setPaymentdetail['remark'];
                                                $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                                $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                                $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                                $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                                $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                                $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                                $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                                $orderspayments->op_status = $setPaymentdetail['op_status'];
                                                $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                                $orderspayments->op_by = $setPaymentdetail['op_by'];
                                                $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                                $orderspayments = OrderPayment::create($orderspayments->toArray());
                                                $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                                $setPaymentdetail['server_id'] = $orderspaymentsId;
                                            } else {
                                                $orderspayments = new OrderPayment();
                                                $orderspayments->order_id = $ordersId;
                                                $orderspayments->branch_id = $branch_id;
                                                $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                                $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                                $orderspayments->order_app_id = $setPaymentdetail['order_app_id']; // application auto inc id
                                                $orderspayments->is_split = $setPaymentdetail['is_split'];
                                                $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                                $orderspayments->remark = $setPaymentdetail['remark'];
                                                $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                                $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                                $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                                $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                                $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                                $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                                $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                                $orderspayments->op_status = $setPaymentdetail['op_status'];
                                                $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                                $orderspayments->op_by = $setPaymentdetail['op_by'];
                                                $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                                $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                                OrderPayment::where('op_id',$serverId)->update($orderspayments->toArray());
                                                $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                                $setPaymentdetail['server_id'] = $orderspaymentsId;
                                            }
                                        } else {
                                            $orderspayments = new OrderPayment();
                                            $orderspayments->uuid = Helper::getUuid();
                                            $orderspayments->order_id = $ordersId;
                                            $orderspayments->branch_id = $branch_id;
                                            $orderspayments->terminal_id = $setPaymentdetail['terminal_id'];
                                            $orderspayments->app_id = $setPaymentdetail['app_id']; // application auto inc id
                                            $orderspayments->order_app_id = $setPaymentdetail['order_app_id']; // application auto inc id
                                            $orderspayments->is_split = $setPaymentdetail['is_split'];
                                            $orderspayments->is_cash = $setPaymentdetail['is_cash'];
                                            $orderspayments->remark = $setPaymentdetail['remark'];
                                            $orderspayments->last_digits = $setPaymentdetail['last_digits'];
                                            $orderspayments->approval_code = $setPaymentdetail['approval_code'];
                                            $orderspayments->reference_number = $setPaymentdetail['reference_number'];
                                            $orderspayments->op_method_id = $setPaymentdetail['op_method_id'];
                                            $orderspayments->op_amount = $setPaymentdetail['op_amount'];
                                            $orderspayments->op_amount_change = $setPaymentdetail['op_amount_change'];
                                            $orderspayments->op_method_response = $setPaymentdetail['op_method_response'];
                                            $orderspayments->op_status = $setPaymentdetail['op_status'];
                                            $orderspayments->op_datetime = $setPaymentdetail['op_datetime'];
                                            $orderspayments->op_by = $setPaymentdetail['op_by'];
                                            $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                            $orderspayments->updated_at = $setPaymentdetail['updated_at'];
                                            $orderspayments->updated_by = ($setPaymentdetail['updated_by'] != 0) ? $setPaymentdetail['updated_by'] : NULL;
                                            $orderspayments = OrderPayment::create($orderspayments->toArray());
                                            $orderspaymentsId = $orderspayments->op_id;  //get order payment id
                                            $setPaymentdetail['server_id'] = $orderspaymentsId;
                                        }
                                    }
                                }

                                // Voucher History
                                $getVoucherdetail = $setOrdersArray['voucher_history'];
                                if(!empty($getVoucherdetail) && is_array($getVoucherdetail)){
                                    foreach ($getVoucherdetail as $setVoucherdetail) {
                                        $ordersvoucher = new VoucherHistory();
                                        $isExistingVoucherItem = array_key_exists("server_id", $setVoucherdetail);
                                        if ($isExistingVoucherItem) {
                                            $serverId = $setVoucherdetail['server_id'];
                                            $ordersvoucher = VoucherHistory::where('voucher_history_id', $serverId)->first();
                                            if (empty($ordersvoucher)) {

                                                $ordersvoucher = new VoucherHistory();

                                                $ordersvoucher->uuid = Helper::getUuid();
                                                $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                                $ordersvoucher->order_id = $ordersId;
                                                $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                                $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                                $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                                $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                                $ordersvoucher->amount = $setVoucherdetail['amount'];
                                                $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                                $ordersvoucher = VoucherHistory::create($ordersvoucher->toArray());
                                                $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                                $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                            } else {
                                                $ordersvoucher->order_id = $ordersId;
                                                $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                                $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                                $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                                $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                                $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                                $ordersvoucher->amount = $setVoucherdetail['amount'];
                                                $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                                VoucherHistory::where('voucher_history_id',$serverId)->update($ordersvoucher->toArray());
                                                $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                                $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                            }
                                        } else {
                                            $ordersvoucher->uuid = Helper::getUuid();
                                            $ordersvoucher->terminal_id = $setVoucherdetail['terminal_id'];
                                            $ordersvoucher->order_id = $ordersId;
                                            $ordersvoucher->app_id = $setVoucherdetail['app_id'];
                                            $ordersvoucher->app_order_id = $setVoucherdetail['app_order_id'];
                                            $ordersvoucher->voucher_id = $setVoucherdetail['voucher_id']; // application auto inc id
                                            $ordersvoucher->user_id = $setVoucherdetail['user_id'];
                                            $ordersvoucher->amount = $setVoucherdetail['amount'];
                                            $ordersvoucher->created_at = $setVoucherdetail['created_at'];
                                            $ordersvoucher = VoucherHistory::create($ordersvoucher->toArray());
                                            $ordersvoucherHisId = $ordersvoucher->voucher_history_id;  //get order payment id
                                            $setVoucherdetail['server_id'] = $ordersvoucherHisId;
                                        }
                                    }
                                }
                            }

                            //Remove Web Order
                            $isExistingWebOrder = array_key_exists("card_order_number", $setOrdersArray);
                            if($isExistingWebOrder){
                                $cartOrderNo = $setOrdersArray['card_order_number'];
                                $carts = Cart::where(['card_order_number' => $cartOrderNo,'source'=>1,'cart_payment_status'=>1])->first();
                                if(!empty($carts)){
                                    $cart_id = $carts->cart_id;
                                    CartDetail::where('cart_id', $cart_id)->delete();
                                    CartSubDetail::where('cart_id', $cart_id)->delete();
                                    Cart::where('cart_id', $cart_id)->delete();
                                }
                            }

                        }

                        DB::commit();
                        Helper::log('AppOrderData Table Synch : Bulk order Created');
                        $message = trans('api.bulk_order_created');
                        $loadOrderInfo = $this->ordersInfo($pushOrders);
                        $response['orders'] = $loadOrderInfo;
                        // total time taking api response
                        $timeEnd = microtime(true);
                        $response['timetaking'] = $timeEnd - $timeStart;
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);

                    } else {
                        DB::rollBack();
                        Helper::log('AppOrderData Table Synch : fail json to array converting');
                        Helper::saveTerminalLog($terminalId, $branchId, 'AppOrderData Sync', 'Create Order data SynchronizeAppdata faid json to array conversation', date('Y-m-d'), date('H:i:s'), 'order');
                        $message = trans('api.faid_json_to_array');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('AppOrderData Table Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Order data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'order');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }


        } catch (\Exception $exception) {
            Helper::log('AppOrderData Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method  : retrive ordeers info
     * @params  : $ordersIds(req)
     */

    public function ordersInfo($ordersIds = [])
    {
        Helper::log('AppOrderData info : start');
        try{

            if(empty($ordersIds)){
                Helper::log('AppOrderData Info : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            }
            $pushOrder = [];
            foreach ($ordersIds as $orderId) {
                $loadOrder = Order::where('order_id',$orderId)->select(['*', 'order_id as server_id', 'order_status as invoice_status'])->first();
                if (!empty($loadOrder)) {
                    $orderItem = OrderDetail::where(['order_id' => $orderId])->select(['*', 'detail_id as server_id'])->get()->toArray();
                    if (!empty($orderItem)) {
                        $orders_items = [];
                        $pushOrderSubItems = [];
                        $data = [];
                        foreach ($orderItem as $orderItemId) {
                            $ordersubItem = OrderModifier::where(['detail_id' => $orderItemId['detail_id']])->select(['*', 'om_id as server_id'])->get()->toArray();
                            $orderItemId['order_modifier'] = $ordersubItem;

                            $ordersubItemAtt = OrderAttributes::where(['detail_id' => $orderItemId['detail_id']])->select(['*', 'oa_id as server_id'])->get()->toArray();
                            $orderItemId['order_attributes'] = $ordersubItemAtt;

                            $data[] =  $orderItemId;
                        }
                        $loadOrder['order_detail'] = $data;
                    }
                }
                $loadPayment = OrderPayment::where(['order_id' => $orderId])->select(['*', 'op_id as server_id'])->get()->toArray();
                if (!empty($loadPayment)) {
                    $loadOrder['order_payment'] = $loadPayment;
                }
                $loadVoucher = VoucherHistory::where(['order_id' => $orderId])->select(['*', 'voucher_history_id as server_id'])->get()->toArray();
                if (!empty($loadVoucher)) {
                    $loadOrder['voucher_history'] = $loadVoucher;
                }
                $pushOrder[] = $loadOrder;
            }

            return $pushOrder;

        } catch (\Exception $exception) {
            Helper::log('AppOrderData info Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }

    }

    public function openShift(Request $request, $locale)
    {
        Helper::log('App Shift Data Open : Start');
        App::setLocale($locale);
        DB::beginTransaction();
        try{

            //return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter'), 'data' => $request]);
            $shift = $request->shift;
            if(empty($shift)){
                Helper::log('App Shift Data Open : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } else {
                $shiftArray = \GuzzleHttp\json_decode($shift, true);
                $pushShift = [];
                $shift = new Shift();

                Helper::log($shiftArray);
                $branch_id = Terminal::find($shiftArray['terminal_id'])->branch_id ?? 0;
                $user_id = User::where('uuid', $shiftArray['user_uuid'])->first()->id ?? 0;
                $shift->uuid            = Helper::getUuid();
                $shift->terminal_id     = ($shiftArray['terminal_id']) ?? "";
                $shift->app_id          = $shiftArray['app_id'] ?? NULL;
                $shift->user_id         = $user_id;
                $shift->branch_id       = $branch_id;
                $shift->status          = $shiftArray['status'] ?? 1;
                $shift->start_amount    = $shiftArray['start_amount'];
                $shift->end_amount      = $shiftArray['end_amount'] ?? 0 ;
                $shift->updated_by      = $shiftArray['updated_by'] ?? NULL;

                $shift = Shift::create($shift->toArray());
                $pushShift[] = $shift;
                DB::commit();
                $response['shift'] = $pushShift;
                $message = 'Bulk shift successfully created.';
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);

            }
        } catch (\Exception $exception) {
            Helper::log('App Shift Data Open Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method  : store shift info
     * @params  : shift (REQ)
     */

    public function createShift(Request $request, $locale)
    {
        Helper::log('AppShiftData Synch : Start');
        App::setLocale($locale);
        DB::beginTransaction();
        try{
            $shift = $request->shift;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;

            if(empty($shift)){
                Helper::log('AppShiftData Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } elseif (empty($terminalId)) {
                Helper::log('AppOrderData Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($branchId)) {
                Helper::log('AppOrderData Table Synch : Branch Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            } else {

                $timeStart = microtime(true);
                if (\GuzzleHttp\json_decode($shift, true)) {
                    $getShiftArray = \GuzzleHttp\json_decode($shift, true);
                    $pushShift = [];
                    if (is_array($getShiftArray)) {  // valid array
                        foreach ($getShiftArray as $setShiftArray) {
                            $isExistingShift = array_key_exists("server_id", $setShiftArray);
                            if ($isExistingShift) {
                                $serverId = $setShiftArray['server_id'];
                                $shift = Shift::where('shift_id',$serverId)->first();
                                if (empty($shift)) {
                                    $shift = new Shift();

                                    $shift->uuid = Helper::getUuid();
                                    $shift->terminal_id = ($setShiftArray['terminal_id']) ? $setShiftArray['terminal_id'] : "";
                                    $shift->app_id = $setShiftArray['app_id'];
                                    $shift->user_id = $setShiftArray['user_id'];
                                    $shift->branch_id = $setShiftArray['branch_id'];
                                    $shift->status = $setShiftArray['status'];
                                    $shift->start_amount = $setShiftArray['start_amount'];
                                    $shift->end_amount = $setShiftArray['end_amount'];
                                    $shift->updated_by = ($setShiftArray['updated_by'] != 0) ? $setShiftArray['updated_by'] : NULL;
                                    $shift->updated_at = ($setShiftArray['updated_at'] != '') ? $setShiftArray['updated_at'] : NULL;

                                    $shift = Shift::create($shift->toArray());
                                    $pushShift[] = $shift;
                                } else {

                                    $shift->terminal_id = ($setShiftArray['terminal_id']) ? $setShiftArray['terminal_id'] : "";
                                    $shift->app_id = $setShiftArray['app_id'];
                                    $shift->user_id = $setShiftArray['user_id'];
                                    $shift->branch_id = $setShiftArray['branch_id'];
                                    $shift->status = $setShiftArray['status'];
                                    $shift->start_amount = $setShiftArray['start_amount'];
                                    $shift->end_amount = $setShiftArray['end_amount'];
                                    $shift->updated_by = ($setShiftArray['updated_by'] != 0) ? $setShiftArray['updated_by'] : NULL;
                                    $shift->updated_at = ($setShiftArray['updated_at'] != '') ? $setShiftArray['updated_at'] : NULL;

                                    Shift::where('shift_id',$serverId)->update($shift->toArray());
                                    $pushShift[] = $shift;
                                }
                            } else {
                                $shift = new Shift();

                                $shift->uuid = Helper::getUuid();
                                $shift->terminal_id = ($setShiftArray['terminal_id']) ? $setShiftArray['terminal_id'] : "";
                                $shift->app_id = $setShiftArray['app_id'];
                                $shift->user_id = $setShiftArray['user_id'];
                                $shift->branch_id = $setShiftArray['branch_id'];
                                $shift->status = $setShiftArray['status'];
                                $shift->start_amount = $setShiftArray['start_amount'];
                                $shift->end_amount = $setShiftArray['end_amount'];
                                $shift->updated_by = ($setShiftArray['updated_by'] != 0) ? $setShiftArray['updated_by'] : NULL;
                                $shift->updated_at = ($setShiftArray['updated_at'] != '') ? $setShiftArray['updated_at'] : NULL;

                                $shift = Shift::create($shift->toArray());
                                $pushShift[] = $shift;
                            }
                        }
                        DB::commit();
                        $response['shift'] = $pushShift;
                        $message = 'Bulk shift successfully created.';
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
                    } else {
                        DB::rollBack();
                        Helper::log('AppShiftData Table Synch : Invalid Json String');
                        $message = trans('api.invalid_json_string');
                        Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Order data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'shift');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('AppShiftData Table Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Order data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'shift');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }
        } catch (\Exception $exception) {
            Helper::log('AppShiftData Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method  : store shift info
     * @params  : shift (REQ)
     */

    public function createShiftDetails(Request $request, $locale)
    {
        Helper::log('AppShiftInvoiceData Synch : Start');
        App::setLocale($locale);
        DB::beginTransaction();
        try{
            $shiftInvoice = $request->shift_detail;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;

            if(empty($shiftInvoice)){
                Helper::log('AppShiftInvoiceData Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } elseif (empty($terminalId)) {
                Helper::log('AppShiftInvoiceData Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($branchId)) {
                Helper::log('AppShiftInvoiceData Table Synch : Branch Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            } else {

                $timeStart = microtime(true);
                if (\GuzzleHttp\json_decode(stripslashes($shiftInvoice), true)) {
                    $getShiftArray = \GuzzleHttp\json_decode(stripslashes($shiftInvoice), true);
                    $pushShiftInvoice = [];
                    if (is_array($getShiftArray)) {  // valid array
                        foreach ($getShiftArray as $setShiftArray) {
                            $isExistingShift = array_key_exists("server_id", $setShiftArray);
                            if ($isExistingShift) {
                                $serverId = $setShiftArray['server_id'];
                                $shiftInvoice = ShiftDetails::where('shift_details_id',$serverId)->first();
                                if (empty($shiftInvoice)) {
                                    $shiftInvoice = new ShiftDetails();

                                    $shiftInvoice->uuid = Helper::getUuid();
                                    $shiftInvoice->app_id = $setShiftArray['app_id'];
                                    $shiftInvoice->shift_id = $setShiftArray['shift_app_id'];
                                    $shiftInvoice->status = $setShiftArray['status'];
                                    $shiftInvoice->invoice_id = $setShiftArray['invoice_id'];
                                    $shiftInvoice->updated_by = ($setShiftArray['updated_by'] != 0) ? $setShiftArray['updated_by'] : NULL;
                                    $shiftInvoice->updated_at = ($setShiftArray['updated_at'] != '') ? $setShiftArray['updated_at'] : NULL;
                                    $shiftInvoice->terminal_id = ($setShiftArray['terminal_id']) ? $setShiftArray['terminal_id'] : "";

                                    $shiftInvoice = ShiftDetails::create($shiftInvoice->toArray());
                                    $pushShiftInvoice[] = $shiftInvoice;
                                } else {

                                    $shiftInvoice->app_id = $setShiftArray['app_id'];
                                    $shiftInvoice->shift_id = $setShiftArray['shift_app_id'];
                                    $shiftInvoice->status = $setShiftArray['status'];
                                    $shiftInvoice->invoice_id = $setShiftArray['invoice_id'];
                                    $shiftInvoice->updated_by = ($setShiftArray['updated_by'] != 0) ? $setShiftArray['updated_by'] : NULL;
                                    $shiftInvoice->updated_at = ($setShiftArray['updated_at'] != '') ? $setShiftArray['updated_at'] : NULL;
                                    $shiftInvoice->terminal_id = ($setShiftArray['terminal_id']) ? $setShiftArray['terminal_id'] : "";

                                    ShiftDetails::where('shift_details_id', $serverId)->update($shiftInvoice->toArray());
                                    $pushShiftInvoice[] = $shiftInvoice;
                                }
                            } else {
                                $shiftInvoice = new ShiftDetails();

                                $shiftInvoice->uuid = Helper::getUuid();
                                $shiftInvoice->app_id = $setShiftArray['app_id'];
                                $shiftInvoice->shift_id = $setShiftArray['shift_app_id'];
                                $shiftInvoice->status = $setShiftArray['status'];
                                $shiftInvoice->invoice_id = $setShiftArray['invoice_id'];
                                $shiftInvoice->updated_by = ($setShiftArray['updated_by'] != 0) ? $setShiftArray['updated_by'] : NULL;
                                $shiftInvoice->updated_at = ($setShiftArray['updated_at'] != '') ? $setShiftArray['updated_at'] : NULL;
                                $shiftInvoice->terminal_id = ($setShiftArray['terminal_id']) ? $setShiftArray['terminal_id'] : "";

                                $shiftInvoice = ShiftDetails::create($shiftInvoice->toArray());
                                $pushShiftInvoice[] = $shiftInvoice;

                            }

                        }
                        DB::commit();
                        $response['shift_detail'] = $pushShiftInvoice;
                        $message = 'Bulk shift detail successfully created.';
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
                    } else {
                        DB::rollBack();
                        Helper::log('AppShiftInvoiceData Table Synch : Invalid Json String');
                        $message = trans('api.invalid_json_string');
                        Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Order data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'shift_detail');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('AppShiftInvoiceData Table Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Order data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'shift_detail');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }
        } catch (\Exception $exception) {
            Helper::log('AppShiftInvoiceData Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method  : Sync Terminal Log
     * @params  : terminal_log(REQ)
     */

    public function createTerminalLog(Request $request, $locale)
    {
        Helper::log('AppShiftData Synch : Start');
        App::setLocale($locale);
        DB::beginTransaction();
        try{
            $terminal_log = $request->terminal_log;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;

            if(empty($terminal_log)){
                Helper::log('AppShiftData Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } elseif (empty($terminalId)) {
                Helper::log('AppOrderData Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($branchId)) {
                Helper::log('AppOrderData Table Synch : Branch Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            } else {

                $timeStart = microtime(true);
                if (\GuzzleHttp\json_decode($terminal_log, true)) {
                    $getTerminalLogArray = \GuzzleHttp\json_decode($terminal_log, true);
                    $pushTerminal = [];
                    if (is_array($getTerminalLogArray)) {  // valid array
                        $terminalLog = new TerminalLog();
                        foreach ($getTerminalLogArray as $setTerminalLogArray) {
                            $isExistingShift = array_key_exists("server_id", $setTerminalLogArray);
                            if ($isExistingShift) {
                                $serverId = $setTerminalLogArray['server_id'];
                                $terminalLog = TerminalLog::where('id',$serverId)->first();
                                if (empty($terminalLog)) {
                                    $terminalLog = new TerminalLog();

                                    $terminalLog->uuid = Helper::getUuid();
                                    $terminalLog->terminal_id = ($setTerminalLogArray['terminal_id']) ? $setTerminalLogArray['terminal_id'] : "";
                                    $terminalLog->branch_id = $setTerminalLogArray['branch_id'];
                                    $terminalLog->module_name = $setTerminalLogArray['module_name'];
                                    $terminalLog->description = $setTerminalLogArray['description'];
                                    $terminalLog->activity_date = $setTerminalLogArray['activity_date'];
                                    $terminalLog->activity_time = $setTerminalLogArray['activity_time'];
                                    $terminalLog->table_name = $setTerminalLogArray['table_name'];
                                    $terminalLog->entity_id = ($setTerminalLogArray['entity_id'] != 0) ? $setTerminalLogArray['entity_id'] : NULL;
                                    $terminalLog->status = $setTerminalLogArray['status'];
                                    $terminalLog->updated_at = ($setTerminalLogArray['updated_at'] != '') ? $setTerminalLogArray['updated_at'] : NULL;
                                    $terminalLog->updated_by = ($setTerminalLogArray['updated_by'] != 0) ? $setTerminalLogArray['updated_by'] : NULL;

                                    $terminalLog = TerminalLog::create($terminalLog->toArray());
                                    $pushTerminal[] = $terminalLog;
                                } else {

                                    $terminalLog->terminal_id = ($setTerminalLogArray['terminal_id']) ? $setTerminalLogArray['terminal_id'] : "";
                                    $terminalLog->branch_id = $setTerminalLogArray['branch_id'];
                                    $terminalLog->module_name = $setTerminalLogArray['module_name'];
                                    $terminalLog->description = $setTerminalLogArray['description'];
                                    $terminalLog->activity_date = $setTerminalLogArray['activity_date'];
                                    $terminalLog->activity_time = $setTerminalLogArray['activity_time'];
                                    $terminalLog->table_name = $setTerminalLogArray['table_name'];
                                    $terminalLog->entity_id = ($setTerminalLogArray['entity_id'] != 0) ? $setTerminalLogArray['entity_id'] : NULL;
                                    $terminalLog->status = $setTerminalLogArray['status'];
                                    $terminalLog->updated_at = ($setTerminalLogArray['updated_at'] != '') ? $setTerminalLogArray['updated_at'] : NULL;
                                    $terminalLog->updated_by = ($setTerminalLogArray['updated_by'] != 0) ? $setTerminalLogArray['updated_by'] : NULL;

                                    TerminalLog::where('id',$serverId)->update($terminalLog->toArray());
                                    $pushTerminal[] = $terminalLog;
                                }
                            } else {
                                $terminalLog = new TerminalLog();

                                $terminalLog->uuid = Helper::getUuid();
                                $terminalLog->terminal_id = ($setTerminalLogArray['terminal_id']) ? $setTerminalLogArray['terminal_id'] : "";
                                $terminalLog->branch_id = $setTerminalLogArray['branch_id'];
                                $terminalLog->module_name = $setTerminalLogArray['module_name'];
                                $terminalLog->description = $setTerminalLogArray['description'];
                                $terminalLog->activity_date = $setTerminalLogArray['activity_date'];
                                $terminalLog->activity_time = $setTerminalLogArray['activity_time'];
                                $terminalLog->table_name = $setTerminalLogArray['table_name'];
                                $terminalLog->entity_id = ($setTerminalLogArray['entity_id'] != 0) ? $setTerminalLogArray['entity_id'] : NULL;
                                $terminalLog->status = $setTerminalLogArray['status'];
                                $terminalLog->updated_at = ($setTerminalLogArray['updated_at'] != '') ? $setTerminalLogArray['updated_at'] : NULL;
                                $terminalLog->updated_by = ($setTerminalLogArray['updated_by'] != 0) ? $setTerminalLogArray['updated_by'] : NULL;


                                $terminalLog = TerminalLog::create($terminalLog->toArray());
                                $pushTerminal[] = $terminalLog;
                            }
                        }
                        DB::commit();
                        $response['terminal_log'] = $pushTerminal;
                        $message = 'Terminal Log successfully created.';
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
                    } else {
                        DB::rollBack();
                        Helper::log('AppTerminalLogData Table Synch : Invalid Json String');
                        $message = trans('api.invalid_json_string');
                        Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Terminal Log data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'terminal_log');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('AppTerminalLogData Table Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Terminal Log data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'terminal_log');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }
        } catch (\Exception $exception) {
            Helper::log('AppTerminalLogData Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Creatbulkorderscancel
     * @params   : JSON
     * @respose  : as like request with server id
     */

    public function creatbulkorderscancel(Request $request, $locale)
    {
        Helper::log('Bulk Cancel Order AppOrderData Synch : Start');
        App::setLocale($locale);
        DB::beginTransaction();
        try {
            $getOrderCancel = $request->order_cancel;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;
            if (empty($getOrderCancel)) {
                Helper::log('AppOrderData Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } elseif (empty($terminalId)) {
                Helper::log('AppOrderData Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($branchId)) {
                Helper::log('AppOrderData Table Synch : Branch Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            } else {
                $timeStart = microtime(true);
                if (\GuzzleHttp\json_decode(stripslashes($getOrderCancel), true)) {
                    $getOrderCancelArray = \GuzzleHttp\json_decode(stripcslashes($getOrderCancel), true);  // convert to array
                    if (is_array($getOrderCancelArray)) {  // valid array

                        $orders = new OrderCancel();
                        $pushOrder = [];
                        foreach ($getOrderCancelArray as $setOrdersArray) {
                            $isExistingOrder = array_key_exists("server_id", $setOrdersArray);
                            if ($isExistingOrder) {
                                $serverId = $setOrdersArray['server_id'];
                                $ordersCancel = OrderCancel::where('id',$serverId)->first();
                                if (empty($ordersCancel)) {
                                    $orders = new OrderCancel();

                                    $TerminalId = $setOrdersArray['terminal_id'];
                                    $createdBy = $setOrdersArray['created_by'];
                                    $app_order_id = $setOrdersArray['order_app_id'];
                                    if(empty($setOrdersArray['order_id'])){
                                        $orderData = Order::where(['terminal_id'=>$TerminalId, 'app_id'=>$app_order_id])->first();
                                        $orderId = $orderData->order_id;
                                    } else {
                                        $orderId = $setOrdersArray['order_id'];
                                        $orderData = Order::where(['order_id'=>$orderId])->first();
                                    }
                                    $orders->order_id = $orderId;
                                    $orders->localID = $setOrdersArray['localID'];
                                    $orders->order_app_id = $setOrdersArray['order_app_id'];
                                    $orders->reason = $setOrdersArray['reason'];
                                    $orders->status = $setOrdersArray['status'];
                                    $orders->terminal_id = $TerminalId;
                                    $orders->created_at = $setOrdersArray['created_at'];
                                    $orders->created_by = $createdBy;
                                    $orders->updated_by = ($setOrdersArray['updated_by'] != 0) ? $setOrdersArray['updated_by'] : NULL;
                                    $orders->updated_at = ($setOrdersArray['updated_at']) ? $setOrdersArray['updated_at'] : $setOrdersArray['created_at'];
                                    $orders = OrderCancel::create($orders->toArray());
                                    $setOrdersArray['server_id'] = $orders->id;
                                    $pushShift[] = $orders;

                                    /*Update Order Status*/
                                    $orderData->order_status = 3;
                                    Order::where('order_id',$orderId)->update($orderData->toArray());


                                } else {
                                    $TerminalId = $setOrdersArray['terminal_id'];
                                    $createdBy = $setOrdersArray['created_by'];
                                    $orders->order_id = $setOrdersArray['order_id'];
                                    $orders->reason = $setOrdersArray['reason'];
                                    $orders->status = $setOrdersArray['status'];
                                    $orders->localID = $setOrdersArray['localID'];
                                    $orders->terminal_id = $TerminalId;
                                    $orders->created_at = $setOrdersArray['created_at'];
                                    $orders->created_by = $createdBy;
                                    $orders->updated_by = ($setOrdersArray['updated_by'] != 0) ? $setOrdersArray['updated_by'] : NULL;
                                    $orders->updated_at = ($setOrdersArray['updated_at']) ? $setOrdersArray['updated_at'] : $setOrdersArray['created_at'];
                                    OrderCancel::where('id',$serverId)->update($orders->toArray());
                                    $setOrdersArray['server_id'] = $orders->id;
                                    $pushShift[] = $orders;
                                }

                            } else {
                                $orders = new OrderCancel();
                                $TerminalId = $setOrdersArray['terminal_id'];
                                $createdBy = $setOrdersArray['created_by'];
                                $app_order_id = $setOrdersArray['order_app_id'];
                                if(empty($setOrdersArray['order_id'])){
                                    $orderData = Order::where(['terminal_id'=>$TerminalId, 'app_id'=>$app_order_id])->first();
                                    $orderId = $orderData->order_id;
                                } else {
                                    $orderId = $setOrdersArray['order_id'];
                                    $orderData = Order::where(['order_id'=>$orderId])->first();
                                }
                                $orders->order_id = $orderId;
                                $orders->localID = $setOrdersArray['localID'];
                                $orders->order_app_id = $setOrdersArray['order_app_id'];
                                $orders->reason = $setOrdersArray['reason'];
                                $orders->status = $setOrdersArray['status'];
                                $orders->terminal_id = $TerminalId;
                                $orders->created_at = $setOrdersArray['created_at'];
                                $orders->created_by = $createdBy;
                                $orders->updated_by = ($setOrdersArray['updated_by'] != 0) ? $setOrdersArray['updated_by'] : NULL;
                                $orders->updated_at = ($setOrdersArray['updated_at']) ? $setOrdersArray['updated_at'] : $setOrdersArray['created_at'];
                                $orders = OrderCancel::create($orders->toArray());
                                $setOrdersArray['server_id'] = $orders->id;
                                $pushShift[] = $orders;

                                /*Update Order Status*/
                                $orderData->order_status = 3;
                                Order::where('order_id',$orderId)->update($orderData->toArray());
                            }
                        }
                        DB::commit();
                        $response['order_cancel'] = $pushShift;
                        // total time taking api response
                        $timeEnd = microtime(true);
                        $response['timetaking'] = $timeEnd - $timeStart;
                        $message = 'BulkCancelOrder successfully created.';
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);

                    } else {
                        DB::rollBack();
                        Helper::log('Bulk Cancel Order AppOrderData Table Synch : fail json to array converting');
                        Helper::saveTerminalLog($terminalId, $branchId, 'Cancel Order AppOrderData Sync', 'Cancel Order data SynchronizeAppdata faid json to array conversation', date('Y-m-d'), date('H:i:s'), 'cancel_order');
                        $message = trans('api.faid_json_to_array');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('Bulk Cancel Order AppOrderData Table Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Cancel Order Sync', 'Cancel Order data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'cancel_order');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }

        } catch (\Exception $exception) {
            Helper::log('Bulk Cancel Order AppOrderData Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method : update inventory
     * @parmas : terminalId, InvoiceUniqId
     * @return : true false
     */

    public function updateStockInventory(Request $request)
    {
        Helper::log('Update Stock Inventory AppData Synch : Start');
        try{
            $getStoreInventory = $request->store_inventory;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;
            if (empty($getStoreInventory)) {
                Helper::log('AppOrderData Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } elseif (empty($terminalId)) {
                Helper::log('AppOrderData Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($branchId)) {
                Helper::log('AppOrderData Table Synch : Branch Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            } else {
                $timeStart = microtime(true);
                if (\GuzzleHttp\json_decode(stripslashes($getStoreInventory), true)) {
                    $getProductStoreInventoryArray = \GuzzleHttp\json_decode(stripslashes($getStoreInventory), true);  // convert to array
                    if (is_array($getProductStoreInventoryArray)) {  // valid array

                        $productStoreInventory = new ProductStoreInventory();
                        $productStoreInventoryLog = new ProductStoreInventoryLog();
                        $pushInventory = [];
                        foreach ($getProductStoreInventoryArray as $setProductStoreInventoryArray) {
                            $isExistingOrder = array_key_exists("server_id", $setProductStoreInventoryArray);
                            if ($isExistingOrder) {
                                $serverId = $setProductStoreInventoryArray['server_id'];
                                $productInventory = ProductStoreInventory::where('inventory_id',$serverId)->first();
                                if (empty($productInventory)) {
                                    $productStoreInventory = new ProductStoreInventory();
                                } else {

                                    $total_product_qty = $setProductStoreInventoryArray['qty'];//$productInventory->qty;
                                    $total_deduct_qty = 0;$final_total_qty = 0;
                                    $total_add_qty = 0;
                                    $inventoryId = $setProductStoreInventoryArray['inventory_id'];
                                    $productId = $setProductStoreInventoryArray['product_id'];
                                    $branchId = $setProductStoreInventoryArray['branch_id'];

                                    $getinventoryLog = $setProductStoreInventoryArray['product_store_inventory_log'];
                                    if (!empty($getinventoryLog) && is_array($getinventoryLog)) {
                                        foreach ($getinventoryLog as $setinventoryLog) {
											$isExistingLog = array_key_exists("server_id", $setinventoryLog);
                                            if ($isExistingLog) {
                                                $logserverId = $setinventoryLog['server_id'];
                                                $productInventory = ProductStoreInventoryLog::where('il_id',$logserverId)->first();
                                                if (empty($productInventory)) {
													$productStoreInventoryLog = new ProductStoreInventoryLog();
													$productStoreInventoryLog->uuid = Helper::getUuid();
													$productStoreInventoryLog->inventory_id = $setinventoryLog['inventory_id'];
													$productStoreInventoryLog->branch_id = $setinventoryLog['branch_id'];
													$productStoreInventoryLog->product_id = $setinventoryLog['product_id'];
													$productStoreInventoryLog->employe_id = $setinventoryLog['employe_id'];
													$productStoreInventoryLog->il_type = $setinventoryLog['il_type'];
													$productStoreInventoryLog->qty = $setinventoryLog['qty'];
													$productStoreInventoryLog->qty_before_change = $setinventoryLog['qty_before_change'];
													$productStoreInventoryLog->qty_after_change = $setinventoryLog['qty_after_change'];
													$productStoreInventoryLog->updated_by = ($setinventoryLog['updated_by'] != 0) ? $setinventoryLog['updated_by'] : NULL;
													$productStoreInventoryLog->updated_at = ($setinventoryLog['updated_at']) ? $setinventoryLog['updated_at'] : $setinventoryLog['created_at'];
													$productStoreInventoryLog = ProductStoreInventoryLog::create($productStoreInventoryLog->toArray());
													$ilId = $productStoreInventoryLog->il_id;
													$setinventoryLog['server_id'] = $ilId;

                                                }
											} else {	
												$productStoreInventoryLog->uuid = Helper::getUuid();
												$productStoreInventoryLog->inventory_id = $setinventoryLog['inventory_id'];
												$productStoreInventoryLog->branch_id = $setinventoryLog['branch_id'];
												$productStoreInventoryLog->product_id = $setinventoryLog['product_id'];
												$productStoreInventoryLog->employe_id = $setinventoryLog['employe_id'];
												$productStoreInventoryLog->il_type = $setinventoryLog['il_type'];
												$productStoreInventoryLog->qty = $setinventoryLog['qty'];
												$productStoreInventoryLog->qty_before_change = $setinventoryLog['qty_before_change'];
												$productStoreInventoryLog->qty_after_change = $setinventoryLog['qty_after_change'];
												$productStoreInventoryLog->updated_by = ($setinventoryLog['updated_by'] != 0) ? $setinventoryLog['updated_by'] : NULL;
												$productStoreInventoryLog->updated_at = ($setinventoryLog['updated_at']) ? $setinventoryLog['updated_at'] : $setinventoryLog['created_at'];
												$productStoreInventoryLog = ProductStoreInventoryLog::create($productStoreInventoryLog->toArray());
												$ilId = $productStoreInventoryLog->il_id;
												$setinventoryLog['server_id'] = $ilId;

											}
                                        }
                                        
                                    }

                                    /* Update Main Inventory Stock */
                                    $productInventory = ProductStoreInventory::where('inventory_id',$inventoryId)->first();
                                    $main_qty = $productInventory->qty;
									$final_total_qty = ($total_product_qty + $main_qty);
									$updateStock = [
										'qty' => $final_total_qty,
										'updated_at' => $setProductStoreInventoryArray['updated_at'],
										'updated_by' => ($setProductStoreInventoryArray['updated_by'] != 0) ? $setProductStoreInventoryArray['updated_by'] : NULL
									];
									//ProductStoreInventory::where(['inventory_id'=>$inventoryId,'branch_id'=>$branchId,'product_id'=>$productId])->update($updateStock);
									$pushInventory[] = $inventoryId;

                                }

                            } else {

                                $inventoryId = $setProductStoreInventoryArray['inventory_id'];
                                $productId = $setProductStoreInventoryArray['product_id'];
                                $branchId = $setProductStoreInventoryArray['branch_id'];
                                $total_product_qty = $setProductStoreInventoryArray['qty'];
                                $total_deduct_qty = 0;$final_total_qty = 0;
                                $total_add_qty = 0;

                                $getinventoryLog = $setProductStoreInventoryArray['product_store_inventory_log'];
                                if (!empty($getinventoryLog) && is_array($getinventoryLog)) {
                                    foreach ($getinventoryLog as $setinventoryLog) {
										$isExistingLog = array_key_exists("server_id", $setinventoryLog);
                                            if ($isExistingLog) {
                                                $logserverId = $setinventoryLog['server_id'];
                                                $productInventory = ProductStoreInventoryLog::where('il_id',$logserverId)->first();
                                                if (empty($productInventory)) {
													$productStoreInventoryLog->uuid = Helper::getUuid();
													$productStoreInventoryLog->inventory_id = $setinventoryLog['inventory_id'];
													$productStoreInventoryLog->branch_id = $setinventoryLog['branch_id'];
													$productStoreInventoryLog->product_id = $setinventoryLog['product_id'];
													$productStoreInventoryLog->employe_id = $setinventoryLog['employe_id'];
													$productStoreInventoryLog->il_type = $setinventoryLog['il_type'];
													$productStoreInventoryLog->qty = $setinventoryLog['qty'];
													$productStoreInventoryLog->qty_before_change = $setinventoryLog['qty_before_change'];
													$productStoreInventoryLog->qty_after_change = $setinventoryLog['qty_after_change'];
													$productStoreInventoryLog->updated_by = ($setinventoryLog['updated_by'] != 0) ? $setinventoryLog['updated_by'] : NULL;
													$productStoreInventoryLog->updated_at = ($setinventoryLog['updated_at']) ? $setinventoryLog['updated_at'] : $setinventoryLog['created_at'];
													$productStoreInventoryLog = ProductStoreInventoryLog::create($productStoreInventoryLog->toArray());
													$ilId = $productStoreInventoryLog->il_id;
													$setinventoryLog['server_id'] = $ilId;

												}
											} else {
												$productStoreInventoryLog->uuid = Helper::getUuid();
												$productStoreInventoryLog->inventory_id = $setinventoryLog['inventory_id'];
												$productStoreInventoryLog->branch_id = $setinventoryLog['branch_id'];
												$productStoreInventoryLog->product_id = $setinventoryLog['product_id'];
												$productStoreInventoryLog->employe_id = $setinventoryLog['employe_id'];
												$productStoreInventoryLog->il_type = $setinventoryLog['il_type'];
												$productStoreInventoryLog->qty = $setinventoryLog['qty'];
												$productStoreInventoryLog->qty_before_change = $setinventoryLog['qty_before_change'];
												$productStoreInventoryLog->qty_after_change = $setinventoryLog['qty_after_change'];
												$productStoreInventoryLog->updated_by = ($setinventoryLog['updated_by'] != 0) ? $setinventoryLog['updated_by'] : NULL;
												$productStoreInventoryLog->updated_at = ($setinventoryLog['updated_at']) ? $setinventoryLog['updated_at'] : $setinventoryLog['created_at'];
												$productStoreInventoryLog = ProductStoreInventoryLog::create($productStoreInventoryLog->toArray());
												$ilId = $productStoreInventoryLog->il_id;
												$setinventoryLog['server_id'] = $ilId;

											}
                                    }

                                }

                                /* Update Main Inventory Stock */
                                $productInventory = ProductStoreInventory::where('inventory_id',$inventoryId)->first();
                                $main_qty = $productInventory->qty;
                                $final_total_qty = ($total_product_qty + $main_qty);
								$updateStock = [
									'qty' => $final_total_qty,
									'updated_at' => $setProductStoreInventoryArray['updated_at'],
									'updated_by' => ($setProductStoreInventoryArray['updated_by'] != 0) ? $setProductStoreInventoryArray['updated_by'] : NULL
								];
								//ProductStoreInventory::where(['inventory_id'=>$inventoryId,'branch_id'=>$branchId,'product_id'=>$productId])->update($updateStock);
								$pushInventory[] = $inventoryId;
                            }
                        }
                        DB::commit();
                        Helper::log('Update Stock Inventory AppData Synch : Inventory Created');
                        $message = trans('api.inventory_created');
                        $loadInventoryInfo = $this->inventoryInfo($pushInventory);
                        $response['product_store_inventory'] = $loadInventoryInfo;
                        // total time taking api response
                        $timeEnd = microtime(true);
                        $response['timetaking'] = $timeEnd - $timeStart;
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);

                    } else {
                        DB::rollBack();
                        Helper::log('Update Stock Inventory AppData Synch : fail json to array converting');
                        Helper::saveTerminalLog($terminalId, $branchId, 'Store Inventory Order AppOrderData Sync', 'Update product Store inventory Order data SynchronizeAppdata faid json to array conversation', date('Y-m-d'), date('H:i:s'), 'product_store_inventory');
                        $message = trans('api.faid_json_to_array');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('Update Stock Inventory AppData Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Store Inventory Order AppOrderData Sync', 'Update product Store inventory Order data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'product_store_inventory');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }

        } catch (\Exception $exception) {
            Helper::log('Update Inventory OrderData Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method  : retrive inventory info
     * @params  : $InventoryIds(req)
     */

    public function inventoryInfo($pushInventoryId = [])
    {
        Helper::log('AppInventoryData info : start');
        try{

            if(empty($pushInventoryId)){
                Helper::log('AppInventoryData Info : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            }
            $pushInventory = [];
            foreach ($pushInventoryId as $inventoryId) {
                $loadInventory = ProductStoreInventory::where('inventory_id',$inventoryId)->select(['*', 'inventory_id as server_id'])->first();
                if (!empty($loadInventory)) {
                    $inventoryLog = ProductStoreInventoryLog::where(['inventory_id' => $inventoryId])->select(['*'])->get()->toArray();
                    if (!empty($inventoryLog)) {

                        $loadInventory['product_store_inventory_log'] = $inventoryLog;
                    } else {
                        $loadInventory['product_store_inventory_log'] = [];
                    }
                }
                $pushInventory[] = $loadInventory;
            }

            return $pushInventory;

        } catch (\Exception $exception) {
            Helper::log('AppInventoryData info Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }
	
	/*
     * @method : update inventory
     * @parmas : terminalId, InvoiceUniqId
     * @return : true false
     */

    public function updateCustomerLiquorInventory(Request $request)
    {
        Helper::log('Update Customer Liquor Inventory AppData Synch : Start');
        try{
            $getStoreInventory = $request->customer_inventory;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;
            if (empty($getStoreInventory)) {
                Helper::log('AppOrderData Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } elseif (empty($terminalId)) {
                Helper::log('AppOrderData Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($branchId)) {
                Helper::log('AppOrderData Table Synch : Branch Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            } else {
                $timeStart = microtime(true);
                if (\GuzzleHttp\json_decode(stripslashes($getStoreInventory), true)) {
                    $getProductStoreInventoryArray = \GuzzleHttp\json_decode(stripslashes($getStoreInventory), true);  // convert to array
                    if (is_array($getProductStoreInventoryArray)) {  // valid array

                        $productStoreInventory = new CustomerLiquorInventory();
                        $productStoreInventoryLog = new CustomerLiquorInventoryLog();
                        $pushInventory = [];
                        foreach ($getProductStoreInventoryArray as $setProductStoreInventoryArray) {
                            $isExistingOrder = array_key_exists("server_id", $setProductStoreInventoryArray);
                            if ($isExistingOrder) {
                                $serverId = $setProductStoreInventoryArray['server_id'];
                                $productInventory = CustomerLiquorInventory::where('cl_id',$serverId)->first();
                                if (empty($productInventory)) {
                                    $productStoreInventory = new CustomerLiquorInventory();
                                } else {

                                    $total_product_qty = $productInventory->qty;
                                    $total_deduct_qty = 0;$final_total_qty = 0;
                                    $total_add_qty = 0;
                                    $inventoryId = $setProductStoreInventoryArray['cl_id'];
                                    $productId = $setProductStoreInventoryArray['product_id'];
                                    $branchId = $setProductStoreInventoryArray['branch_id'];

                                    $getinventoryLog = $setProductStoreInventoryArray['customer_liquor_inventory_log'];
                                    if (!empty($getinventoryLog) && is_array($getinventoryLog)) {
                                        foreach ($getinventoryLog as $setinventoryLog) {
                                            $productStoreInventoryLog->uuid = Helper::getUuid();
                                            $productStoreInventoryLog->cl_id = $setinventoryLog['cl_id'];
                                            $productStoreInventoryLog->cl_appId = $setinventoryLog['cl_appId'];
                                            $productStoreInventoryLog->branch_id = $setinventoryLog['branch_id'];
                                            $productStoreInventoryLog->product_id = $setinventoryLog['product_id'];
                                            $productStoreInventoryLog->customer_id = $setinventoryLog['employe_id'];
                                            $productStoreInventoryLog->il_type = $setinventoryLog['il_type'];
                                            $productStoreInventoryLog->qty = $setinventoryLog['qty'];
                                            $productStoreInventoryLog->qty_before_change = $setinventoryLog['qty_before_change'];
                                            $productStoreInventoryLog->qty_after_change = $setinventoryLog['qty_after_change'];
                                            $productStoreInventoryLog->updated_by = ($setinventoryLog['updated_by'] != 0) ? $setinventoryLog['updated_by'] : NULL;
                                            $productStoreInventoryLog->updated_at = ($setinventoryLog['updated_at']) ? $setinventoryLog['updated_at'] : $setinventoryLog['created_at'];
                                            $productStoreInventoryLog = CustomerLiquorInventoryLog::create($productStoreInventoryLog->toArray());

                                            /* Calculate Stock Inventory */
                                            if($setinventoryLog['il_type'] == 2){
                                                $total_deduct_qty += $setinventoryLog['qty_before_change'] - $setinventoryLog['qty_after_change'];
                                            } else {
                                                $total_add_qty += $setinventoryLog['qty_after_change'] - $setinventoryLog['qty_before_change'];
                                            }
                                        }
                                        $final_total_qty = ($total_product_qty - $total_deduct_qty) + $total_add_qty;
                                        $updateStock = [
                                            'qty' => $final_total_qty,
                                            'updated_at' => config('constants.date_time'),
                                            'updated_by' => Auth::user()->id
                                        ];
                                        CustomerLiquorInventory::where(['cl_id'=>$inventoryId,'branch_id'=>$branchId,'product_id'=>$productId])->update($updateStock);
                                        $pushInventory[] = $inventoryId;
                                    }

                                }

                            } else {

                                $inventoryId = $setProductStoreInventoryArray['cl_id'];
                                $productId = $setProductStoreInventoryArray['product_id'];
                                $branchId = $setProductStoreInventoryArray['branch_id'];
                                $total_product_qty = $setProductStoreInventoryArray['qty'];
                                $total_deduct_qty = 0;$final_total_qty = 0;
                                $total_add_qty = 0;

                                $getinventoryLog = $setProductStoreInventoryArray['customer_liquor_inventory_log'];
                                if (!empty($getinventoryLog) && is_array($getinventoryLog)) {
                                    foreach ($getinventoryLog as $setinventoryLog) {
                                        $productStoreInventoryLog->uuid = Helper::getUuid();
                                        $productStoreInventoryLog->cl_id = $setinventoryLog['cl_id'];
                                        $productStoreInventoryLog->cl_appId = $setinventoryLog['cl_appId'];
                                        $productStoreInventoryLog->branch_id = $setinventoryLog['branch_id'];
                                        $productStoreInventoryLog->product_id = $setinventoryLog['product_id'];
                                        $productStoreInventoryLog->customer_id = $setinventoryLog['employe_id'];
                                        $productStoreInventoryLog->il_type = $setinventoryLog['il_type'];
                                        $productStoreInventoryLog->qty = $setinventoryLog['qty'];
                                        $productStoreInventoryLog->qty_before_change = $setinventoryLog['qty_before_change'];
                                        $productStoreInventoryLog->qty_after_change = $setinventoryLog['qty_after_change'];
                                        $productStoreInventoryLog->updated_by = ($setinventoryLog['updated_by'] != 0) ? $setinventoryLog['updated_by'] : NULL;
                                        $productStoreInventoryLog->updated_at = ($setinventoryLog['updated_at']) ? $setinventoryLog['updated_at'] : $setinventoryLog['created_at'];
                                        $productStoreInventoryLog = ProductStoreInventoryLog::create($productStoreInventoryLog->toArray());

                                        /* Calculate Stock Inventory */
                                        if($setinventoryLog['il_type'] == 2){
                                            $total_deduct_qty += $setinventoryLog['qty_before_change'] - $setinventoryLog['qty_after_change'];
                                        } else {
                                            $total_add_qty += $setinventoryLog['qty_after_change'] - $setinventoryLog['qty_before_change'];
                                        }
                                    }
                                    $final_total_qty = ($total_product_qty - $total_deduct_qty) + $total_add_qty;
                                    $updateStock = [
                                        'qty' => $final_total_qty,
                                        'updated_at' => config('constants.date_time')
                                    ];
                                    ProductStoreInventory::where(['cl_id'=>$inventoryId,'branch_id'=>$branchId,'product_id'=>$productId])->update($updateStock);
                                    $pushInventory[] = $inventoryId;
                                }
                            }
                        }
                        DB::commit();
                        Helper::log('Update Customer Liquor Inventory AppData Synch : Inventory Created');
                        $message = trans('api.inventory_created');
                        $loadInventoryInfo = $this->customerInventoryInfo($pushInventory);
                        $response['product_store_inventory'] = $loadInventoryInfo;
                        // total time taking api response
                        $timeEnd = microtime(true);
                        $response['timetaking'] = $timeEnd - $timeStart;
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);

                    } else {
                        DB::rollBack();
                        Helper::log('Update Customer Liquor Inventory AppData Synch : fail json to array converting');
                        Helper::saveTerminalLog($terminalId, $branchId, 'Store Customer Liquor Inventory Order AppOrderData Sync', 'Update product Store inventory Order data SynchronizeAppdata faid json to array conversation', date('Y-m-d'), date('H:i:s'), 'product_store_inventory');
                        $message = trans('api.faid_json_to_array');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('Update Stock Inventory AppData Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Store Customer Liquor Inventory Order AppOrderData Sync', 'Update product Store inventory Order data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'product_store_inventory');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }

        } catch (\Exception $exception) {
            Helper::log('Update Inventory Customer Liquor Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method  : retrive inventory info
     * @params  : $InventoryIds(req)
     */

    public function customerInventoryInfo($pushInventoryId = [])
    {
        Helper::log('AppInventoryData info : start');
        try{

            if(empty($pushInventoryId)){
                Helper::log('AppInventoryData Info : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            }
            $pushInventory = [];
            foreach ($pushInventoryId as $inventoryId) {
                $loadInventory = CustomerLiquorInventory::where('cl_id',$inventoryId)->select(['*', 'cl_id as server_id'])->first();
                if (!empty($loadInventory)) {
                    $inventoryLog = CustomerLiquorInventoryLog::where(['cl_id' => $inventoryId])->select(['*'])->get()->toArray();
                    if (!empty($inventoryLog)) {

                        $loadInventory['customer_liquor_inventory_log'] = $inventoryLog;
                    }
                }
                $pushInventory[] = $loadInventory;
            }

            return $pushInventory;

        } catch (\Exception $exception) {
            Helper::log('AppInventoryData info Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /**
     * Customer Sync
     * @param Request $request
     * @param $locale
     * @return \Illuminate\Http\JsonResponse
     */

    public function createCustomer(Request $request, $locale)
    {
        Helper::log('AppCustomerData Synch : Start');
        App::setLocale($locale);
        DB::beginTransaction();
        try{
            $customer = $request->customer;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;

            if(empty($customer)){
                Helper::log('AppCustomerData Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } elseif (empty($terminalId)) {
                Helper::log('AppCustomerData Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($branchId)) {
                Helper::log('AppCustomerData Table Synch : Branch Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            } else {

                $timeStart = microtime(true);
                if (\GuzzleHttp\json_decode($customer, true)) {
                    $getCustomerArray = \GuzzleHttp\json_decode($customer, true);
                    $pushShift = [];
                    if (is_array($getCustomerArray)) {  // valid array
                        foreach ($getCustomerArray as $setCustomerArray) {
                            $isExistingShift = array_key_exists("server_id", $setCustomerArray);
                            if ($isExistingShift) {
                                $serverId = $setCustomerArray['server_id'];
                                $customer = Customer::where('customer_id',$serverId)->first();
                                if (empty($customer)) {
                                    $customer = new Customer();

                                    $customer->uuid = Helper::getUuid();
                                    $customer->terminal_id = ($setCustomerArray['terminal_id']) ? $setCustomerArray['terminal_id'] : "";
                                    $customer->app_id = $setCustomerArray['app_id'];
                                    $customer->first_name = $setCustomerArray['first_name'];
                                    $customer->last_name = $setCustomerArray['last_name'];
                                    $customer->name = $setCustomerArray['name'];
                                    $customer->username = $setCustomerArray['username'];
                                    $customer->email = $setCustomerArray['email'];
                                    $customer->role = $setCustomerArray['role'];
                                    $customer->phonecode = $setCustomerArray['phonecode'];
                                    $customer->mobile = $setCustomerArray['mobile'];
                                    $customer->password = Hash::make($setCustomerArray['password']);
                                    $customer->address = $setCustomerArray['address'];
                                    $customer->country_id = $setCustomerArray['country_id'];
                                    $customer->state_id = $setCustomerArray['state_id'];
                                    $customer->city_id = $setCustomerArray['city_id'];
                                    $customer->zipcode = $setCustomerArray['zipcode'];
                                    $customer->api_token = $setCustomerArray['api_token'];
                                    $customer->last_login = $setCustomerArray['last_login'];
                                    $customer->status = $setCustomerArray['status'];
                                    $customer->created_at = ($setCustomerArray['created_at'] != '') ? $setCustomerArray['created_at'] : NULL;
                                    $customer->created_by = ($setCustomerArray['created_by'] != 0) ? $setCustomerArray['created_by'] : NULL;
                                    $customer->updated_by = ($setCustomerArray['updated_by'] != 0) ? $setCustomerArray['updated_by'] : NULL;
                                    $customer->updated_at = ($setCustomerArray['updated_at'] != '') ? $setCustomerArray['updated_at'] : NULL;

                                    $customer = Customer::create($customer->toArray());
                                    $customerId = $customer->customer_id;
                                    $customer['server_id'] = $customerId;
                                    $pushCustomer[] = $customer;
                                } else {

                                    $customer->terminal_id = ($setCustomerArray['terminal_id']) ? $setCustomerArray['terminal_id'] : "";
                                    $customer->app_id = $setCustomerArray['app_id'];
                                    $customer->first_name = $setCustomerArray['first_name'];
                                    $customer->last_name = $setCustomerArray['last_name'];
                                    $customer->name = $setCustomerArray['name'];
                                    $customer->username = $setCustomerArray['username'];
                                    $customer->username = $setCustomerArray['username'];
                                    $customer->email = $setCustomerArray['email'];
                                    $customer->role = $setCustomerArray['role'];
                                    $customer->phonecode = $setCustomerArray['phonecode'];
                                    $customer->mobile = $setCustomerArray['mobile'];
                                    $customer->address = $setCustomerArray['address'];
                                    $customer->country_id = $setCustomerArray['country_id'];
                                    $customer->state_id = $setCustomerArray['state_id'];
                                    $customer->city_id = $setCustomerArray['city_id'];
                                    $customer->zipcode = $setCustomerArray['zipcode'];
                                    $customer->api_token = $setCustomerArray['api_token'];
                                    $customer->last_login = $setCustomerArray['last_login'];
                                    $customer->status = $setCustomerArray['status'];
                                    $customer->created_at = ($setCustomerArray['created_at'] != '') ? $setCustomerArray['created_at'] : NULL;
                                    $customer->created_by = ($setCustomerArray['created_by'] != 0) ? $setCustomerArray['created_by'] : NULL;
                                    $customer->updated_by = ($setCustomerArray['updated_by'] != 0) ? $setCustomerArray['updated_by'] : NULL;
                                    $customer->updated_at = ($setCustomerArray['updated_at'] != '') ? $setCustomerArray['updated_at'] : NULL;

                                    Customer::where('customer_id',$serverId)->update($customer->toArray());
                                    $pushCustomer[] = $customer;
                                }
                            } else {
                                $customer = new Customer();

                                $customer->uuid = Helper::getUuid();
                                $customer->terminal_id = ($setCustomerArray['terminal_id']) ? $setCustomerArray['terminal_id'] : "";
                                $customer->app_id = $setCustomerArray['app_id'];
                                $customer->first_name = $setCustomerArray['first_name'];
                                $customer->last_name = $setCustomerArray['last_name'];
                                $customer->name = $setCustomerArray['name'];
                                $customer->username = $setCustomerArray['username'];
                                $customer->username = $setCustomerArray['username'];
                                $customer->email = $setCustomerArray['email'];
                                $customer->role = $setCustomerArray['role'];
                                $customer->phonecode = $setCustomerArray['phonecode'];
                                $customer->mobile = $setCustomerArray['mobile'];
                                $customer->address = $setCustomerArray['address'];
                                $customer->country_id = $setCustomerArray['country_id'];
                                $customer->state_id = $setCustomerArray['state_id'];
                                $customer->city_id = $setCustomerArray['city_id'];
                                $customer->zipcode = $setCustomerArray['zipcode'];
                                $customer->api_token = $setCustomerArray['api_token'];
                                $customer->last_login = $setCustomerArray['last_login'];
                                $customer->status = $setCustomerArray['status'];
                                $customer->created_at = ($setCustomerArray['created_at'] != '') ? $setCustomerArray['created_at'] : NULL;
                                $customer->created_by = ($setCustomerArray['created_by'] != 0) ? $setCustomerArray['created_by'] : NULL;
                                $customer->updated_by = ($setCustomerArray['updated_by'] != 0) ? $setCustomerArray['updated_by'] : NULL;
                                $customer->updated_at = ($setCustomerArray['updated_at'] != '') ? $setCustomerArray['updated_at'] : NULL;

                                $customer = Customer::create($customer->toArray());
                                $customerId = $customer->customer_id;
                                $customer['server_id'] = $customerId;
                                $pushCustomer[] = $customer;
                            }
                        }
                        DB::commit();
                        $response['customer'] = $pushCustomer;
                        $message = 'Bulk Customer successfully created.';
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
                    } else {
                        DB::rollBack();
                        Helper::log('AppCustomerData Table Synch : Invalid Json String');
                        $message = trans('api.invalid_json_string');
                        Helper::saveTerminalLog($terminalId, $branchId, 'Customer Auto Sync', 'Create Customer data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'customer');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('AppCustomerData Table Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Customer Auto Sync', 'Create Customer data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'customer');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }
        } catch (\Exception $exception) {
            Helper::log('AppCustomerData Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }
}
