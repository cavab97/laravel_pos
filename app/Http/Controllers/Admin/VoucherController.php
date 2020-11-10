<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_voucher');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.voucher.index');
    }

    /**
     * Pagination for backend customer
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

            $defaultCondition = 'uuid != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $defaultCondition .= " AND ( voucher_name LIKE '%$search%' OR voucher_code LIKE '%$search%' OR voucher_discount LIKE '%$search%' ) ";
            }

            $name = $request->input('voucher_name_code', null);

            if ($name != null) {
                $name = Helper::string_sanitize($name);
                $defaultCondition .= " AND ( voucher_name LIKE '%$name%' OR voucher_code LIKE '%$name%' ) ";
            }


            $voucher_discount_type = $request->input('voucher_discount_type', null);
            if ($voucher_discount_type != null) {
                $defaultCondition .= " AND `voucher_discount_type` = $voucher_discount_type";
            }
            $voucher_discount = $request->input('voucher_discount', null);
            if ($voucher_discount != null) {
                $defaultCondition .= " AND `voucher_discount` LIKE '%$voucher_discount%' ";
            }
            $status = $request->input('status', null);
            if ($status != null) {
                $defaultCondition .= " AND `status` = $status ";
            }
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $from = isset($from_date) ? (date('Y-m-d', strtotime($from_date))) : null;
            $to = isset($to_date) ? (date('Y-m-d', strtotime($to_date))) : null;

            if (empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`voucher`.updated_at, '%Y-%m-%d') <= '" . $to . "'";
            }
            if (!empty($from) && empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`voucher`.updated_at, '%Y-%m-%d') >= '" . $from . "'";
            }
            if (!empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`voucher`.updated_at, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
            }

            $voucherCount = Voucher::whereRaw($defaultCondition)
                ->count();
            $voucherList = Voucher::whereRaw($defaultCondition)
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get()->toArray();
            foreach ($voucherList as $key => $value) {
                $voucherList[$key]['voucher_applicable_from'] = date('Y-m-d', strtotime($value['voucher_applicable_from']));
                $voucherList[$key]['voucher_applicable_to'] = date('Y-m-d', strtotime($value['voucher_applicable_to']));
            }

            return response()->json([
                "aaData" => $voucherList,
                "iTotalDisplayRecords" => $voucherCount,
                "iTotalRecords" => $voucherCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('User pagination exception');
            Helper::log($exception);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_voucher');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $categoryList = Category::where('status', 1)->get();
        $productList = Product::where('status', 1)->get();
        return view('backend.voucher.create', compact('categoryList', 'productList'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Voucher store : start');
        try {
            $voucher_name = $request->voucher_name;
            $voucher_code = $request->voucher_code;
            $voucher_discount_type = $request->voucher_discount_type;
            $voucher_discount = $request->voucher_discount;
            $applicable_from = $request->voucher_applicable_from;
            $applicable_to = $request->voucher_applicable_to;
            $voucher_categories = $request->voucher_categories;
            $minimum_amount = $request->minimum_amount;
            $maximum_amount = $request->maximum_amount;
            $uses_total = $request->uses_total;
            $uses_customer = $request->uses_customer;
            $voucher_categories_ids = '';
            $voucher_products_ids = '';
            if ($voucher_categories && !empty($voucher_categories)) {
                foreach ($voucher_categories as $key => $val) {
                    $voucher_categories_ids .= $val;
                    if (count($voucher_categories) != $key - 1) {
                        $voucher_categories_ids .= ',';
                    }
                }
            }
            $voucher_products = $request->voucher_products;
            if ($voucher_products && !empty($voucher_products)) {
                foreach ($voucher_products as $key => $val) {
                    $voucher_products_ids .= $val;
                    if (count($voucher_products) != $key - 1) {
                        $voucher_products_ids .= ',';
                    }
                }
            }
            $status = $request->status;
            $loginId = Auth::user()->id;
            $checkExists = Voucher::where('voucher_name', $voucher_name)->count();
            if ($checkExists > 0) {
                Helper::log('Voucher store : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/voucher.voucher_exists')]);
            } else {
                $voucherFolder = $this->createDirectory('voucher');
                $voucher_icon = '';
                if ($file = $request->file('voucher_banner')) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move("$voucherFolder/", $fileName);
                    chmod($voucherFolder . '/' . $fileName, 0777);
                    $voucher_icon = 'uploads/voucher/' . $fileName;
                }
                $insertVoucher = [
                    'uuid' => Helper::getUuid(),
                    'voucher_name' => trim($voucher_name),
                    'voucher_code' => $voucher_code,
                    'voucher_banner' => $voucher_icon,
                    'voucher_discount_type' => $voucher_discount_type,
                    'voucher_discount' => $voucher_discount,
                    'minimum_amount' => $minimum_amount,
                    'maximum_amount' => $maximum_amount,
                    'uses_total' => $uses_total,
                    'uses_customer' => $uses_customer,
                    'voucher_applicable_from' => date('Y-m-d', strtotime($applicable_from)),
                    'voucher_applicable_to' => date('Y-m-d', strtotime($applicable_to)),
                    'voucher_categories' => $voucher_categories_ids,
                    'voucher_products' => $voucher_products_ids,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];
                $voucherData = Voucher::create($insertVoucher);
                DB::commit();
                Helper::saveLogAction('1', 'Voucher', 'Store', 'Add new Voucher ' . $voucherData->uuid, Auth::user()->id);

                Helper::log('Voucher store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Voucher store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Voucher', 'Store', 'Add new Voucher Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_save_information')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('edit_voucher');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $voucherData = Voucher::where('uuid', $id)->first();
        $voucher_applicable_from = date('Y-m-d',strtotime($voucherData->voucher_applicable_from));
        $voucher_applicable_to = date('Y-m-d',strtotime($voucherData->voucher_applicable_to));
        $voucherData['voucher_applicable_from'] = date('d-m-Y',strtotime($voucherData->voucher_applicable_from));
        $voucherData['voucher_applicable_to'] = date('d-m-Y',strtotime($voucherData->voucher_applicable_to));
        $categoryList = Category::where('status', 1)->get();
        $productList = Product::where('status', 1)->get();
        if (empty($voucherData)) {
            Helper::log('Voucher edit : No record found');
            return redirect()->back()->with('error', trans('backend/common.oops'));
        }
        return view('backend.voucher.edit', compact('voucherData', 'categoryList', 'productList','voucher_applicable_from','voucher_applicable_to'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Voucher update : start');
        try {
            $loginId = Auth::user()->id;
            $voucher_name = $request->voucher_name;
            $voucher_code = $request->voucher_code;
            $voucher_discount_type = $request->voucher_discount_type;
            $voucher_discount = $request->voucher_discount;
            $applicable_from = $request->voucher_applicable_from;
            $applicable_to = $request->voucher_applicable_to;
            $voucher_categories = $request->voucher_categories;
            $voucher_products = $request->voucher_products;
            $minimum_amount = $request->minimum_amount;
            $maximum_amount = $request->maximum_amount;
            $uses_total = $request->uses_total;
            $uses_customer = $request->uses_customer;
            $voucher_id = $request->voucher_id;
            $status = $request->status;
            $checkExists = Voucher::where('voucher_name', $voucher_name)->where('voucher_id', '!=', $voucher_id)->count();
            if ($checkExists > 0) {
                Helper::log('Voucher update : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/voucher.voucher_exists')]);
            } else {
                $updateData = [
                    'voucher_name' => trim($voucher_name),
                    'voucher_code' => $voucher_code,
                    'voucher_discount_type' => $voucher_discount_type,
                    'voucher_discount' => $voucher_discount,
                    'minimum_amount' => $minimum_amount,
                    'maximum_amount' => $maximum_amount,
                    'uses_total' => $uses_total,
                    'uses_customer' => $uses_customer,
                    'voucher_applicable_from' => date('Y-m-d', strtotime($applicable_from)),
                    'voucher_applicable_to' => date('Y-m-d', strtotime($applicable_to)),
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];
                if ($voucher_categories && !empty($voucher_categories)) {
                    $voucher_categories_ids = '';
                    foreach ($voucher_categories as $key => $val) {
                        $voucher_categories_ids .= $val;
                        if (count($voucher_categories) != ++$key) {
                            $voucher_categories_ids .= ',';
                        }
                    }
                    $updateData['voucher_categories'] = $voucher_categories_ids;
                }
                $voucher_products = $request->voucher_products;
                if ($voucher_products && !empty($voucher_products)) {
                    $voucher_products_ids = '';
                    foreach ($voucher_products as $key => $val) {
                        $voucher_products_ids .= $val;
                        if (count($voucher_products) != $key + 1) {
                            $voucher_products_ids .= ',';
                        }
                    }
                    $updateData['voucher_products'] = $voucher_products_ids;
                }
                $voucherFolder = $this->createDirectory('voucher');
                if ($file = $request->file('voucher_banner')) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move($voucherFolder, $fileName);
                    $myIcon = $voucherFolder . $fileName;
                    chmod($myIcon, 0777);
                    $updateData['voucher_banner'] = "uploads/voucher/$fileName";
                }
                Voucher::where('uuid', $id)->update($updateData);
                DB::commit();
                Helper::saveLogAction('1', 'Voucher', 'Update', 'Update Voucher ' . $id, Auth::user()->id);

                Helper::log('Voucher update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Voucher update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Voucher', 'Update', 'Update Voucher Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_voucher');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.voucher.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Languages::setBackLang();

        DB::beginTransaction();
        Helper::log('voucher delete : start');
        try {
            $voucherData = Voucher::where('uuid', $id)->first();
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Voucher::where('uuid', $id)->update($deleteData);
            DB::commit();
            Helper::saveLogAction('1', 'Voucher', 'Destroy', 'Destroy Voucher ' . $id, Auth::user()->id);

            Helper::log('voucher delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('voucher delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Voucher', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);

            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
