<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Table;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facade;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_table');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $tableList = Table::select('table.*', DB::raw('(Select `name` From branch WHERE branch_id = `table`.branch_id) AS branch_name'))->get();
        return view('backend.table.index', compact('tableList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_table');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $branchList = Branch::where('status', 1)->get()->toArray();
        return view('backend.table.create', compact('branchList'));
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
        Helper::log('Table store : start');
        try {
            $loginId = Auth::user()->id;
            $name = trim($request->table_name);
            $branch_id = $request->branch_id;
            $table_type = $request->table_type;
            $table_qr = Helper::randomString(10);
            $table_capacity = $request->table_capacity;
            $table_service_charge = $request->table_service_charge;
            $table_section = $request->table_section;
            $checkExists = Table::where('table_name', $name)->count();
            if ($checkExists > 0) {
                Helper::log('Table store : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/table.table_exists')]);
            } else {
                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'table_name' => trim($name),
                    'branch_id' => $branch_id,
                    'table_type' => $table_type,
                    'table_qr' => $table_qr,
                    'table_capacity' => $table_capacity,					
                    'status' => $request->status,
                    'table_section' => $table_section,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];
				if($table_type == 1) {
                    $insertData['table_service_charge'] = $request->table_service_charge;
                } else {
                    $insertData['table_service_charge'] = NULL;
                }
                $tableData = Table::create($insertData);
                Helper::saveLogAction('1', 'Table', 'Store', 'Add new table ' . $tableData->uuid, Auth::user()->id);

                DB::commit();
                Helper::log('Table store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Table store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Table', 'Store', 'Add new table exception ' . $exception->getMessage(), Auth::user()->id);
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
        $checkPermission = Permissions::checkActionPermission('edit_table');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $tableData = Table::where('uuid', $id)->first();
        if (empty($tableData)) {
            Helper::log('Table edit : No record found');
            return redirect()->back()->with('error', trans('backend/common.oops'));
        }
        $branchList = Branch::where('status', 1)->get()->toArray();
        return view('backend.table.edit', compact('tableData', 'branchList'));
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
        Helper::log('Table update : start');
        try {
            $loginId = Auth::user()->id;
            $name = trim($request->table_name);
            $branch_id = $request->branch_id;
            $table_type = $request->table_type;
            $table_qr = $request->table_qr;
            $table_capacity = $request->table_capacity;
            $table_service_charge = $request->table_service_charge;
            $table_section = $request->table_section;
            $tableId = $request->table_id;
            $checkExists = Table::where('table_name', $name)->where('table_id', '!=', $tableId)->count();
            if ($checkExists > 0) {
                Helper::log('Table update : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/table.table_exists')]);
            } else {
                $updateData = [
                    'table_name' => trim($name),
                    'branch_id' => $branch_id,
                    'table_type' => $table_type,
                    'table_qr' => $table_qr,
                    'table_capacity' => $table_capacity,	
                    'table_section' => $table_section,				
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];
				if($table_type == 1) {
                    $updateData['table_service_charge'] = $request->table_service_charge;
                } else {
                    $updateData['table_service_charge'] = NULL;
                }
                Table::where('uuid', $id)->update($updateData);
                DB::commit();
                Helper::saveLogAction('1', 'Table', 'Update', 'Update table ' . $id, Auth::user()->id);
                Helper::log('Table update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Table update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Table', 'Update', 'Update table Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_table');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.table.delete', compact('uuid'));
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
        Helper::log('Table delete : start');
        try {
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Table::where('uuid', $id)->update($deleteData);
            DB::commit();
            Helper::saveLogAction('1', 'Table', 'delete', 'delete table ' . $id, Auth::user()->id);
            Helper::log('Table delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Table delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Table', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    public function generateQr($uuid)
    {
        DB::beginTransaction();
        Helper::log('Table Qr generate: start');
        try {
            $tableData = Table::where('uuid', $uuid)->first();
            $QrFolder = $this->createDirectory('qr_code');
            $qrcode = Facade::size(250)
                ->backgroundColor(255, 255, 204)
                ->generate('MyNotePaper');

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Table Qr generate : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/table.not_generate')]);
        }
    }
}
