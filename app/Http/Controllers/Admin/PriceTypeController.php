<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\PriceType;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $priceTypeList = PriceType::all();
        return view('backend.price_type.index', compact('priceTypeList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        return view('backend.price_type.create');
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
        Helper::log('Price type create : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $checkName = PriceType::where('name', $name)->count();
            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/price_type.name_exists')]);
            } else {
                $priceType = PriceType::create([
                    'uuid' => Helper::getUuid(),
                    'name' => $name,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ]);
                DB::commit();
                Helper::saveLogAction('1', 'Price-type', 'Store', 'Add new price type ' . $priceType->uuid, Auth::user()->id);

                Helper::log('Price type create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Price type create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Price type', 'Store price type exception :' . $exception->getMessage(), Auth::user()->id);

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
        $priceTypeData = PriceType::where('uuid', $uuid)->first();
        return view('backend.price_type.edit', compact('priceTypeData'));
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
        Helper::log('Price type update : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $checkName = PriceType::where('name', $name)->where('uuid', '!=', $uuid)->count();
            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/price_type.name_exists')]);
            } else {
                PriceType::where('uuid', $uuid)->update(
                    ['name' => $name,
                        'status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                DB::commit();
                Helper::saveLogAction('1', 'Price-type', 'Update', 'Update price type ' . $uuid, Auth::user()->id);
                Helper::log('Price type update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Price type update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Price type', 'Create price type exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_price_type');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $priceTypeData = PriceType::where('uuid', $uuid)->first();
        $productCount = Product::where('price_type_id', $priceTypeData->pt_id)->count();
        return view('backend.price_type.delete', compact('uuid','productCount'));
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
        Helper::log('PriceType delete : start');
        try {
            PriceType::where('uuid', $id)->first();
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2

            ];
            PriceType::where('uuid', $id)->update($deleteData);
            DB::commit();
            Helper::saveLogAction('1', 'Price-type', 'Destroy', 'Destroy price type' . $id, Auth::user()->id);
            Helper::log('PriceType delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('PriceType delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'PriceType', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
