<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Permissions;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

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
                    'customer_id', 'uuid', 'name', 'email', 'mobile', 'profile', 'status', 'last_login', 'created_at')
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get()->toArray();
            foreach ($cusList as $key => $value) {
                $cusList[$key]['created_at'] = date('Y-m-d H:i A', strtotime($value['created_at']));
                if(empty($value['profile']) || !file_exists(public_path($value['profile']))){
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

        /*$categoryList = OrderDetail::leftjoin('category','category.category_id','order_detail.category_id')
            ->leftjoin('order','order.order_id','order_detail.order_id')
            ->where('order.order_status',4)
            ->select('category.category_id','category.name',DB::raw('SUM(order_detail.detail_qty) AS TotalQuantity'),DB::raw('SUM(order.grand_total) AS Total'))
            ->groupBy('category.category_id')
			->orderBy('Total','DESC')
            ->get();*/
        $categoryList = OrderDetail::leftjoin('product','product.product_id','order_detail.product_id')
            ->leftjoin('order','order.order_id','order_detail.order_id')
            ->leftjoin('product_category','product_category.product_id','product.product_id')
            ->leftjoin('category','category.category_id','product_category.category_id')
            ->where('order.order_status',4)
            ->select('category.category_id','category.name',DB::raw('SUM(order_detail.detail_qty) AS TotalQuantity'),DB::raw('SUM(order.grand_total) AS Total'))
            ->groupBy('category.category_id')
            ->orderBy('Total','DESC')
            ->get();

        return view('backend.reports.category_report', compact('categoryList'));
    }

}


