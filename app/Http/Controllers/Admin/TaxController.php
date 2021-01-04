<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchTax;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Tax;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_tax');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        if (Auth::user()->role != 1) {
            $branchIds = UserBranch::where('user_id', Auth::id())->pluck('branch_id');
            $taxIds = BranchTax::whereIn('branch_id', $branchIds)->where([ 'status' => 1])->pluck('tax_id');
        }
        $taxList = Tax::where('status', '!=', 2);
        if (isset($taxIds)) {
            $taxList = $taxList->whereIn('tax_id', $taxIds);
        }
        $taxList = $taxList->get();

        if(!empty($taxList)){
            foreach($taxList as $key => $value){
                $branchData = BranchTax::where('tax_id', $value->tax_id)->get();
                if(!empty($branchData)){
                    $i = 0;
                    $branch_name = '';
                    foreach ($branchData as $bkey => $bvalue){
                        $branch = Branch::where('branch_id',$bvalue->branch_id)->select('name')->first();
                        $name = $branch->name;
                        $branch_name .= $name;
                        if (count($branchData) != ($i + 1)) {
                            $branch_name .= ',';
                        }
                        $i++;
                    }
                    $taxList[$key]['branch_name'] = $branch_name;
                }
            }
        }
        return view('backend.tax.index', compact('taxList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('Add_tax');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $hasPermission = Auth::user()->role == 1;
        $branchList = [];
        $userId = Auth::id();
        if (!$hasPermission && $userId > 0) {
            $branchIds = UserBranch::where('user_id', $userId)->pluck('branch_id');
            if (count($branchIds) > 0) {
                $branchList = Branch::whereIn('branch_id', $branchIds)->select('name', 'branch_id')->get();
                $hasPermission = true;
            }
         } else {
            $branchList = Branch::select('name', 'branch_id')->get();
         }
        return view('backend.tax.create', compact('hasPermission', 'branchList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Helper::log('Tax create : start');
        try {
            $code = $request->code;
            $description = $request->description;
            $rate = $request->rate;
            $status = $request->status;
            $branch_id = $request->branch_id;

            dd($branch_id);
            $is_fixed = 0;
            if ($request->is_fixed != '') {
                $is_fixed = 1;
            }
            $checkCode = Tax::where('code', $code)->count();

            if ($checkCode > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/tax.tax_exists')]);
            } else {

                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'code' => $code,
                    'description' => $description,
                    'rate' => $rate,
                    'status' => $status,
                    'is_fixed' => $is_fixed,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                $tax = Tax::create($insertData);
                Helper::saveLogAction('1', 'Tax', 'Store', 'Add new Tax ' . $tax->uuid, Auth::id());
                Helper::log('Tax create : finish');
                if (!empty($branch_id)) {
                    foreach ($branch_id as $key => $value) {
                        $insertBranchTax = [
                            'tax_id' => $tax->tax_id,
                            'branch_id' => $value,
                            'rate' => $rate,
                            'status' => $status,
                            'updated_at' => config('constants.date_time'),
                            'updated_by' => Auth::id(),
                        ];
                        BranchTax::create($insertBranchTax);
                    }
                }
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Tax create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Tax', 'Exception', 'Create Tax exception :' . $exception->getMessage(), Auth::id());
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
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
    public function edit($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('edit_tax');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $hasPermission = Auth::user()->role == 1;
        $userId = Auth::id();
        $branchList = [];
        $selectedBranchList = [];
        $taxData = Tax::where('uuid', $uuid)->first();
        if (!$hasPermission && $userId > 0) {
            $branchIds = UserBranch::where('user_id', $userId)->pluck('branch_id');
            $selectedBranchList = BranchTax::whereIn('branch_id', $branchIds)->where([ 'status' => 1, 'tax_id' => $taxData->tax_id ])->pluck('branch_id')->toArray();
            $branchList = Branch::whereIn('branch_id', $branchIds)->select('name', 'branch_id')->get();
            if (count($branchList) > 0) {
                $hasPermission = BranchTax::whereIn('branch_id', $branchIds)->where([ 'status' => 1, 'tax_id' => $taxData->tax_id ])->exists();
            }

        } else {
            $branchList = Branch::select('name', 'branch_id')->get();
            $selectedBranchList = BranchTax::where([ 'status' => 1, 'tax_id' => $taxData->tax_id ])->pluck('branch_id')->toArray();
        }
        return view('backend.tax.edit', compact('taxData', 'branchList', 'hasPermission', 'selectedBranchList'));
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
        Helper::log('Tax update : start');
        try {
            $code = $request->code;
            $description = $request->description;
            $rate = $request->rate;
            $status = $request->status;
            $branch_id = $request->branch_id;

            $is_fixed = 0;
            if ($request->is_fixed != '') {
                $is_fixed = 1;
            }
            $checkTax = Tax::where('code', $code)->where('uuid', '!=', $uuid)->count();

            if ($checkTax > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/tax.tax_exists')]);
            } else {

                $taxData = [
                    'code' => $code,
                    'description' => $description,
                    'rate' => $rate,
                    'is_fixed' => $is_fixed,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                $tax = Tax::where('uuid', $uuid)->first();
                $tax->update($taxData);
                if (!empty($branch_id)) {
                    $updateObj = [
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id(),
                    ];
                    $currentExistIds = BranchTax::withTrashed()->where([ 'tax_id' => $tax->tax_id, 'status' => 1])->pluck('branch_id')->toArray();
                    //$branchData->whereNotNull('deleted_at')->whereIn('branch_id', $branch_id)->restore();
                    BranchTax::withTrashed()->where([ 'tax_id' => $tax->tax_id, 'status' => 1])->whereNotNull('deleted_at')->whereIn('branch_id', $branch_id)->restore();
                    BranchTax::withTrashed()->where([ 'tax_id' => $tax->tax_id, 'status' => 1])->whereNotNull('deleted_at')->whereIn('branch_id', $branch_id)->update($updateObj);
                    BranchTax::where([ 'tax_id' => $tax->tax_id, 'status' => 1])->whereNotIn('branch_id', $branch_id)->delete();
                    BranchTax::where([ 'tax_id' => $tax->tax_id, 'status' => 1])->whereNotIn('branch_id', $branch_id)->update($updateObj);
                    $newBranch = array_diff($branch_id, $currentExistIds);
                    if (isset($newBranch) && !empty($newBranch)) {
                        foreach ($newBranch as $key => $value) {
                            $insertBranchTax = [
                                'tax_id' => $tax->tax_id,
                                'branch_id' => $value,
                                'rate' => $rate,
                                'status' => $status,
                                'updated_at' => config('constants.date_time'),
                                'updated_by' => Auth::id(),
                            ];
                            BranchTax::create($insertBranchTax);
                        }
                    }
                } else {
                    BranchTax::where([ 'tax_id' => $tax->tax_id, 'status' => 1])->delete();
                }
                Helper::saveLogAction('1', 'Tax', 'Update', 'Update Tax' . $uuid, Auth::user()->id);
                Helper::log('Tax update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Tax update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Tax', 'Exception', 'Update Tax exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_tax');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $taxData = Tax::where('uuid',$uuid)->first();
        $taxId = $taxData->tax_id;
        $branchTaxCount = BranchTax::where('tax_id',$taxId)->count();

        return view('backend.tax.delete', compact('uuid','branchTaxCount'));
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
        Helper::log('Tax delete : start');
        try {
            $deleteData = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Tax::where('uuid', $uuid)->update($deleteData);
            Helper::saveLogAction('1', 'Tax', 'Destroy', 'Destroy Tax' . $uuid, Auth::user()->id);

            Helper::log('Tax delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('Tax delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Tax', 'Destroy', 'Delete Tax exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
