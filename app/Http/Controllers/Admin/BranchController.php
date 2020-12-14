<?php

namespace App\Http\Controllers\Admin;

use App\Models\BranchTax;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Branch;
use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_branch');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.branch.index');
    }

    /**
     * Pagination for backend branchs
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

            $userData = Auth::user();

            $defaultCondition = 'uuid != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $defaultCondition .= " AND ( name LIKE '%$search%' OR email LIKE '%'$search%' OR contact_no LIKE '%'$search%' ) ";
            }

            $name = $request->input('name', null);
            if ($name != null) {
                $name = Helper::string_sanitize($name);
                $defaultCondition .= " AND `name` LIKE '%$name%'";
            }

            $contact_no = $request->input('contact_no', null);
            if ($contact_no != null) {
                $defaultCondition .= " AND `contact_no` LIKE '%$contact_no%' ";
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
                $defaultCondition .= " AND DATE_FORMAT(`branch`.updated_at, '%Y-%m-%d') <= '" . $to . "'";
            }
            if (!empty($from) && empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`branch`.updated_at, '%Y-%m-%d') >= '" . $from . "'";
            }
            if (!empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`branch`.updated_at, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
            }

            if ($userData->role != 1) {
                $branchIds = UserBranch::where('user_id', $userData->id)->where('status',1)->select("branch_id")->get();
                if(!empty($branchIds)){
                    $ids = [];
                    foreach ($branchIds as $value){
                        array_push($ids, $value->branch_id);
                    }
                    if(count($ids) > 0){
                        $implodeIds = implode(',',$ids);
                    } else {
                        $implodeIds = '';
                    }
                    $defaultCondition .= " AND branch_id IN ('$implodeIds')";
                }
            }

            $branchCount = Branch::whereRaw($defaultCondition)
                ->count();
            $branchList = Branch::whereRaw($defaultCondition)
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get();

            foreach ($branchList as $k => $v) {
                $branchList[$k]->open_from = date('H:i', strtotime($v->open_from));
                $branchList[$k]->closed_on = date('H:i', strtotime($v->closed_on));
            }
            return response()->json([
                "aaData" => $branchList,
                "iTotalDisplayRecords" => $branchCount,
                "iTotalRecords" => $branchCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('Branch pagination exception');
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
        $checkPermission = Permissions::checkActionPermission('add_branch');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $taxList = Tax::where('status', 1)->get();
        return view('backend.branch.create', compact('taxList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        Helper::log('branch create : start');
        try {
            $name = trim($request->name);
            $contact_no = $request->contact_no;
            $email = $request->email;
            $contact_person = $request->contact_person;
            $open_from = $request->open_from;
            $closed_on = $request->closed_on;
            //$tax = $request->tax;
            $branch_banner = $request->branch_banner;
            $address = $request->address;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $status = $request->status;
            $order_prefix = $request->order_prefix;
            $invoice_start = $request->invoice_start;
			$service_charge = $request->service_charge;


            $checkName = Branch::where('name', $name)->count();
            //$checkEmail = Branch::where('email', $email)->count();
            //$checkContact = Branch::where('contact_no', $contact_no)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/branch.branch_name_exists')]);
            } else {

                $branchData = [
                    'uuid' => Helper::getUuid(),
                    'name' => $name,
                    'slug' => Helper::slugify(trim($name)),
                    'address' => $address,
                    'contact_no' => $contact_no,
                    'email' => $email,
                    'contact_person' => $contact_person,
                    'open_from' => date('H:i:s', strtotime($open_from)),
                    'closed_on' => date('H:i:s', strtotime($closed_on)),
                    'service_charge' => $service_charge,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'status' => $status,
                    'invoice_start' => $invoice_start,
                    'order_prefix' => strtoupper($order_prefix),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                if ($file = $request->file('branch_banner')) {
                    $categoryFolder = $this->createDirectory('branch_banner');
                    $file = $request->file('branch_banner');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move("$categoryFolder/", $fileName);
                    chmod($categoryFolder . '/' . $fileName, 0777);
                    $branch_banner = 'uploads/branch_banner/' . $fileName;
                    $branchData['branch_banner'] = $branch_banner;
                }

                $branData = Branch::create($branchData);

                $branchId = $branData->branch_id;
                $taxId = $request->tax_id;
                if (isset($taxId) && !empty($taxId)) {
                    foreach ($taxId as $key => $value) {
                        $rateData = Tax::where('tax_id', $value)->select('rate')->first();
                        $insertTax = [
                            'tax_id' => $value,
                            'branch_id' => $branchId,
                            'rate' => $rateData['rate'],
                            'updated_by' => Auth::user()->id
                        ];
                        BranchTax::create($insertTax);
                    }
                }
                Helper::saveLogAction('1', 'Branch', 'Store', 'Add new branch ' . $branData->uuid, Auth::user()->id);
                DB::commit();
                Helper::log('Branch create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Branch create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Branch', 'Store', 'Add new branch Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('view_branch');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $branchData = Branch::where('uuid', $uuid)
            ->select('branch.*',
                DB::raw('(SELECT name FROM users WHERE id = branch.updated_by) AS updated_name'))
            ->first();
		$branchTax = BranchTax::leftjoin('tax','tax.tax_id','branch_tax.tax_id')->where('branch_tax.branch_id',$branchData->branch_id)->get();
        $branchData->branch_tax = $branchTax;
        $language_id = Languages::getBackLanguageId();
        if (!empty($branchData)) {
            return view('backend.branch.view', compact('branchData'));
        } else {
            return redirect()->route('admin.branch.index')->with('error', trans('backend/common.oops'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('edit_branch');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $branchData = Branch::where('uuid', $uuid)->first();
        $branchTaxData = BranchTax::where('branch_id', $branchData->branch_id)->select('branch_id', 'tax_id', 'status')->get();
        $branchData->taxData = $branchTaxData;

        $taxList = Tax::where('status', 1)->get();

        return view('backend.branch.edit', compact('branchData', 'taxList'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        DB::beginTransaction();
        Helper::log('branch create : start');
        try {
            $name = trim($request->name);
            $contact_no = $request->contact_no;
            $email = $request->email;
            $contact_person = $request->contact_person;
            $open_from = $request->open_from;
            $closed_on = $request->closed_on;
            //$tax = $request->tax;
            $branch_banner = $request->branch_banner;
            $address = $request->address;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $status = $request->status;
            $order_prefix = $request->order_prefix;
            $invoice_start = $request->invoice_start;
			$service_charge = $request->service_charge;

            $checkName = Branch::where('name', $name)->where('uuid', '!=', $uuid)->count();
            //$checkEmail = Branch::where('email', $email)->where('uuid', '!=', $uuid)->count();
            //$checkContact = Branch::where('contact_no', $contact_no)->where('uuid', '!=', $uuid)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/branch.branch_name_exists')]);
            } else {

                $branchData = [
                    'name' => $name,
                    'slug' => Helper::slugify(trim($name)),
                    'address' => $address,
                    'contact_no' => $contact_no,
                    'email' => $email,
                    'contact_person' => $contact_person,
                    'open_from' => date('H:i:s', strtotime($open_from)),
                    'closed_on' => date('H:i:s', strtotime($closed_on)),
                    'service_charge' => $service_charge,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'status' => $status,
                    'order_prefix' => strtoupper($order_prefix),
                    'invoice_start' => $invoice_start,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                if ($file = $request->file('branch_banner')) {
                    $categoryFolder = $this->createDirectory('branch_banner');
                    $file = $request->file('branch_banner');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move("$categoryFolder/", $fileName);
                    chmod($categoryFolder . '/' . $fileName, 0777);
                    $branch_banner = 'uploads/branch_banner/' . $fileName;
                    $branchData['branch_banner'] = $branch_banner;
                }

                Branch::where('uuid', $uuid)->update($branchData);
                $loginId = Auth::user()->id;
                $tax_id = $request->tax_id;
                $branchId = $request->branch_id;

                $BranchTaxData = BranchTax::where('branch_id', $branchId)->get()->toArray();
                if (isset($BranchTaxData) && !empty($BranchTaxData)) {
                    $existBranchArray = array();
                    foreach ($BranchTaxData as $key => $val) {
                        array_push($existBranchArray, $val['tax_id']);
                    }
                    $is_exist = true;
                    if (isset($tax_id) && !empty($tax_id)) {
                        $newTax = array_diff($tax_id, $existBranchArray);
                        $oldBranchArray = array_diff($existBranchArray, $tax_id);
                        $updateBranchTaxArray = array_intersect($existBranchArray, $tax_id);
                        $removeBranchArray = array_diff($oldBranchArray, $tax_id);
                        if (empty($oldBranchArray) && empty($updateBranchTaxArray) && empty($newTax)) {
                            $is_exist = true;
                        } else {
                            $is_exist = false;
                            /*New insert*/
                            if (isset($newTax) && !empty($newTax)) {
                                foreach ($newTax as $key => $value) {
                                    $rateData = Tax::where('tax_id', $value)->select('rate')->first();

                                    $insertBranchTaxTax = [
                                        'branch_id' => $branchId,
                                        'tax_id' => $value,
                                        'rate' => $rateData->rate,
                                        'status' => 1,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];
                                    $this->insertBranchTax($insertBranchTaxTax);
                                }
                            }
                            /*status update*/
                            if (isset($updateBranchTaxArray) && !empty($updateBranchTaxArray)) {
                                foreach ($updateBranchTaxArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 1,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];

                                    BranchTax::where(['tax_id' => $value, 'branch_id' => $branchId])->update($updateObj);
                                }
                            }
                            if (isset($removeBranchArray) && !empty($removeBranchArray)) {
                                foreach ($removeBranchArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];

                                    BranchTax::where(['tax_id' => $value, 'branch_id' => $branchId])->update($updateObj);
                                }
                            }
                        }
                    }

                    if ($is_exist == true) {
                        if (isset($existBranchArray) && !empty($existBranchArray)) {
                            foreach ($existBranchArray as $key => $value) {
                                $updateObj = [
                                    'status' => 2,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => $loginId
                                ];
                                BranchTax::where(['tax_id' => $value, 'branch_id' => $branchId])->update($updateObj);
                            }
                        }
                    }
                } else {
                    if (isset($tax_id) && !empty($tax_id)) {
                        foreach ($tax_id as $key => $value) {
                            $rateData = Tax::where('tax_id', $value)->select('rate')->first();

                            $insertBranchTaxTax = [
                                'branch_id' => $branchId,
                                'tax_id' => $value,
                                'rate' => $rateData->rate,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $loginId
                            ];
                            $this->insertBranchTax($insertBranchTaxTax);
                        }
                    }
                }

                Helper::saveLogAction('1', 'Branch', 'Update', 'Update Branch ' . $uuid, Auth::user()->id);
                DB::commit();
                Helper::log('Branch create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Branch create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Branch', 'Update', 'Update Branch Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_category');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $branchData = Branch::where('uuid', $uuid)->first();

        return view('backend.branch.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        Languages::setBackLang();

        DB::beginTransaction();
        Helper::log('Branch delete : start');
        try {
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Branch::where('uuid', $uuid)->update($deleteData);

            DB::commit();
            Helper::saveLogAction('1', 'Branch', 'Destroy', 'Destroy Branch ' . $uuid, Auth::user()->id);
            Helper::log('Branch delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Branch delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Branch', 'Destroy', 'Destroy Branch Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    public function insertBranchTax($object)
    {
        BranchTax::create($object);
    }

    public function updateBranchTax($object)
    {
        BranchTax::create($object);
    }
}
