<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Product;
use App\Models\Rac;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_box');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $defaultCondition = 'box.uuid != ""';

        $userData = Auth::user();
        if ($userData->role == 1) {
            $boxList = Box::leftjoin('branch', 'branch.branch_id', 'box.branch_id')
                ->leftjoin('rac', 'rac.rac_id', 'box.rac_id')
                ->select('box.*', 'branch.name AS branch_name', 'rac.name AS rac_name')
                ->where('box.status', '!=', 2)
                ->whereRaw($defaultCondition)
                ->orderBy('box_id', 'DESC')
                ->get()->toArray();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $boxList = Box::leftjoin('branch', 'branch.branch_id', 'box.branch_id')
                ->leftjoin('rac', 'rac.rac_id', 'box.rac_id')
                ->select('box.*', 'branch.name AS branch_name', 'rac.name AS rac_name')
                ->whereIn('box.branch_id', $branchIds)
                ->where('box.status', '!=', 2)
                ->whereRaw($defaultCondition)
                ->groupBy('rac_id')
                ->orderBy('box_id', 'DESC')
                ->get()->toArray();
        }
        return view('backend.box.index', compact('boxList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('Add_box');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->where('status', 1)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
        }

        return view('backend.box.create', compact('branchList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Helper::log('Box create : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $branch_id = $request->branch_id;
            $rac_id = $request->rac_id;
            $product_id = $request->product_id;
            $box_for = $request->box_for;
            $box_limit = $request->box_limit;
            $wine_qty = $request->wine_qty;

            $checkName = Box::where('name', $name)->where('branch_id', $branch_id)->where('rac_id', $rac_id)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/rac.box_name_exists')]);
            } else {

                $boxData = [
                    'uuid' => Helper::getUuid(),
                    'branch_id' => $branch_id,
                    'rac_id' => $rac_id,
                    'product_id' => $product_id,
                    'name' => $name,
                    'slug' => Helper::slugify($name),
                    'box_for' => $box_for,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                if($box_for == 1){
                    $boxData['wine_qty'] = $wine_qty;
                } else {
                    $boxData['box_limit'] = $box_limit;
                }

                $boxData = Box::create($boxData);

                Helper::saveLogAction('1', 'Box', 'Store', 'Add new Box ' . $boxData->uuid, Auth::user()->id);
                Helper::log('Box create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Box create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Box', 'Store', 'Add new Box ' . $exception->getMessage(), Auth::user()->id);
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
        $checkPermission = Permissions::checkActionPermission('edit_box');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->where('status',1)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
        }

        $boxData = Box::where('uuid', $uuid)->first();
        if ($boxData->branch_id) {
            $boxData->racList = Rac::where('branch_id', $boxData->branch_id)->orderBy('name', 'ASC')->get();
            $boxData->productList = Product::leftjoin('product_branch', 'product_branch.product_id', '=', 'product.product_id')
                ->select('product.*')
                ->where(['product.status'=>1,'product.has_rac_managemant'=>1])
                ->where('product_branch.branch_id', $boxData->branch_id)
                ->get();
        }

        return view('backend.box.edit', compact('boxData', 'branchList'));
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

        Helper::log('Box update : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $branch_id = $request->branch_id;
            $rac_id = $request->rac_id;
            $product_id = $request->product_id;
            $box_for = $request->box_for;
            $box_limit = $request->box_limit;
            $wine_qty = $request->wine_qty;

            $checkName = Box::where('name', $name)->where('branch_id', $branch_id)->where('rac_id', $rac_id)->where('uuid', '!=', $uuid)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/rac.box_name_exists')]);
            } else {

                $boxData = [
                    'branch_id' => $branch_id,
                    'rac_id' => $rac_id,
                    'product_id' => $product_id,
                    'name' => $name,
                    'slug' => Helper::slugify($name),
                    'box_for' => $box_for,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                if($box_for == 1){
                    $boxData['wine_qty'] = $wine_qty;
                } else {
                    $boxData['box_limit'] = $box_limit;
                }

                Box::where('uuid', $uuid)->update($boxData);

                Helper::saveLogAction('1', 'Box', 'Update', 'Update Box ' . $uuid, Auth::user()->id);
                Helper::log('Box update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Box update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Box', 'Update', 'Update Box ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_box');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.box.delete', compact('uuid'));
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
        Helper::log('Box delete : start');
        try {
            $deleteData = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Box::where('uuid', $uuid)->update($deleteData);

            Helper::saveLogAction('1', 'Box', 'Destroy', 'Destroy Box ' . $uuid, Auth::user()->id);
            Helper::log('Box delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('Box delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Box', 'Destroy', 'Destroy Box ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    public function racByBranch($branchId)
    {
        $userData = Auth::user();
        $racList = Rac::where('branch_id', $branchId)->orderBy('name', 'ASC')->get();
        if ($userData->role == 1) {
            $productList = Product::where(['status'=>1,'has_rac_managemant'=>1])->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->where('status', 1)->select("branch_id")->get();
            $productList = Product::leftjoin('product_branch', 'product_branch.product_id', '=', 'product.product_id')
                ->select('product.*')
                ->where(['product.status'=>1,'product.has_rac_managemant'=>1])
                ->whereIn('product_branch.branch_id', $branchIds)
                ->get();
        }

        return response()->json(['status' => 200, 'list' => $racList, 'productList' => $productList]);
    }
}
