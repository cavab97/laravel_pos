<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryAttribute;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryAttributesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_category_attribute');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $attributesList = CategoryAttribute::where('status', '!=', 2)->get();
        return view('backend.category_attribute.index', compact('attributesList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('Add_category_attribute');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.category_attribute.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Helper::log('Category Attributes create : start');
        try {
            $name = trim($request->name);
            $status = $request->status;

            $checkName = CategoryAttribute::where('name', $name)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/attributes.name_exists')]);
            } else {

                $attributesData = [
                    'uuid' => Helper::getUuid(),
                    'name' => $name,
                    'slug' => Helper::slugify($name),
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                $catData = CategoryAttribute::create($attributesData);

                Helper::saveLogAction('1', 'CategoryAttribute', 'Store', 'Add new category attribute ' . $catData->uuid, Auth::user()->id);
                Helper::log('Category Attributes create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Category Attributes create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'CategoryAttribute', 'Store', 'Add new category attribute ' . $exception->getMessage(), Auth::user()->id);
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
        $checkPermission = Permissions::checkActionPermission('edit_category_attribute');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $attributesData = CategoryAttribute::where('uuid', $uuid)->first();

        return view('backend.category_attribute.edit', compact('attributesData'));
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
        Helper::log('Category Attributes update : start');
        try {
            $name = trim($request->name);
            $status = $request->status;

            $checkName = CategoryAttribute::where('name', $name)->where('uuid', '!=', $uuid)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/attributes.name_exists')]);
            } else {

                $attributesData = [
                    'name' => $name,
                    'slug' => Helper::slugify($name),
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                CategoryAttribute::where('uuid', $uuid)->update($attributesData);

                Helper::saveLogAction('1', 'CategoryAttribute', 'Update', 'Update Category Attribute ' . $uuid, Auth::user()->id);
                Helper::log('Category Attributes update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Category Attributes update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'CategoryAttribute', 'Update', 'Update Category Attribute ' . $exception->getMessage(), Auth::user()->id);
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

        return view('backend.category_attribute.delete', compact('uuid'));
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
        Helper::log('Category Attributes delete : start');
        try {
            $deleteData = [
                'status' => 2
            ];
            CategoryAttribute::where('uuid', $uuid)->update($deleteData);

            Helper::saveLogAction('1', 'CategoryAttribute', 'Destroy', 'Destroy CategoryAttribute ' . $uuid, Auth::user()->id);
            Helper::log('Category Attributes delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('Category Attributes delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'CategoryAttribute', 'Destroy', 'Destroy CategoryAttribute ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
