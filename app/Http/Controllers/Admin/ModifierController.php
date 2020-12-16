<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Modifier;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModifierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_modifier');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $modifierList = Modifier::all();
        return view('backend.modifier.index', compact('modifierList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('Add_modifier');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.modifier.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Helper::log('Modifier create : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $is_default = 0;
            if ($request->is_default != '') {
                $is_default = 1;
            }
            $is_global = 0;
            if ($request->is_global != '') {
                $is_global = 1;
            }
            $checkName = Modifier::where('name', $name)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/modifier.name_exists')]);
            } else {

                $modifierData = [
                    'uuid' => Helper::getUuid(),
                    'name' => $name,
                    'is_default' => $is_default,
                    'is_global' => $is_global,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                $modifierData = Modifier::create($modifierData);

                Helper::saveLogAction('1', 'Modifier', 'Store', 'Add new Modifier ' . $modifierData->uuid, Auth::user()->id);
                Helper::log('Modifier create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Modifier create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Modifier', 'Store', 'Add new Modifier ' . $exception->getMessage(), Auth::user()->id);
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
        $checkPermission = Permissions::checkActionPermission('edit_modifier');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $modifierData = Modifier::where('uuid', $uuid)->first();

        return view('backend.modifier.edit', compact('modifierData'));
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
        Helper::log('Modifier create : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $is_default = 0;
            if ($request->is_default != '') {
                $is_default = 1;
            }
            $is_global = 0;
            if ($request->is_global != '') {
                $is_global = 1;
            }
            $checkName = Modifier::where('name', $name)->where('uuid', '!=', $uuid)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/modifier.name_exists')]);
            } else {

                $modifierData = [
                    'name' => $name,
                    'is_default' => $is_default,
                    'is_global' => $is_global,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                Modifier::where('uuid', $uuid)->update($modifierData);

                Helper::saveLogAction('1', 'Modifier', 'Update', 'Update Modifier ' . $uuid, Auth::user()->id);
                Helper::log('Modifier create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Modifier create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Modifier', 'Update', 'Update Modifier ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_modifier');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.modifier.delete', compact('uuid'));
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
        Helper::log('Modifier delete : start');
        try {
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Modifier::where('uuid', $uuid)->update($deleteData);

            Helper::saveLogAction('1', 'Modifier', 'Destroy', 'Destroy Modifier ' . $uuid, Auth::user()->id);
            Helper::log('Modifier delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('Modifier delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Modifier', 'Destroy', 'Destroy Modifier ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
