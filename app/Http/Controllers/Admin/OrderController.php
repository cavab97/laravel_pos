<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Order;
use App\Models\OrderAttributes;
use App\Models\OrderDetail;
use App\Models\Permissions;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_orders');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.order.index');
    }

    /**
     * Pagination for backend users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];

            $defaultCondition = 'order.uuid != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $defaultCondition .= " AND ( name LIKE '%$search%' OR email LIKE '%$search%' OR mobile LIKE '%$search%' ) ";
            }

            $name = $request->input('name', null);
            if ($name != null) {
                $name = Helper::string_sanitize($name);
                //$defaultCondition .= " AND `customer_name` LIKE '%$name%' ";
                $defaultCondition .= " AND (`customer`.name LIKE '%$name%') ";
            }
            $invoice_no = $request->input('invoice_no', null);
            if ($invoice_no != null) {
                $invoice_no = Helper::string_sanitize($invoice_no);
                $defaultCondition .= " AND `invoice_no` LIKE '%$invoice_no%' ";
            }
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $from = isset($from_date) ? (date('Y-m-d', strtotime($from_date))) : null;
            $to = isset($to_date) ? (date('Y-m-d', strtotime($to_date))) : null;

            if (empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`order`.order_date, '%Y-%m-%d') <= '" . $to . "'";
            }
            if (!empty($from) && empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`order`.order_date, '%Y-%m-%d') >= '" . $from . "'";
            }
            if (!empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`order`.order_date, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
            }
            $userCount = Order::leftjoin('customer', 'customer.customer_id', '=', 'order.customer_id')->whereRaw($defaultCondition)
                ->count();
            $userList = Order::leftjoin('customer', 'customer.customer_id', '=', 'order.customer_id')->whereRaw($defaultCondition)
                ->select(
                    'order_id', 'order.uuid', 'table_id', 'invoice_no', 'sub_total', 'sub_total_after_discount', 'grand_total', 'order_source', 'order_status', 'customer.name as customer_name',
                    DB::raw('DATE_FORMAT(order.order_date, "%d-%m-%Y %h:%i %p") as order_date'),
                    DB::raw('(SELECT name FROM branch WHERE branch.branch_id = order.branch_id) AS branch_name'),
                    DB::raw('(SELECT terminal_name FROM terminal WHERE terminal.terminal_id = order.terminal_id) AS terminal_name')
                    //DB::raw('(SELECT name FROM customer WHERE customer.customer_id = order.customer_id) AS customer_name')
                )
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get();
            return response()->json([
                "aaData" => $userList,
                "iTotalDisplayRecords" => $userCount,
                "iTotalRecords" => $userCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('Order pagination exception');
            Helper::log($exception);
        }
    }

    public function show($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_orders');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $language_id = Languages::getBackLanguageId();
        $orderData = Order::where('order.uuid', $uuid)
            ->leftjoin('customer', 'customer.customer_id', '=', 'order.customer_id')
			->leftjoin('order_payment','order_payment.order_id','order.order_id')
            ->leftjoin('payment','payment.payment_id','order_payment.op_method_id')
            ->select('order.*', 'customer.name as customer_name','order_payment.is_split','order_payment.remark','order_payment.last_digits','order_payment.approval_code','order_payment.reference_number','order_payment.op_method_id','order_payment.op_status as payment_status','payment.name as payment_name')
            ->first();
        if ($orderData->branch_id) {
            $branchId = $orderData->branch_id;
            $branchName = Branch::select('name')->where('branch_id', $branchId)->first();
            $orderData->branch_name = $branchName->name;
            $orderData->taxDetail = json_decode($orderData->tax_json, true);
        }
		
		if ($orderData->terminal_id) {
            $terminal_id = $orderData->terminal_id;
            $terminalData = Terminal::select('terminal_name')->where('terminal_id',$terminal_id)->first();
            $orderData->terminal_name = $terminalData->terminal_name;
        }
		
        $orderDetails = OrderDetail::where('order_id', $orderData->order_id)->get()->toArray();
        $orderDetail_discount1 = 0;
        $orderDetail_discount2 = 0;
        foreach ($orderDetails as $key => $value) {
             $productDetail = json_decode($value['product_detail'], true);
			 
             $orderDetails[$key]['product_name'] = $productDetail['name'];
             /*$orderDetail_discount1 += $value['discount'];*/
            $attributeData = OrderAttributes::where('order_id', $orderData->order_id)->where('detail_id', $value['detail_id'])->get()->toArray();
            $orderDetails[$key]['attributes'] = $attributeData;
            $orderDetails[$key]['product_detail'] = json_decode($value['product_detail'], true);
        }
        return view('backend.order.view', compact('orderData', 'orderDetails'));
    }
}
