<?php

namespace App\Http\Controllers\Admin;

use App\Models\Box;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Branch;
use App\Models\ProductBranch;
use App\Models\ProductStoreInventory;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Models\ProductStoreInventoryLog;
use App\Models\Rac;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductStoreInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_product_inventory');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $userData = Auth::user();
        if ($userData->role == 1) {
            $inventoryList = ProductStoreInventory::leftjoin('product', 'product.product_id', 'product_store_inventory.product_id')
                ->leftjoin('branch', 'branch.branch_id', 'product_store_inventory.branch_id')
				->where('product_store_inventory.status','!=',2)
                ->select('product_store_inventory.*', 'product.name AS product_name', 'branch.name AS branch_name')
                ->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();

            $inventoryList = ProductStoreInventory::leftjoin('product', 'product.product_id', 'product_store_inventory.product_id')
                ->leftjoin('branch', 'branch.branch_id', 'product_store_inventory.branch_id')
				->where('product_store_inventory.status','!=',2)
                ->select('product_store_inventory.*', 'product.name AS product_name', 'branch.name AS branch_name')
                ->whereIn('product_store_inventory.branch_id', $branchIds)
                ->get();
        }
        return view('backend.product_inventory.index', compact('inventoryList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_product_inventory');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $productList = Product::where('has_inventory', 1)
                ->where('status', 1)
                ->select('product_id', 'uuid', 'name', 'has_rac_managemant')
                ->get();
            $branchList = Branch::where('status', 1)
                ->select('branch_id', 'uuid', 'name')
                ->get();
            $racList = Rac::where('status', 1)
                ->select('rac_id', 'uuid', 'name', 'slug')
                ->get();
        } else {

            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();

            $productList = Product::leftjoin('product_branch', 'product_branch.product_id', '=', 'product.product_id')
                ->whereIn('product_branch.branch_id', $branchIds)
                ->where('has_inventory', 1)
                ->where('product.status', 1)
                ->select('product.product_id', 'product.uuid', 'product.name', 'product.has_rac_managemant')
                ->get();
            $branchList = Branch::where('status', 1)
                ->whereIn('branch_id', $branchIds)
                ->select('branch_id', 'uuid', 'name')
                ->get();
            $racList = Rac::where('status', 1)
                ->whereIn('branch_id', $branchIds)
                ->select('rac_id', 'uuid', 'name', 'slug')
                ->get();
        }

        return view('backend.product_inventory.create', compact('productList', 'racList'));
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
        Helper::log('Product Inventory create : start');
        try {
            $product_id = $request->product_id;
            $branch_id = $request->branch_id;
            $qty = $request->qty;
            $warning_stock_level = $request->warning_stock_level;
            $status = $request->status;
            $hac_rac_product = $request->has_rac_product;
            $rac_id = $request->rac_id;
            $box_id = $request->box_id;

            $checkProduct = ProductStoreInventory::where(['product_id' => $product_id, 'branch_id' => $branch_id])->count();

            if ($checkProduct > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/inventory.product_exists')]);
                /*$productInventory = ProductStoreInventory::where(['product_id' => $product_id, 'branch_id' => $branch_id])->first();
                $updateInventoryData = [
                    'qty' => $qty,
                    'status' => $status,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => Auth::user()->id,
                ];

                if ($hac_rac_product) {
                    $updateInventoryData['rac_id'] = $rac_id;
                    $updateInventoryData['box_id'] = $box_id;
                }

                ProductStoreInventory::where(['product_id'=>$product_id,'branch_id'=>$branch_id])->update($updateInventoryData);
                $inventory_id = $productInventory->inventory_id;

                $inventoryLogData = [
                    'uuid' => Helper::getUuid(),
                    'inventory_id' => $inventory_id,
                    'branch_id' => $branch_id,
                    'product_id' => $product_id,
                    'employe_id' => Auth::user()->id,
                    'qty' => $qty,
                    'qty_before_change' => $productInventory->qty,
                    'qty_after_change' => $qty,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => Auth::user()->id,
                ];

                ProductStoreInventoryLog::create($inventoryLogData);*/
            } else {

                $inventoryData = [
                    'uuid' => Helper::getUuid(),
                    'product_id' => $product_id,
                    'branch_id' => $branch_id,
                    'qty' => $qty,
                    'warningStockLevel' => 0,//$warning_stock_level,
                    'status' => $status,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => Auth::user()->id,
                ];

                if ($hac_rac_product) {
                    $inventoryData['rac_id'] = $rac_id;
                    $inventoryData['box_id'] = $box_id;
                }

                $productInventory = ProductStoreInventory::create($inventoryData);
                $inventory_id = $productInventory->inventory_id;

                $inventoryLogData = [
                    'uuid' => Helper::getUuid(),
                    'inventory_id' => $inventory_id,
                    'branch_id' => $branch_id,
                    'product_id' => $product_id,
                    'employe_id' => Auth::user()->id,
                    'qty' => $qty,
                    'qty_before_change' => 0,
                    'qty_after_change' => $qty,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => Auth::user()->id,
                ];

                ProductStoreInventoryLog::create($inventoryLogData);

                Helper::saveLogAction('1', 'Inventory', 'Store', 'Add new Inventory ' . $productInventory->uuid, Auth::user()->id);
                Helper::log('Product Inventory create : finish');
                DB::commit();
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }


        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Product Inventory create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Inventory', 'Store', 'Add new Inventory ' . $exception->getMessage(), Auth::user()->id);
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
        $checkPermission = Permissions::checkActionPermission('edit_product_inventory');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $inventoryData = ProductStoreInventory::where('uuid', $uuid)->first();
        $productId = $inventoryData->product_id;

        $productList = Product::where('has_inventory', 1)
            ->where('status', 1)
            ->select('product_id', 'uuid', 'name')
            ->get();

        $productData = Product::where('product_id', $productId)->first();
        $branchIds = ProductBranch::where('product_id', $productId)->select('branch_id')->get();

        $branchList = Branch::whereIn('branch_id', $branchIds)->where('status', 1)->select('branch_id', 'uuid', 'name', 'slug')->get();

        return view('backend.product_inventory.edit', compact('inventoryData', 'productList', 'branchList'));
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
        Helper::log('Product Inventory update : start');
        try {
            $product_id = $request->product_id;
            $branch_id = $request->branch_id;
            $qty = $request->qty;
            $warning_stock_level = $request->warning_stock_level;
            $status = $request->status;
            $hac_rac_product = $request->has_rac_product;
            $rac_id = $request->rac_id;
            $box_id = $request->box_id;

            $checkProduct = ProductStoreInventory::where(['product_id' => $product_id, 'branch_id' => $branch_id])->where('uuid', '!=', $uuid)->count();

            if ($checkProduct > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/inventory.product_exists')]);
            } else {

                $productInventory = ProductStoreInventory::where('uuid', '!=', $uuid)->first();

                $inventoryData = [
                    'product_id' => $product_id,
                    'branch_id' => $branch_id,
                    'qty' => $qty,
                    'warningStockLevel' => 0,//$warning_stock_level,
                    'status' => $status,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => Auth::user()->id,
                ];

                if ($hac_rac_product) {
                    $updateInventoryData['rac_id'] = $rac_id;
                    $updateInventoryData['box_id'] = $box_id;
                }

                ProductStoreInventory::where('uuid', $uuid)->update($inventoryData);

                $inventory_id = $productInventory->inventory_id;

                $inventoryLogData = [
                    'uuid' => Helper::getUuid(),
                    'inventory_id' => $inventory_id,
                    'branch_id' => $branch_id,
                    'product_id' => $product_id,
                    'employe_id' => Auth::user()->id,
                    'qty' => $qty,
                    'qty_before_change' => $productInventory->qty,
                    'qty_after_change' => $qty,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => Auth::user()->id,
                ];

                ProductStoreInventoryLog::create($inventoryLogData);

                DB::commit();

                Helper::saveLogAction('1', 'Inventory', 'Update', 'Update Inventory ' . $uuid, Auth::user()->id);
                Helper::log('Product Inventory update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Product Inventory update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Inventory', 'Update', 'Update Inventory ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }
	
	public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_product_inventory');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $productData = ProductStoreInventory::where('uuid', $uuid)->first();

        return view('backend.product_inventory.delete', compact('uuid'));
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
        Helper::log('Product Inventory delete : start');
        try {
            $productData = ProductStoreInventory::where('uuid', $uuid)->first();
            if ($productData) {
                $productId = $productData->inventory_id;
                ProductStoreInventory::where('uuid', $uuid)->update(['status'=>2,'updated_at' => config('constants.date_time'),'updated_by' => Auth::user()->id]);
            }
            Helper::saveLogAction('1', 'Product Inventory', 'Destroy', 'Destroy Product Inventory' . $productId, Auth::user()->id);
            DB::commit();
            Helper::log('Product Inventory delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Product Inventory delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Product Inventory', 'Destroy', 'Delete Inventory Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    /**
     * Get Product Branch
     */
    public function getProductBranch($productId)
    {
        Helper::log('Get Product Branch : Start');

        $productData = Product::where('product_id', $productId)->first();
        $branchIds = ProductBranch::where('product_id', $productId)->select('branch_id')->get();

        $branchList = Branch::whereIn('branch_id', $branchIds)->where('status', 1)->select('branch_id', 'uuid', 'name', 'slug')->get();
        return response()->json(['status' => 200, 'list' => $branchList, 'has_rac' => $productData->has_rac_managemant]);
    }

    /**
     * Get Branch Rac
     */
    public function getBranchRac($branchId)
    {
        Helper::log('Get Branch Rac : Start');

        $racList = Rac::where('branch_id', $branchId)->where('status', 1)->select('rac_id', 'uuid', 'name', 'slug')->get();
        return response()->json(['status' => 200, 'list' => $racList]);
    }

    /**
     * Get Branch Rac Boxes
     */
    public function getBranchRacBox($racId)
    {
        Helper::log('Get Branch Rac Boxes : Start');

        $boxList = Box::where('rac_id', $racId)->where('status', 1)->select('box_id', 'uuid', 'name', 'slug')->get();
        return response()->json(['status' => 200, 'list' => $boxList]);
    }
}
