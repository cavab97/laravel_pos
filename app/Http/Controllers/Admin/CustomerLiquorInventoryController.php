<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerLiquorInventory;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerLiquorInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_wine_store_management');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $userData = Auth::user();
        if ($userData->role == 1) {
            $inventoryList = CustomerLiquorInventory::leftjoin('product', 'product.product_id', 'customer_liquor_inventory.cl_product_id')
                ->leftjoin('branch', 'branch.branch_id', 'customer_liquor_inventory.cl_branch_id')
                ->leftjoin('customer', 'customer.customer_id', 'customer_liquor_inventory.cl_customer_id')
                ->select('customer_liquor_inventory.*', 'product.name AS product_name', 'branch.name AS branch_name', 'customer.name AS customer_name')
                ->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();

            $inventoryList = CustomerLiquorInventory::leftjoin('product', 'product.product_id', 'customer_liquor_inventory.cl_product_id')
                ->leftjoin('branch', 'branch.branch_id', 'customer_liquor_inventory.cl_branch_id')
                ->leftjoin('customer', 'customer.customer_id', 'customer_liquor_inventory.cl_customer_id')
                ->select('customer_liquor_inventory.*', 'product.name AS product_name', 'branch.name AS branch_name', 'customer.name AS customer_name')
                ->whereIn('customer_liquor_inventory.cl_branch_id', $branchIds)
                ->get();
        }
        return view('backend.customer_liquor_inventory.index', compact('inventoryList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
