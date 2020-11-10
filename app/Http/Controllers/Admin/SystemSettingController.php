<?php

namespace App\Http\Controllers\Admin;

use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SystemSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_setting');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $settingList = SystemSetting::all();

        return view('backend.setting.index',compact('settingList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_setting');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.setting.create');
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
        Helper::log('Setting store : start');
        DB::beginTransaction();
        try {
            $namespace = $request->namespace;
            $display_name = $request->display_name;
            $key = $request->key;
            
            $type = $request->type;
			if($type != 4){
                $value = $request->value;
            } else {
                $value = $request->booleanvalue;
            }

            $checkExists = SystemSetting::where('namespace', $namespace)->where('key', $key)->count();
            if ($checkExists > 0) {
                Helper::log('Setting namespace : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/setting.namespace_exists')]);
            } else {
                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'display_name' => $display_name,
                    'type' => $type,
                    'namespace' => $namespace,
                    'key' => $key,
                    'value' => $value,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => Auth::user()->id
                ];
                SystemSetting::create($insertData);

                DB::commit();
                Helper::log('Setting store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Setting  store : exception');
            Helper::log($exception);
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
        $checkPermission = Permissions::checkActionPermission('edit_setting ');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $settingData = SystemSetting::where('uuid', $uuid)->first();
        return view('backend.setting.edit', compact('settingData'));
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
        Helper::log('Setting update : start');
        try {
            $display_name = $request->display_name;
            $namespace = $request->namespace;
            $key = $request->key;
            
            $type = $request->type;
			if($type != 4){
                $value = $request->value;
            } else {
                $value = $request->booleanvalue;
            }

            $checkExists = SystemSetting::where('namespace', $namespace)->where('key', $key)->where('uuid', '!=', $uuid)->count();
            if ($checkExists > 0) {
                Helper::log('Setting namespace : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/setting.namespace_exists')]);
            } else {

                $updateData = [
                    'display_name' => $display_name,
                    'namespace' => $namespace,
                    'key' => $key,
                    'value' => $value,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => Auth::user()->id
                ];

                SystemSetting::where('uuid', $uuid)->update($updateData);
                DB::commit();
                Helper::log('Setting update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Setting update : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
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
