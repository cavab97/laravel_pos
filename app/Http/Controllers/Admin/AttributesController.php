<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attributes;
use App\Models\CategoryAttribute;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttributesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_attributes');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $attributesList = Attributes::leftjoin('category_attribute', 'category_attribute.ca_id', '=', 'attributes.ca_id')
            ->select('attributes.*', 'category_attribute.name as ca_name')
            ->get();
        return view('backend.attributes.index', compact('attributesList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('Add_attributes');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $catAttr = CategoryAttribute::where('status', 1)->get();
        return view('backend.attributes.create', compact('catAttr'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Helper::log('Attributes create : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $ca_id = $request->ca_id;
            $is_default = 0;
            if ($request->is_default != '') {
                $is_default = 1;
            }
            $checkName = Attributes::where('name', $name)->where('ca_id', $ca_id)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/attributes.name_exists')]);
            } else {

                $attributesData = [
                    'uuid' => Helper::getUuid(),
                    'ca_id' => $ca_id,
                    'name' => $name,
                    'is_default' => $is_default,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                $attData = Attributes::create($attributesData);

                Helper::saveLogAction('1', 'Attributes', 'Store', 'Add new attributes ' . $attData->uuid, Auth::user()->id);
                Helper::log('Attributes create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Attributes create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Attributes', 'Store', 'Add new attributes ' . $exception->getMessage(), Auth::user()->id);
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
        $checkPermission = Permissions::checkActionPermission('edit_attributes');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $catAttr = CategoryAttribute::where('status', 1)->get();

        $attributesData = Attributes::where('uuid', $uuid)->first();

        return view('backend.attributes.edit', compact('attributesData', 'catAttr'));
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
        Helper::log('Attributes update : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $ca_id = $request->ca_id;

            $is_default = 0;
            if ($request->is_default != '') {
                $is_default = 1;
            }
            $checkName = Attributes::where('name', $name)->where('ca_id', $ca_id)->where('uuid', '!=', $uuid)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/attributes.name_exists')]);
            } else {

                $attributesData = [
                    'ca_id' => $ca_id,
                    'name' => $name,
                    'is_default' => $is_default,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                Attributes::where('uuid', $uuid)->update($attributesData);

                Helper::saveLogAction('1', 'Attributes', 'Update', 'Update attributes ' . $uuid, Auth::user()->id);
                Helper::log('Attributes update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Attributes update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Attributes', 'Update', 'Update Attributes ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_attributes');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.attributes.delete', compact('uuid'));
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
        Helper::log('Attributes delete : start');
        try {
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Attributes::where('uuid', $uuid)->update($deleteData);

            Helper::saveLogAction('1', 'Attributes', 'Destroy', 'Destroy Attributes ' . $uuid, Auth::user()->id);
            Helper::log('Attributes delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('Attributes delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Attributes', 'Destroy', 'Destroy Attributes ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
