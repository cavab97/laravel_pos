<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Kitchen;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_kitchen');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $kitchenList = Kitchen::select('kitchen_department.*', DB::raw('(Select `name` From branch WHERE branch_id = `kitchen_department`.branch_id) AS branch_name'), DB::raw('(Select `printer_name` From printer WHERE printer_id = `kitchen_department`.kitchen_printer_id) AS printer_name'))->get();
        return view('backend.kitchen.index', compact('kitchenList'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('Add_kitchen');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $printerList = Printer::where('status', 1)->get();
        $branchList = Branch::where('status', 1)->get();
        return view('backend.kitchen.create', compact('branchList', 'printerList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Helper::log('Kitchen create : start');
        try {
            $kitchen_name = trim($request->kitchen_name);
            $branch_id = $request->branch_id;
            $printer_id = $request->kitchen_printer_id;
            $status = $request->status;
            $checkName = Kitchen::where('kitchen_name', $kitchen_name)->count();
            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/kitchen.kitchen_name_exists')]);
            } else {

                $printerData = [
                    'uuid' => Helper::getUuid(),
                    'branch_id' => $branch_id,
                    'kitchen_name' => $kitchen_name,
                    'kitchen_printer_id' => $printer_id,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                $kitchenData = Kitchen::create($printerData);
                Helper::saveLogAction('1', 'Kitchen', 'Store', 'Add new kitchen ' . $kitchenData->uuid, Auth::user()->id);

                Helper::log('Kitchen create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Kitchen create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Kitchen', 'Create kitchen exception :' . $exception->getMessage(), Auth::user()->id);
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
    public function edit($id)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('edit_kitchen');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $kitchenData = Kitchen::where('uuid', $id)->first();
        $branchList = Branch::where('status', 1)->get();
        $printerList = Printer::where('branch_id', $kitchenData->branch_id)->get();
        return view('backend.kitchen.edit', compact('kitchenData', 'printerList', 'branchList'));
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
        Helper::log('Kitchen create : start');
        try {
            $name = trim($request->kitchen_name);
            $branch_id = $request->branch_id;
            $printer_id = $request->kitchen_printer_id;
            $status = $request->status;
            $checkName = Kitchen::where('kitchen_name', $name)->where('uuid', '!=', $id)->count();
            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/printer.printer_name_exists')]);
            } else {

                $updateData = [
                    'branch_id' => $branch_id,
                    'kitchen_name' => $name,
                    'kitchen_printer_id' => $printer_id,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                Kitchen::where('uuid', $id)->update($updateData);
                Helper::saveLogAction('1', 'Kitchen', 'Update', 'Update Kitchen ' . $id, Auth::user()->id);

                Helper::log('Kitchen create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Kitchen create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Kitchen', 'Update kitchen exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_kitchen');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.kitchen.delete', compact('uuid'));
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
        Helper::log('Kitchen delete : start');
        try {
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Kitchen::where('uuid', $id)->update($deleteData);
            Helper::saveLogAction('1', 'Kitchen', 'Destroy', 'Destroy Kitchen ' . $id, Auth::user()->id);

            Helper::log('Kitchen delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('Kitchen delete : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    public function getPrinter(Request $request, $branchId)
    {
        try {
            $printerData = Printer::where('branch_id', $branchId)->get();
            return response()->json(['status' => 200, 'data' => $printerData]);
        } catch (\Exception $exception) {
            Helper::log($exception);
            Helper::saveLogAction('1', 'Kitchen', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
