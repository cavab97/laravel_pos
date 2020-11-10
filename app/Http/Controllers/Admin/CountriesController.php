<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Languages;
use Illuminate\Http\Request;
use App\Models\Helper;
use App\Models\Permissions;
use App\Models\Countries;
use Illuminate\Support\Facades\DB;

class CountriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('view_countries');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $countryList = Countries::orderBy('country_id', 'ASC')->where('country_id', 132)->get();
        return view('backend.country.index', compact('countryList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('add_countries');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.country.create');
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
        Helper::log('Country store : start');
        try {
            $sortname = $request->sortname;
            $name = $request->name;
            $slug = Helper::slugify($request->name);
            $phoneCode = $request->phoneCode;

            $checkName = Countries::where('name', $name)->count();
            $checkSortname = Countries::where('sortname', $sortname)->count();
            if ($checkName > 0) {
                Helper::log('Country store : name is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/country.name_exists')]);
            } else if ($checkSortname) {
                Helper::log('Country store : name is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/country.short_name_exists')]);
            } else {
                $insertData = [
                    'sortname' => $sortname,
                    'name' => $name,
                    'slug' => $slug,
                    'phoneCode' => $phoneCode,
                    'updated_at' => config('constants.date_time')
                ];
                Countries::create($insertData);
                DB::commit();
                Helper::log('Country store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('User store : exception');
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
    public function edit($id)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('edit_countries');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $countryData = Countries::where('country_id', $id)->first();
        return view('backend.country.edit', compact('countryData'));
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
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Country update : start');
        try {

            $sortname = $request->sortname;
            $name = $request->name;
            $slug = Helper::slugify($request->name);
            $phoneCode = $request->phoneCode;

            $checkName = Countries::where('name', $name)->where('country_id', '!=', $id)->count();
            $checkSortname = Countries::where('sortname', $sortname)->where('country_id', '!=', $id)->count();
            if ($checkName > 0) {
                Helper::log('Country store : name is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/country.name_exists')]);
            } else if ($checkSortname) {
                Helper::log('Country store : name is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/country.short_name_exists')]);
            } else {
                $updateData = [
                    'sortname' => $sortname,
                    'name' => $name,
                    'slug' => $slug,
                    'phoneCode' => $phoneCode,
                ];

                Countries::where('country_id', $id)->update($updateData);
                DB::commit();
                Helper::log('Country update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Country update : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    public function delete($id)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_countries');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.country.delete', compact('id'));
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
        Helper::log('Country delete : start');
        try {

            Countries::where('country_id', $id)->delete();

            DB::commit();
            Helper::log('Country delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('User delete : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
