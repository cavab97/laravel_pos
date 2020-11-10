<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cities;
use App\Models\Languages;
use App\Models\Helper;
use App\Models\Permissions;
use App\Models\States;
use App\Models\Countries;
use Illuminate\Support\Facades\DB;

class StatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('view_states');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $selectedItems = ['state.*', 'country.name as country_nm'];
        $stateList = States::select($selectedItems)
            ->leftjoin('country', 'state.country_id', 'country.country_id')
            ->where('state.country_id', 132)
            ->orderBy('state_id', 'DESC')
            ->get();

        return view('backend.state.index', compact('stateList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_states');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $countries = Countries::where('country_id', 132)->select('name', 'country_id')->first();
        return view('backend.state.create', compact('countries'));
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
        Helper::log('State store : start');
        try {
            $name = $request->name;
            $slug = Helper::slugify($request->name);
            $country_id = $request->country_id;

            $checkName = States::where('name', $name)->count();
            if ($checkName > 0) {
                Helper::log('State store : name is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/state.name_exists')]);
            } else {
                $insertData = [
                    'name' => $name,
                    'slug' => $slug,
                    'country_id' => $country_id,
                    'updated_at' => config('constants.date_time')
                ];
                States::create($insertData);
                DB::commit();
                Helper::log('State store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('State store : exception');
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
        $checkPermission = Permissions::checkActionPermission('edit_states');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $stateData = States::where('state_id', $id)->first();
        $countries = Countries::where('country_id', 132)->select('name', 'country_id')->first();
        return view('backend.state.edit', compact('stateData', 'countries'));
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
        Helper::log('State update : start');
        try {

            $name = $request->name;
            $slug = Helper::slugify($request->name);
            $country_id = $request->country_id;

            $checkName = States::where('name', $name)->where('state_id', '!=', $id)->count();
            if ($checkName > 0) {
                Helper::log('State store : name is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/state.name_exists')]);
            } else {
                $updateData = [
                    'name' => $name,
                    'slug' => $slug,
                    'country_id' => $country_id,
                ];
                States::where('state_id', $id)->update($updateData);
                DB::commit();
                Helper::log('State update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('State update : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    public function delete($id)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_states');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $stateData = States::where('state_id', $id)->first();
        $cityCount = Cities::where('state_id', $stateData->state_id)->count();

        return view('backend.state.delete', compact('id', 'cityCount'));
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
        Helper::log('State delete : start');
        try {

            States::where('state_id', $id)->delete();

            DB::commit();
            Helper::log('State delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('State delete : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    public function stateByCountry($countryId)
    {
        $stateList = States::listByCountry($countryId);

        return response()->json(['status' => 200, 'list' => $stateList]);
    }
}
