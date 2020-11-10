<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Languages;
use App\Models\Helper;
use App\Models\Permissions;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;
use Illuminate\Support\Facades\DB;

class CitiesController extends Controller
{
    public function cityByState($stateId)
    {
        $cityList = Cities::listByState($stateId);
        return response()->json(['status' => 200, 'list' => $cityList]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_cities');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $selectedItems = ['city.*', 'state.name as state_nm'];
        $cityList = Cities::select($selectedItems)
            ->leftjoin('state', 'city.state_id', 'state.state_id')
            ->where('state.country_id', 132)
            ->orderBy('city_id', 'DESC')
            ->get();
        return view('backend.city.index', compact('cityList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_cities');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $countries = Countries::where('country_id', 132)->select('name', 'country_id')->first();
        $stateList = States::select('name', 'state_id')->where('country_id', 132)->orderBy('name', 'ASC')->get();
        return view('backend.city.create', compact('countries', 'stateList'));
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
        Helper::log('City store : start');
        try {
            $name = $request->name;
            $slug = Helper::slugify($request->name);
            $state_id = $request->state;

            $checkName = Cities::where('name', $name)->count();
            if ($checkName > 0) {
                Helper::log('City store : name is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/city.name_exists')]);
            } else {
                $insertData = [
                    'name' => $name,
                    'slug' => $slug,
                    'state_id' => $state_id,
                    'updated_at' => config('constants.date_time')
                ];
                Cities::create($insertData);
                DB::commit();
                Helper::log('City store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('City store : exception');
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
        $checkPermission = Permissions::checkActionPermission('edit_cities');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $cityData = Cities::where('city_id', $id)->first();
        $countries = Countries::where('country_id', 132)->select('name', 'country_id')->first();
        $stateList = States::select('name', 'state_id')->where('country_id', 132)->orderBy('name', 'ASC')->get();
        $stateData = States::where('state_id', $cityData->state_id)->first();
        $cityData->country = $stateData->country_id;
        $cityData->state = $cityData->state_id;
        return view('backend.city.edit', compact('cityData', 'countries', 'stateList'));
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
        Helper::log('City update : start');
        try {

            $name = $request->name;
            $slug = Helper::slugify($request->name);
            $state_id = $request->state;

            $checkName = Cities::where('name', $name)->where('city_id', '!=', $id)->count();
            if ($checkName > 0) {
                Helper::log('City store : name is exists');
                return response()->json(['status' => 409, 'message' => 'The city name is already exists!']);
            } else {
                $updateData = [
                    'name' => $name,
                    'slug' => $slug,
                    'state_id' => $state_id,
                ];
                Cities::where('city_id', $id)->update($updateData);
                DB::commit();
                Helper::log('City update : finish');
                return response()->json(['status' => 200, 'message' => 'This information has been updated!']);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('City update : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => 'This information has not been updated!']);
        }
    }

    public function delete($id)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_cities');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $cityData = Cities::where('city_id', $id)->first();

        return view('backend.city.delete', compact('id'));
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
        Helper::log('City delete : start');
        try {

            Cities::where('city_id', $id)->delete();

            DB::commit();
            Helper::log('City delete : finish');
            return response()->json(['status' => 200, 'message' => 'This information has been deleted!']);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('City delete : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => 'This information has not been deleted!']);
        }
    }
}
