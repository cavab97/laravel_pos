<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Rac;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RacController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_rac');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $defaultCondition = 'rac.uuid != ""';

        $userData = Auth::user();
        if ($userData->role == 1) {
            $racList = Rac::leftjoin('branch', 'branch.branch_id', 'rac.branch_id')
                ->select('rac.*', 'branch.name AS branch_name')
                ->where('rac.status', '!=', 2)
                ->whereRaw($defaultCondition)
                ->orderBy('rac_id', 'DESC')
                ->get()->toArray();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $racList = Rac::leftjoin('branch', 'branch.branch_id', 'rac.branch_id')
                ->select('rac.*', 'branch.name AS branch_name')
                ->whereIn('rac.branch_id', $branchIds)
                ->where('rac.status', '!=', 2)
                ->whereRaw($defaultCondition)
                ->groupBy('rac_id')
                ->orderBy('rac_id', 'DESC')
                ->get()->toArray();
        }
        return view('backend.rac.index', compact('racList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('Add_rac');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
        }

        return view('backend.rac.create', compact('branchList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Helper::log('Rac create : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $branch_id = $request->branch_id;

            $checkName = Rac::where('name', $name)->where('branch_id', $branch_id)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/rac.name_exists')]);
            } else {

                $RacData = [
                    'uuid' => Helper::getUuid(),
                    'branch_id' => $branch_id,
                    'name' => $name,
                    'slug' => Helper::slugify($name),
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                $racData = Rac::create($RacData);

                Helper::saveLogAction('1', 'Rac', 'Store', 'Add new Rac ' . $racData->uuid, Auth::user()->id);
                Helper::log('Rac create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Rac create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Rac', 'Store', 'Add new Rac ' . $exception->getMessage(), Auth::user()->id);
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
        $checkPermission = Permissions::checkActionPermission('edit_rac');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
        }

        $racData = Rac::where('uuid', $uuid)->first();

        return view('backend.rac.edit', compact('racData', 'branchList'));
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

        Helper::log('Rac update : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $branch_id = $request->branch_id;

            $checkName = Rac::where('name', $name)->where('branch_id', $branch_id)->where('uuid', '!=', $uuid)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/rac.name_exists')]);
            } else {

                $racData = [
                    'branch_id' => $branch_id,
                    'name' => $name,
                    'slug' => Helper::slugify($name),
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                Rac::where('uuid', $uuid)->update($racData);

                Helper::saveLogAction('1', 'Rac', 'Update', 'Update Rac ' . $uuid, Auth::user()->id);
                Helper::log('Rac update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Rac update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Rac', 'Update', 'Update Rac ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_rac');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.rac.delete', compact('uuid'));
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
        Helper::log('Rac delete : start');
        try {
            $deleteData = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Rac::where('uuid', $uuid)->update($deleteData);

            Helper::saveLogAction('1', 'Rac', 'Destroy', 'Destroy Rac ' . $uuid, Auth::user()->id);
            Helper::log('Rac delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('Rac delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Rac', 'Destroy', 'Destroy Rac ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
