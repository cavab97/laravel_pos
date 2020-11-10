<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerBranch;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_banner');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $bannerList = Banner::all();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $bannerList = Banner::leftjoin('banner_branch', 'banner_branch.banner_id', '=', 'banner.banner_id')
                ->whereIn('banner_branch.branch_id', $branchIds)
                ->select('banner.*')
                ->groupBy('banner_id')
                ->get();
        }

        return view('backend.banner.index', compact('bannerList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_banner');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get()->toArray();
        }
        return view('backend.banner.create', compact('branchList'));
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
        Helper::log('Banner store : start');
        try {
            $title = $request->title;
            $description = $request->description;
            $loginId = Auth::user()->id;
            $checkExists = Banner::where('title', $title)->count();
            if ($checkExists > 0) {
                Helper::log('Banner store : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/banner.title_exists')]);
            } else {

                $bannerFolder = $this->createDirectory('banner');
                $banner_for_mobile = '';
                if ($file = $request->file('banner_for_mobile')) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . 'm' . '.' . $extension;
                    $file->move("$bannerFolder/", $fileName);
                    chmod($bannerFolder . '/' . $fileName, 0777);
                    $banner_for_mobile = 'uploads/banner/' . $fileName;
                }
                $banner_for_web = '';
                if ($file = $request->file('banner_for_web')) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . 'w' . '.' . $extension;
                    $file->move("$bannerFolder/", $fileName);
                    chmod($bannerFolder . '/' . $fileName, 0777);
                    $banner_for_web = 'uploads/banner/' . $fileName;
                }
                $insertbanner = [
                    'uuid' => Helper::getUuid(),
                    'title' => trim($title),
                    'banner_for_mobile' => $banner_for_mobile,
                    'banner_for_web' => $banner_for_web,
                    'description' => $description,
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];
                $bannerData = Banner::create($insertbanner);
                $bannerId = $bannerData->banner_id;
                $branch_id = $request->branch_id;
                if (isset($branch_id) && !empty($branch_id)) {
                    foreach ($branch_id as $key => $value) {
                        $insertBanBranch = [
                            'uuid' => Helper::getUuid(),
                            'banner_id' => $bannerId,
                            'branch_id' => $value,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $loginId
                        ];
                        BannerBranch::create($insertBanBranch);
                    }
                }

                Helper::saveLogAction('1', 'Banner', 'Store', 'Add new banner ' . $bannerData->uuid, Auth::user()->id);
                DB::commit();
                Helper::log('Banner store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Banner store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Banner', 'Store', 'Store Exception ' . $exception->getMessage(), Auth::user()->id);
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
    public function edit($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('edit_banner');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get()->toArray();
        }
        $bannerData = Banner::where('uuid', $uuid)->first();
        if (empty($bannerData)) {
            Helper::log('banner edit : No record found');
            return redirect()->back()->with('error', trans('backend/common.oops'));
        }
        $bannerId = $bannerData->banner_id;
        /*get selected branch */
        $bannerBranchData = BannerBranch::where('banner_id', $bannerId)->select('branch_id', 'status')->get();
        $bannerData->branchData = $bannerBranchData;

        return view('backend.banner.edit', compact('bannerData','branchList'));
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
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Banner update : start');
        try {
            $loginId = Auth::user()->id;
            $title = $request->title;
            $description = $request->description;
            $banner_id = $request->banner_id;

            $checkExists = Banner::where('title', $title)->where('banner_id', '!=', $banner_id)->count();
            if ($checkExists > 0) {
                Helper::log('Banner update : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/banner.title_exists')]);
            } else {
                $updateData = [
                    'title' => trim($title),
                    'description' => $description,
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];
                $bannerFolder = $this->createDirectory('banner');
                if ($file = $request->file('banner_for_mobile')) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . 'm' . '.' . $extension;
                    $file->move($bannerFolder, $fileName);
                    $myIcon = $bannerFolder . $fileName;
                    chmod($myIcon, 0777);
                    $updateData['banner_for_mobile'] = "uploads/banner/$fileName";
                }
                if ($file = $request->file('banner_for_web')) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . 'w' . '.' . $extension;
                    $file->move($bannerFolder, $fileName);
                    $myIcon = $bannerFolder . $fileName;
                    chmod($myIcon, 0777);
                    $updateData['banner_for_web'] = "uploads/banner/$fileName";
                }
                Banner::where('uuid', $uuid)->update($updateData);

                $branch_id = $request->branch_id;
                $bannerId = $request->banner_id;

                $bannerBranchData = BannerBranch::where('banner_id', $bannerId)->get()->toArray();
                if (isset($bannerBranchData) && !empty($bannerBranchData)) {
                    $existBranchArray = array();
                    foreach ($bannerBranchData as $key => $val) {
                        array_push($existBranchArray, $val['branch_id']);
                    }
                    $is_exist = true;
                    if (isset($branch_id) && !empty($branch_id)) {
                        $newBranch = array_diff($branch_id, $existBranchArray);
                        $oldBranchArray = array_diff($existBranchArray, $branch_id);
                        $updateBranchArray = array_intersect($existBranchArray, $branch_id);
                        $removeBranchArray = array_diff($oldBranchArray, $branch_id);
                        if (empty($oldBranchArray) && empty($updateBranchArray) && empty($newBranch)) {
                            $is_exist = true;
                        } else {
                            $is_exist = false;
                            /*New insert*/
                            if (isset($newBranch) && !empty($newBranch)) {
                                foreach ($newBranch as $key => $value) {
                                    $insertCatBranch = [
                                        'uuid' => Helper::getUuid(),
                                        'banner_id' => $bannerId,
                                        'branch_id' => $value,
                                        'status' => 1,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];
                                    BannerBranch::create($insertCatBranch);
                                }
                            }
                            /*status update*/
                            if (isset($updateBranchArray) && !empty($updateBranchArray)) {
                                foreach ($updateBranchArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 1,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];

                                    BannerBranch::where(['branch_id' => $value, 'banner_id' => $bannerId])->update($updateObj);
                                }
                            }
                            if (isset($removeBranchArray) && !empty($removeBranchArray)) {
                                foreach ($removeBranchArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];

                                    BannerBranch::where(['branch_id' => $value, 'banner_id' => $bannerId])->update($updateObj);
                                }
                            }
                        }
                    }

                    if ($is_exist == true) {
                        if (isset($existBranchArray) && !empty($existBranchArray)) {
                            foreach ($existBranchArray as $key => $value) {
                                $updateObj = [
                                    'status' => 2,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => $loginId
                                ];
                                BannerBranch::where(['branch_id' => $value, 'banner_id' => $bannerId])->update($updateObj);
                            }
                        }
                    }
                } else {
                    if (isset($branch_id) && !empty($branch_id)) {
                        foreach ($branch_id as $key => $value) {
                            $insertCatBranch = [
                                'uuid' => Helper::getUuid(),
                                'banner_id' => $bannerId,
                                'branch_id' => $value,
                                'status' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $loginId
                            ];
                            BannerBranch::create($insertCatBranch);
                        }
                    }
                }

                Helper::saveLogAction('1', 'Banner', 'Update', 'Update branch ' . $uuid, Auth::user()->id);
                DB::commit();
                Helper::log('Banner update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Banner update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Banner', 'Update Exception ' . $exception, Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
