<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assets;
use App\Models\Attributes;
use App\Models\Branch;
use App\Models\CategoryAttribute;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\SetMeal;
use App\Models\SetmealAttribute;
use App\Models\SetMealBranch;
use App\Models\SetMealProduct;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetMealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_setmeal');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $setmealList = SetMeal::leftjoin('setmeal_product', 'setmeal_product.setmeal_id', 'setmeal.setmeal_id')
                ->where('setmeal.status','!=',2)
                ->select('setmeal.*')->groupBy('setmeal.setmeal_id')->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $setmealBranch = SetMealBranch::whereIn('branch_id', $branchIds)->where('status', 1)->select('setmeal_id')->get();
            $setmealList = SetMeal::leftjoin('setmeal_product', 'setmeal_product.setmeal_id', 'setmeal.setmeal_id')
                ->whereIn('setmeal.setmeal_id', $setmealBranch)
                ->where('setmeal.status','!=',2)
                ->select('setmeal.*')
                ->groupBy('setmeal.setmeal_id')
                ->get();
        }

        return view('backend.setmeal.index', compact('setmealList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_setmeal');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
            $productList = Product::where(['has_setmeal' => 1,'status' => 1])->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get()->toArray();
            $productIds = ProductBranch::whereIn('branch_id', $branchIds)->select("product_id")->get();
            $productList = Product::where(['has_setmeal' => 1,'status' => 1])->whereIn('product_id', $productIds)->get();
        }

        return view('backend.setmeal.create', compact('branchList', 'productList'));
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
        Helper::log('Setmeal store : start');
        try {
            $setmealName = $request->name;
            $setmealPrice = $request->price;
            $setmealStatus = $request->status;

            $loginId = Auth::user()->id;
            $checkExists = SetMeal::where('name', $setmealName)->count();
            if ($checkExists > 0) {
                Helper::log('Setmeal store : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/setmeal.name_exists')]);
            } else {

                $insertSetmeal = [
                    'uuid' => Helper::getUuid(),
                    'name' => trim($setmealName),
                    'price' => $setmealPrice,
                    'status' => $request->status,
                    'created_at' => config('constants.date_time'),
                    'created_by' => $loginId,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => $loginId,
                ];
                $setmealData = SetMeal::create($insertSetmeal);
                $setmealId = $setmealData->setmeal_id;

                /*insert Image Data*/
                if ($file = $request->file('setmeal_image')) {
                    $folder = $this->createDirectory('setmeal');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move($folder, $fileName);
                    chmod($folder . $fileName, 0777);
                    $image = 'uploads/setmeal/' . $fileName;
                    $imageData = [
                        'uuid' => Helper::getUuid(),
                        'asset_type' => 2,
                        'asset_type_id' => $setmealId,
                        'asset_path' => $image,
                        'updated_at' => config('constants.date_time'),
                        'updated_by' => Auth::user()->id
                    ];
                    Assets::create($imageData);

                }

                /*insert product Data*/
                if ($request->product_id) {
                    foreach ($request->product_id as $modkey => $modvalue) {
                        $insertProdData = [
                            'setmeal_id' => $setmealId,
                            'product_id' => $modvalue,
                            'quantity' => $request->prod_qty[$modkey],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        SetMealProduct::create($insertProdData);
                    }
                }

                $branch_id = $request->branch_id;
                if (isset($branch_id) && !empty($branch_id)) {
                    foreach ($branch_id as $key => $value) {
                        $insertCatBranch = [
                            'uuid' => Helper::getUuid(),
                            'setmeal_id' => $setmealId,
                            'branch_id' => $value,
                            'updated_by' => $loginId,
                            'updated_at' => config('constants.date_time'),
                        ];
                        SetMealBranch::create($insertCatBranch);
                    }
                }
            }
            Helper::saveLogAction('1', 'Setmeal', 'Store', 'Add new setmeal' . $setmealId, Auth::user()->id);
            DB::commit();
            Helper::log('Setmeal store : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Setmeal store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Setmeal', 'Add new Setmeal exception :' . $exception->getMessage(), Auth::user()->id);
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
    public function edit($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('edit_setmeal');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $setmealData = SetMeal::where('uuid', $uuid)->first();
        if (empty($setmealData)) {
            Helper::log('Setmeal edit : No record found');
            return redirect()->back()->with('error', trans('backend/common.oops'));
        }
        $setmealId = $setmealData->setmeal_id;
        $setmealData->setmeal_product = SetMealProduct::leftjoin('product', 'product.product_id', 'setmeal_product.product_id')
            ->where('setmeal_id', $setmealId)->where('setmeal_product.status', '!=', 2)
            ->select('setmeal_product.*', 'product.name')
            ->get();

        $setmealData->branchData = SetMealBranch::leftjoin('branch', 'branch.branch_id', 'setmeal_branch.branch_id')
            ->where('setmeal_branch.setmeal_id', $setmealId)
            ->where('setmeal_branch.status', '!=', 2)
            ->select('setmeal_branch.*', 'branch.name')
            ->get();
        $i = 0;
        $branchIds = '';
        foreach ($setmealData->branchData as $key => $value) {
            $branchIds .= $value->branch_id;
            if (count($setmealData->branchData) != ($i + 1)) {
                $branchIds .= ',';
            }
            $i++;
        }
        $setmealData->branch = $branchIds;

        $setmealImageData = Assets::where('asset_type', 2)->where('asset_type_id', $setmealId)->first();
        if(!empty($setmealImageData)) {
            $setmealData->setmeal_image = $setmealImageData->asset_path;
        } else {
            $setmealData->setmeal_image = '';
        }

        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
            $productList = Product::where(['has_setmeal' => 1,'status' => 1])->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get()->toArray();
            $productIds = ProductBranch::whereIn('branch_id', $branchIds)->select("product_id")->get();
            $productList = Product::where(['has_setmeal' => 1,'status' => 1])->whereIn('product_id', $productIds)->get();
        }

        return view('backend.setmeal.edit', compact('setmealData', 'productList', 'branchList'));
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
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Setmeal update : start');
        try {
            $loginId = Auth::user()->id;
            $setmealName = $request->name;
            $setmealPrice = $request->price;
            $setmealStatus = $request->status;

            $setmealId = $request->setmeal_id;

            $checkExists = Setmeal::where('name', $setmealName)->where('setmeal_id', '!=', $setmealId)->count();
            if ($checkExists > 0) {
                Helper::log('Setmeal update : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/common.name_exists')]);
            } else {
                $updateData = [
                    'name' => trim($setmealName),
                    'price' => $setmealPrice,
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];

                SetMeal::where('uuid', $uuid)->update($updateData);

                /*insert Image Data*/
                if ($file = $request->file('setmeal_image')) {
                    $folder = $this->createDirectory('setmeal');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move($folder, $fileName);
                    chmod($folder . $fileName, 0777);
                    $image = 'uploads/setmeal/' . $fileName;
                    $imageData = [
                        'uuid' => Helper::getUuid(),
                        'asset_type' => 2,
                        'asset_type_id' => $setmealId,
                        'asset_path' => $image,
                        'updated_at' => config('constants.date_time'),
                        'updated_by' => Auth::user()->id
                    ];
                    Assets::create($imageData);

                }

                $branch_id = $request->branch_id;


                $product_id = $request->product_id;
                $productSetmealData = SetMealProduct::where('setmeal_id', $setmealId)->get()->toArray();
                if (isset($productSetmealData) && !empty($productSetmealData)) {
                    $existProductArray = array();
                    foreach ($productSetmealData as $key => $val) {
                        array_push($existProductArray, $val['product_id']);
                    }
                    $is_exist = true;
                    if (isset($product_id) && !empty($product_id)) {
                        $newProduct = array_diff($product_id, $existProductArray);
                        $oldProductArray = array_diff($existProductArray, $product_id);
                        $updateProductArray = array_intersect($product_id, $existProductArray);
                        $removeProductArray = array_diff($oldProductArray, $product_id);
                        if (empty($oldProductArray) && empty($updateProductArray) && empty($oldProductArray)) {
                            $is_exist = true;
                        } else {
                            $is_exist = false;
                            /*New insert*/
                            if (isset($newProduct) && !empty($newProduct)) {
                                foreach ($newProduct as $key => $value) {
                                    if ($value) {
                                        $insertProSetmeal = [
                                            'product_id' => $value,
                                            'setmeal_id' => $setmealId,
                                            'quantity' => $request->prod_qty[$key],
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ];
                                        SetMealProduct::create($insertProSetmeal);
                                    }
                                }
                            }
                            /*status update*/
                            if (isset($updateProductArray) && !empty($updateProductArray)) {
                                foreach ($updateProductArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 1,
                                        'quantity' => $request->prod_qty[$key],
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ];

                                    SetMealProduct::where(['setmeal_id' => $setmealId, 'product_id' => $value])->update($updateObj);
                                }
                            }
                            if (isset($removeProductArray) && !empty($removeProductArray)) {
                                foreach ($removeProductArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ];

                                    SetMealProduct::where(['setmeal_id' => $setmealId, 'product_id' => $value])->update($updateObj);
                                }
                            }
                        }
                    }
                    if ($is_exist == true) {
                        if (isset($existProductArray) && !empty($existProductArray)) {
                            foreach ($existProductArray as $key => $value) {
                                $updateObj = [
                                    'status' => 2,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                SetMealProduct::where(['setmeal_id' => $setmealId, 'product_id' => $value])->update($updateObj);
                            }
                        }
                    }
                } else {
                    if (isset($product_id) && !empty($product_id)) {
                        foreach ($product_id as $key => $value) {
                            $insertProSetmeal = [
                                'product_id' => $value,
                                'setmeal_id' => $setmealId,
                                'quantity' => $request->prod_qty[$key],
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            SetMealProduct::create($insertProSetmeal);
                        }
                    }
                }

                /* update Branch */
                $setmealBranchData = SetMealBranch::where('setmeal_id', $setmealId)->get()->toArray();
                if (isset($setmealBranchData) && !empty($setmealBranchData)) {
                    $existBranchArray = array();
                    foreach ($setmealBranchData as $key => $val) {
                        array_push($existBranchArray, $val['branch_id']);
                    }
                    $is_exist = true;
                    if (isset($branch_id) && !empty($branch_id)) {
                        $newBranch = array_diff($branch_id, $existBranchArray);
                        $oldBranchArray = array_diff($existBranchArray, $branch_id);
                        $updateBranchArray = array_intersect($existBranchArray, $branch_id);
                        $removeBranchArray = array_diff($oldBranchArray, $branch_id);
                        if (empty($oldBranchArray) && empty($updateBranchArray) && empty($newBranch)) {
                            $is_exist = true;
                        } else {
                            $is_exist = false;
                            /*New insert*/
                            if (isset($newBranch) && !empty($newBranch)) {
                                foreach ($newBranch as $key => $value) {
                                    $insertCatBranch = [
                                        'uuid' => Helper::getUuid(),
                                        'setmeal_id' => $setmealId,
                                        'branch_id' => $value,
                                        'status' => 1,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];
                                    SetMealBranch::create($insertCatBranch);
                                }
                            }
                            /*status update*/
                            if (isset($updateBranchArray) && !empty($updateBranchArray)) {
                                foreach ($updateBranchArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 1,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];

                                    SetMealBranch::where(['branch_id' => $value, 'setmeal_id' => $setmealId])->update($updateObj);
                                }
                            }
                            if (isset($removeBranchArray) && !empty($removeBranchArray)) {
                                foreach ($removeBranchArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];

                                    SetMealBranch::where(['branch_id' => $value, 'setmeal_id' => $setmealId])->update($updateObj);
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
                                SetMealBranch::where(['branch_id' => $value, 'setmeal_id' => $setmealId])->update($updateObj);
                            }
                        }
                    }
                } else {
                    if (isset($branch_id) && !empty($branch_id)) {
                        foreach ($branch_id as $key => $value) {
                            $insertCatBranch = [
                                'uuid' => Helper::getUuid(),
                                'setmeal_id' => $setmealId,
                                'branch_id' => $value,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $loginId
                            ];
                            SetMealBranch::create($insertCatBranch);
                        }
                    }
                }
                Helper::saveLogAction('1', 'Setmeal', 'Update', 'Update Setmeal ' . $setmealId, Auth::user()->id);
                DB::commit();
                Helper::log('Setmeal update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            dd($exception->getMessage());
            DB::rollBack();
            Helper::log('Setmeal update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Setmeal', 'Update Setmeal exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_setmeal');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.setmeal.delete', compact('uuid'));
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
        Helper::log('setmeal delete : start');
        try {
            $deleteData = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            SetMeal::where('uuid', $uuid)->update($deleteData);

            Helper::saveLogAction('1', 'setmeal', 'Destroy', 'Destroy setmeal ' . $uuid, Auth::user()->id);
            Helper::log('setmeal delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('setmeal delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'setmeal', 'Destroy', 'Destroy setmeal ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    public function getProductAttribute($product_id)
    {
        Languages::setBackLang();
        Helper::log('setmeal product attribute : start');
        try {

            $attributeList = Attributes::leftjoin('product_attribute','product_attribute.attribute_id','attributes.attribute_id')
                ->where('product_attribute.product_id', $product_id)
                ->where('product_attribute.status', 1)
                ->get()->toArray();
            return response()->json(['status' => 200, 'data' => $attributeList]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('setmeal product attribute : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }
}
