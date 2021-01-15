<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Order;
use App\Models\OrderCancel;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\Payment;
use App\Models\Permissions;
use App\Models\ProductCategory;
use App\Models\Shift;
use App\Models\Terminal;
use App\Models\TerminalLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerIndex()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_customer_reports');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.reports.customer');
    }

    /**
     * Pagination for backend customer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerPaginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];

            $defaultCondition = 'uuid != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $defaultCondition .= " AND ( name LIKE '%$search%' OR email LIKE '%$search%' OR mobile LIKE '%$search%' ) ";
            }

            $name = $request->input('name', null);
            if ($name != null) {
                $name = Helper::string_sanitize($name);
                $defaultCondition .= " AND `name` LIKE '%$name%' ";
            }

            $mobile = $request->input('mobile', null);
            if ($mobile != null) {
                $defaultCondition .= " AND `mobile` LIKE '%$mobile%' ";
            }
            $email = $request->input('email', null);
            if ($email != null) {
                $defaultCondition .= " AND `email` LIKE '%$email%' ";
            }

            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $from = isset($from_date) ? (date('Y-m-d', strtotime($from_date))) : null;
            $to = isset($to_date) ? (date('Y-m-d', strtotime($to_date))) : null;

            if (empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') <= '" . $to . "'";
            }
            if (!empty($from) && empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') >= '" . $from . "'";
            }
            if (!empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
            }
            $status = $request->input('status', null);
            if ($status != null) {
                $defaultCondition .= " AND status =" . $status;
            }
            $cusCount = Customer::whereRaw($defaultCondition)
                ->count();
            $cusList = Customer::whereRaw($defaultCondition)
                ->select(
                    'customer_id',
                    'uuid',
                    'name',
                    'email',
                    'mobile',
                    'profile',
                    'status',
                    'last_login',
                    'created_at'
                )
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get()->toArray();
            foreach ($cusList as $key => $value) {
                $cusList[$key]['created_at'] = date('Y-m-d H:i A', strtotime($value['created_at']));
                if (empty($value['profile']) || !file_exists(public_path($value['profile']))) {
                    $cusList[$key]['profile'] = config('constants.default_user');
                }
            }
            return response()->json([
                "aaData" => $cusList,
                "iTotalDisplayRecords" => $cusCount,
                "iTotalRecords" => $cusCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('User pagination exception');
            Helper::log($exception);
        }
    }

    public function customerExportData(Request $request)
    {
        $fileName = 'Customer_' . time() . '.xlsx';
        return Excel::download(new CustomerExport($request->all()), $fileName);
    }

    public function categoryReportIndex(Request $request)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_category_reports');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.reports.category_report');
    }

    public function categoryReportPaginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];

            $defaultCondition = '`order`.uuid != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $defaultCondition .= " AND ( name LIKE '%$search%' OR email LIKE '%$search%' OR mobile LIKE '%$search%' ) ";
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
            $categoryCount = OrderDetail::leftjoin('product', 'product.product_id', 'order_detail.product_id')
                ->leftjoin('order', 'order.order_id', 'order_detail.order_id')
                ->leftjoin('product_category', 'product_category.product_id', 'product.product_id')
                ->leftjoin('category', 'category.category_id', 'product_category.category_id');
            $categoryList = OrderDetail::leftjoin('product', 'product.product_id', 'order_detail.product_id')
                ->leftjoin('order', 'order.order_id', 'order_detail.order_id')
                ->leftjoin('product_category', 'product_category.product_id', 'product.product_id')
                ->leftjoin('category', 'category.category_id', 'product_category.category_id');
            if (Auth::user()->role > 1) {
                $categoryCount = $categoryCount->join('product_branch', 'product_branch.product_id', 'product.product_id')->whereIn('product_branch.branch_id', Auth::user()->getBranchIds());
                $categoryList = $categoryList->join('product_branch', 'product_branch.product_id', 'product.product_id')->whereIn('product_branch.branch_id', Auth::user()->getBranchIds());
            }
            $categoryCount = $categoryCount
                ->whereRaw($defaultCondition)
                ->where('order.order_status', 4)
                ->select('category.category_id', 'category.name', DB::raw('SUM(order_detail.detail_qty) AS TotalQuantity'), DB::raw('SUM(order.grand_total) AS Total'))
                ->groupBy('category.category_id')
                ->count();
            //$categoryCount = count($categoryCount);
            $categoryList = $categoryList
                ->whereRaw($defaultCondition)
                ->where('order.order_status', 4)
                ->select('category.category_id', 'category.name', DB::raw('SUM(order_detail.detail_qty) AS TotalQuantity'), DB::raw('SUM(order.grand_total) AS Total'))
                ->groupBy('category.category_id')
                ->orderBy('Total', 'DESC')
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get();
            foreach ($categoryList as $key => $value) {
                $categoryList[$key]['index'] = ++$start;
            }
            return response()->json([
                "aaData" => $categoryList,
                "iTotalDisplayRecords" => $categoryCount,
                "iTotalRecords" => $categoryCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('User pagination exception');
            Helper::log($exception);
        }
    }

    public function shiftReportIndex(Request $request)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_shift_reports');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $availableBranch = [];
        if (Auth::user()->role > 1) {
            $availableBranch = Branch::whereIn('branch_id', Auth::user()->getBranchIds())->pluck('name', 'branch_id')->toArray(); //
            $terminalList = Terminal::whereIn('branch_id', Auth::user()->getBranchIds());
        } else {
            $availableBranch = Branch::pluck('name', 'branch_id')->toArray();
            $terminalList = Terminal::all();
        }
        return view('backend.reports.shift_report', compact('terminalList', 'availableBranch'));
    }

    public function shiftPaginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];
            $branchIds = $request->branch_id;
            $getTerId = array();
            $defaultCondition = 'shift.uuid != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $whereterminal = " ( terminal_name LIKE '%$search%' ) ";
                $getTerminalId = Terminal::whereRaw($whereterminal)->select('terminal_id')->get()->toArray();
                foreach ($getTerminalId as $value) {
                    array_push($getTerId, $value['terminal_id']);
                }
                $implodeTermId = implode(',', $getTerId);
                if (!empty($implodeTermId)) {
                    $defaultCondition .= " AND shift.terminal_id in ($implodeTermId)";
                } else {
                    $defaultCondition .= " AND shift.terminal_id in ('$implodeTermId')";
                }
            }

            $terminal_id = $request->input('terminal_id', null);
            if ($terminal_id != null) {
                $defaultCondition .= " AND `shift`.terminal_id = '$terminal_id' ";
            }

            $shiftList = Shift::select(
                'shift.*',
                DB::raw("(select terminal_name FROM terminal where terminal_id = shift.terminal_id) AS terminal_name"),
                DB::raw("(select name FROM branch where branch_id = shift.branch_id) AS branch_name"),
                DB::raw("(SELECT CAST(shift.start_amount AS DECIMAL(16,2))) AS start_amount"),
                DB::raw("(SELECT CAST(shift.end_amount AS DECIMAL(16,2))) AS end_amount"),
                DB::raw("(select name FROM users where id = shift.user_id) AS user_name")
            );
            if (!isset($branchIds) || empty($branchIds)) {
                $branchIds = Auth::user()->getBranchIds();
            } else {
                $branchIds = explode(',', $branchIds);
            }
            if (Auth::user()->role > 1) {
                $shiftList = $shiftList->whereIn('shift.branch_id', $branchIds);
            }
            $shiftList = $shiftList->whereRaw($defaultCondition);
            $shiftCount = $shiftList->count();
            $shiftList = $shiftList
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                /*->forPage($start, $page_length)
                 ->skip($start)
                ->take($page_length) */
                ->get();

            return response()->json([
                "aaData" => $shiftList,
                "iTotalDisplayRecords" => $shiftCount,
                "iTotalRecords" => $shiftCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('Shift report pagination : exception');
            Helper::log($exception);
        }
    }


    public function cancelledReportIndex(Request $request)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_shift_reports');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $availableBranch = [];
        if (Auth::user()->role > 1) {
            $availableBranch = Branch::whereIn('branch_id', Auth::user()->getBranchIds())->pluck('name', 'branch_id')->toArray(); //
            $terminalList = Terminal::whereIn('branch_id', Auth::user()->getBranchIds());
        } else {
            $availableBranch = Branch::pluck('name', 'branch_id')->toArray();
            $terminalList = Terminal::all();
        }
        return view('backend.reports.cancelled_report', compact('terminalList', 'availableBranch'));
    }

    public function cancelledPaginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];
            $branchIds = $request->branch_id;
            $getTerId = array();
            $defaultCondition = 'order_cancel.status != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $whereterminal = " ( terminal_name LIKE '%$search%' ) ";
                $getTerminalId = Terminal::whereRaw($whereterminal)->select('terminal_id')->get()->toArray();
                foreach ($getTerminalId as $value) {
                    array_push($getTerId, $value['terminal_id']);
                }
                $implodeTermId = implode(',', $getTerId);
                if (!empty($implodeTermId)) {
                    $defaultCondition .= " AND shift.terminal_id in ($implodeTermId)";
                } else {
                    $defaultCondition .= " AND shift.terminal_id in ('$implodeTermId')";
                }
            }

            $terminal_id = $request->input('terminal_id', null);
            if ($terminal_id != null) {
                $defaultCondition .= " AND `order_cancel`.terminal_id = '$terminal_id' ";
            }

            if (!isset($branchIds) || empty($branchIds)) {
                $branchIds = Auth::user()->getBranchIds();
            } else {
                $branchIds = explode(',', $branchIds);
            }
            if (Auth::user()->role > 1) {
                $ordersIdsList = Order::join('order', 'order.order_id', 'order_cancel.order_id')->whereIn('order.branch_id', $branchIds);
                $shiftList = $shiftList->whereIn('order_cancel.order_id', $ordersIdsList);
            }
            $invoice_no = $request->input('invoice_no', null);
            if ($invoice_no != null) {
                $invoice_no = Helper::string_sanitize($invoice_no);
                $ordersIdsList = $ordersIdsList->where('invoice_no', 'LIKE', $invoice_no);
            }
            $orderCancelList = OrderCancel::join('order', 'order.order_id', 'order_cancel.order_id')->whereIn('order.branch_id', $branchIds)
                ->select(
                    'order_cancel.*',
                    DB::raw("(SELECT name FROM branch where branch_id = order.branch_id) AS branch_name"),
                    DB::raw("(SELECT CAST(order.grand_total AS DECIMAL(16,2))) AS grand_total"),
                    DB::raw("(SELECT name FROM users WHERE id = order_cancel.created_by) AS cashier"),
                    DB::raw("(SELECT order.uuid) AS uuid"),
                    DB::raw("(SELECT order.invoice_no) AS invoice_no"),
                    DB::raw("(SELECT terminal_name FROM terminal where terminal_id = order_cancel.terminal_id) AS terminal_name"),
                );
            /* ,
                DB::raw("(select name FROM users where id = shift.user_id) AS user_name")); */
            $orderCancelList = $orderCancelList
            ->whereRaw($defaultCondition);
            $orderCancelCount = $orderCancelList->count();
            $orderCancelData = $orderCancelList
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get();
            return response()->json([
                "aaData" => $orderCancelData,
                "iTotalDisplayRecords" => $orderCancelCount,
                "iTotalRecords" => $orderCancelCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('Shift report pagination : exception');
            Helper::log($exception);
        }
    }
    public function paymentReportIndex(Request $request)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_shift_reports');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $availableBranch = [];
        if (Auth::user()->role > 1) {
            $availableBranch = Branch::whereIn('branch_id', Auth::user()->getBranchIds())->pluck('name', 'branch_id')->toArray(); //
            $terminalList = Terminal::whereIn('branch_id', Auth::user()->getBranchIds());
        } else {
            $availableBranch = Branch::pluck('name', 'branch_id')->toArray();
            $terminalList = Terminal::all();
        }
        return view('backend.reports.payment_transaction', compact('terminalList', 'availableBranch'));
    }
    public function paymentPaginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];

            $branchIds = $request->branch_id;
            $terminal_id = $request->input('terminal_id', null);
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $defaultCondition = " op_id != ''";
            $getTerminalId = array();
            if (!empty($from_date) && $from_date == $to_date) {
                $orderData = Order::whereDate('order_date', '<=', $from_date);
            } else if (!empty($from_date) && !empty($to_date)) {
                $orderData = Order::whereBetween('order_date', [$from_date, $to_date]);
            } else if (!empty($from_date)) {
                $orderData = Order::whereDate('order_date', '>=', $from_date);
            } else if (!empty($to_date)) {
                $orderData = Order::whereDate('order_date', '<=', $to_date);
            }
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $whereterminal = " ( invoice_no LIKE '%$search%' ) ";
                if (isset($orderData) && $orderData == null) {
                    $orderData = Order::whereRaw($whereterminal);
                } else {
                    $orderData = $orderData->whereRaw($whereterminal);
                }
            }
            if ($terminal_id != null) {
                $defaultCondition .= " AND `order_payment`.terminal_id = '$terminal_id' ";
            }
            if (!isset($branchIds) || empty($branchIds)) {
                $branchIds = Auth::user()->getBranchIds();
            } else {
                $branchIds = explode(',', $branchIds);
            }
            if (isset($orderData)) {
                $getTerminalId = $orderData->pluck('order_id')->toArray();
            } else {
                $getTerminalId = Order::whereIn('branch_id', $branchIds)->pluck('order_id')->toArray();
            }
            $paymentList =
                Payment::join('order_payment', 'order_payment.op_method_id', 'payment.payment_id')
                ->whereIn('order_payment.order_id', $getTerminalId)
                ->groupBy('order_payment.op_method_id');
            $paymentList = $paymentList->select(
                'payment.payment_id AS id',
                'payment.name AS payment_option',
                DB::raw("(
                    SELECT SUM(order_payment.op_amount)
                    FROM payment p
                    WHERE order_payment.op_method_id = payment.payment_id
                    AND p.payment_id = payment.payment_id
                    AND order_payment.op_amount > 0
                    GROUP BY order_payment.op_method_id) AS sales_amount
                "),
                DB::raw("(
                    SELECT COUNT(order_payment.op_id)
                    FROM payment p
                    WHERE order_payment.op_method_id = payment.payment_id
                    AND p.payment_id = payment.payment_id
                    AND order_payment.op_amount > 0
                    GROUP BY order_payment.op_method_id) AS total_sales_transaction
                "),
                DB::raw("(
                    SELECT SUM(ABS(order_payment.op_amount))
                    WHERE order_payment.op_status IN (5)
                    AND order_payment.op_method_id = payment.payment_id
                    GROUP BY order_payment.op_method_id) AS refunds_amount
                "),
                DB::raw("(
                    SELECT COUNT(order_payment.op_id)
                    WHERE order_payment.op_status IN (5)
                    AND order_payment.op_method_id = payment.payment_id
                    GROUP BY order_payment.op_method_id) AS refunds_transaction
                "),
                DB::raw("(
                    SELECT SUM(order_payment.op_amount) FROM `order_cancel`
                    JOIN `order_payment`    ON order_payment.order_id = order_cancel.order_id
                    WHERE order_payment.op_status NOT IN (5)
                    AND order_payment.op_method_id = payment.payment_id
                    GROUP BY order_payment.op_method_id) AS cancel_amount
                "),
                DB::raw("(
                    SELECT COUNT(order_cancel.order_id) FROM `order_cancel`
                    JOIN `order_payment`    ON order_payment.order_id = order_cancel.order_id
                    WHERE order_payment.op_status NOT IN (5)
                    AND order_payment.op_method_id = payment.payment_id
                    GROUP BY order_payment.op_method_id) AS cancel_transaction
                "),
            );
            $paymentList = $paymentList->whereRaw($defaultCondition);
            $paymentData = $paymentList
                //->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get();
            $paymentCount = $paymentList->pluck('id')->count();
            return response()->json([
                "aaData" => $paymentData,
                "iTotalDisplayRecords" => $paymentCount,
                "iTotalRecords" => $paymentCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('Shift report pagination : exception');
            Helper::log($exception);
        }
    }

    public function itemDiscountReportIndex(Request $request)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_shift_reports');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $availableBranch = [];
        if (Auth::user()->role > 1) {
            $branchIds = Auth::user()->getBranchIds();
            $availableBranch = Branch::whereIn('branch_id', $branchId)->pluck('name', 'branch_id')->toArray();
            $terminalList = Terminal::whereIn('branch_id', $branchId);
            $categoryIds = CategoryBranch::whereIn('branch_id', $branchId);
            $categoryList = Category::whereIn('category_id', $categoryIds)->pluck('name', 'category_id')->toArray();
        } else {
            $availableBranch = Branch::pluck('name', 'branch_id')->toArray();
            $terminalList = Terminal::all();
            $categoryList = Category::pluck('name', 'category_id')->toArray();
        }
        return view('backend.reports.discount_item_report', compact('terminalList', 'availableBranch', 'categoryList'));
    }
    public function itemDiscountPaginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $col = 'mDataProp_' . $request['iSortCol_0'];
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];

            $branchIds = $request->branch_id;
            $terminal_id = $request->input('terminal_id', null);
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $category_id = $request->category_id;
            $defaultCondition = " detail_id != ''";
            $getTerminalId = array();
            if (!empty($from_date) && $from_date == $to_date) {
                $orderData = Order::whereDate('order_date', '<=', $from_date);
            } else if (!empty($from_date) && !empty($to_date)) {
                $orderData = Order::whereBetween('order_date', [$from_date, $to_date]);
            } else if (!empty($from_date)) {
                $orderData = Order::whereDate('order_date', '>=', $from_date);
            } else if (!empty($to_date)) {
                $orderData = Order::whereDate('order_date', '<=', $to_date);
                Log::debug($orderData->pluck('order_id')->toArray());
            }
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $whereterminal = " ( invoice_no LIKE '%$search%' ) ";
                if (isset($orderData) && $orderData == null) {
                    $orderData = Order::whereRaw($whereterminal);
                } else {
                    $orderData = $orderData->whereRaw($whereterminal);
                }
            }
            if ($terminal_id != null) {
                $defaultCondition .= " AND `order_payment`.terminal_id = '$terminal_id' ";
            }
            if (!isset($branchIds) || empty($branchIds)) {
                $branchIds = Auth::user()->getBranchIds();
            } else {
                $branchIds = explode(',', $branchIds);
            }
            if (isset($orderData)) {
                $getTerminalId = $orderData->pluck('order_id')->toArray();
            } else {
                $getTerminalId = Order::whereIn('branch_id', $branchIds)->pluck('order_id')->toArray();
            }
            $price = $request->price;
            $percentage_discount = $request->percentage_discount;
            if (!empty($price)) {
                $price_opt = $request->price_opt;
                $defaultCondition .= " AND `order_detail`.discount_amount $price_opt " . $price;
            } else if (!empty($percentage_discount)) {
                $defaultCondition .= " AND `order_detail`.discount_amount >=" . $percentage_discount;
            }
            $getDiscountItem = OrderDetail::where('discount_amount', '>', 0)
            ->whereIn('order_detail.terminal_id', $getTerminalId);
            if (!empty($category_id)) {
                $productIds = ProductCategory::where('category_id', $category_id)->pluck('product_id')->toArray();
                $getDiscountItem = $getDiscountItem->whereIn('product_id', $productIds);
            }
            $getDiscountItem = $getDiscountItem->whereRaw($defaultCondition);
            Helper::log($getDiscountItem->get()->toArray());
            $getDiscountItem = $getDiscountItem->select(
                'product_name', 'discount_type',
                'discount_amount', 'discount_remark',
                'updated_at',
                DB::raw("(SELECT name FROM `users` WHERE id = order_detail.updated_by) AS cashier"),
                DB::raw("(SELECT uuid FROM `order` WHERE order_id = order_detail.order_id) AS uuid"),
                DB::raw("(SELECT invoice_no FROM `order` WHERE order_id = order_detail.order_id) AS invoice_no"),
                DB::raw("(SELECT terminal_name FROM `terminal` where terminal_id = order_detail.terminal_id) AS terminal_name"),
            );
            $discountItemData = $getDiscountItem
                //->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get();
            $discountItemCount = $getDiscountItem->count();
            return response()->json([
                "aaData" => $discountItemData,
                "iTotalDisplayRecords" => $discountItemCount,
                "iTotalRecords" => $discountItemCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('Shift report pagination : exception');
            Helper::log($exception);
        }
    }

    public function orderDiscountReportIndex(Request $request)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_shift_reports');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $availableBranch = [];
        if (Auth::user()->role > 1) {
            $availableBranch = Branch::whereIn('branch_id', Auth::user()->getBranchIds())->pluck('name', 'branch_id')->toArray(); //
            $terminalList = Terminal::whereIn('branch_id', Auth::user()->getBranchIds());
        } else {
            $availableBranch = Branch::pluck('name', 'branch_id')->toArray();
            $terminalList = Terminal::all();
        }
        return view('backend.reports.payment_transaction', compact('terminalList', 'availableBranch'));
    }
    public function orderDiscountPaginate(Request $request)
    {
        try {
            $search         = $request['sSearch'];
            $start          = $request['iDisplayStart'];
            $page_length    = $request['iDisplayLength'];
            $col            = 'mDataProp_' . $request['iSortCol_0'];
            $order_by_field = $request->$col;
            $order_by       = $request['sSortDir_0'];

            $branchIds = $request->branch_id;
            $terminal_id = $request->input('terminal_id', null);
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $defaultCondition = " op_id != ''";
            $getTerminalId = array();
            if (!empty($from_date) && $from_date == $to_date) {
                $orderData = Order::whereDate('order_date', '<=', $from_date);
            } else if (!empty($from_date) && !empty($to_date)) {
                $orderData = Order::whereBetween('order_date', [$from_date, $to_date]);
            } else if (!empty($from_date)) {
                $orderData = Order::whereDate('order_date', '>=', $from_date);
            } else if (!empty($to_date)) {
                $orderData = Order::whereDate('order_date', '<=', $to_date);
                Log::debug($orderData->pluck('order_id')->toArray());
            }
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $whereterminal = " ( invoice_no LIKE '%$search%' ) ";
                if (isset($orderData) && $orderData == null) {
                    $orderData = Order::whereRaw($whereterminal);
                } else {
                    $orderData = $orderData->whereRaw($whereterminal);
                }
            }
            if ($terminal_id != null) {
                $defaultCondition .= " AND `order_payment`.terminal_id = '$terminal_id' ";
            }
            if (!isset($branchIds) || empty($branchIds)) {
                $branchIds = Auth::user()->getBranchIds();
            } else {
                $branchIds = explode(',', $branchIds);
            }
            if (isset($orderData)) {
                $getTerminalId = $orderData->pluck('order_id')->toArray();
            } else {
                $getTerminalId = Order::whereIn('branch_id', $branchIds)->pluck('order_id')->toArray();
            }
            $paymentList =
                Payment::join('order_payment', 'order_payment.op_method_id', 'payment.payment_id')
                ->whereIn('order_payment.order_id', $getTerminalId)
                ->groupBy('order_payment.op_method_id');
            $paymentList = $paymentList->select(
                'payment.payment_id AS id',
                'payment.name AS payment_option',
                DB::raw("(
                    SELECT SUM(order_payment.op_amount)
                    FROM payment p
                    WHERE order_payment.op_method_id = payment.payment_id
                    AND p.payment_id = payment.payment_id
                    AND order_payment.op_amount > 0
                    GROUP BY order_payment.op_method_id) AS sales_amount
                "),
            );
            $paymentList->whereRaw($defaultCondition);
            $orderDiscountData = $paymentList
                //->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get();
            $orderDiscountCount = $orderDiscountData->count();
            return response()->json([
                "aaData" => $orderDiscountData,
                "iTotalDisplayRecords" => $orderDiscountCount,
                "iTotalRecords" => $orderDiscountCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('Shift report pagination : exception');
            Helper::log($exception);
        }
    }
}
