<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableColor;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TableColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_table_color');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $tableColorList = TableColor::where('status','!=',2)->get();
        return view('backend.table_color.index', compact('tableColorList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_table_color');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.table_color.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Table Color store : start');
        try {
            $loginId = Auth::user()->id;
            $time_minute = $request->time_minute;
            $color_code = $request->color_code;

            $checkExists = TableColor::where('time_minute', $time_minute)->count();
            $checkColorExists = TableColor::where('color_code', $color_code)->count();
            if ($checkExists > 0) {
                Helper::log('Table Color store : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/table_color.table_color_time')]);
            } else if ($checkColorExists > 0) {
                Helper::log('Table Color store : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/table_color.table_color_exists')]);
            } else {
                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'time_minute' => $time_minute,
                    'color_code' => $color_code,
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];

                $tableData = TableColor::create($insertData);
                Helper::saveLogAction('1', 'Table Color', 'Store', 'Add new table color ' . $tableData->uuid, Auth::user()->id);

                DB::commit();
                Helper::log('Table Color store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Table Color store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Table', 'Store', 'Add new table exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_save_information')]);
        }
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
    public function edit($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('edit_table_color ');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $tableColorData = TableColor::where('uuid', $uuid)->first();
        return view('backend.table_color.edit', compact('tableColorData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Table Color update : start');
        try {
            $loginId = Auth::user()->id;
            $time_minute = $request->time_minute;
            $color_code = $request->color_code;
            $tableColorId = $request->id;
            $checkExists = TableColor::where('time_minute', $time_minute)->where('id', '!=', $tableColorId)->count();
            $checkColorExists = TableColor::where('color_code', $color_code)->where('id', '!=', $tableColorId)->count();
            if ($checkExists > 0) {
                Helper::log('Table Color update : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/table_color.table_color_time')]);
            } else if ($checkColorExists > 0) {
                Helper::log('Table Color update : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/table_color.table_color_exists')]);
            } else {
                $updateData = [
                    'time_minute' => $time_minute,
                    'color_code' => $color_code,
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];

                TableColor::where('uuid', $uuid)->update($updateData);
                DB::commit();
                Helper::saveLogAction('1', 'Table Color', 'Update', 'Update table Color' . $uuid, Auth::user()->id);
                Helper::log('Table Color update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Table Color update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Table', 'Update', 'Update table Color Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_table_color');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.table_color.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Languages::setBackLang();

        DB::beginTransaction();
        Helper::log('Table Color delete : start');
        try {
            $deleteData = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            TableColor::where('uuid', $id)->update($deleteData);
            DB::commit();
            Helper::saveLogAction('1', 'Table Color', 'delete', 'delete table Color ' . $id, Auth::user()->id);
            Helper::log('Table Color delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Table Color delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Table Color', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
