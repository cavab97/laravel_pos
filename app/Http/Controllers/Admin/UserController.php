<?php

namespace App\Http\Controllers\Admin;

use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\PosPermission;
use App\Models\PosRolePermission;
use App\Models\RolePermission;
use App\Models\Roles;
use App\Models\Branch;
use App\Models\UserBranch;
use App\Models\UserPermission;
use App\Models\UserPosPermission;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_users');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.users.index');
    }

    /**
     * Pagination for backend users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];

			$userData = Auth::user();

            $defaultCondition = 'uuid != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $defaultCondition .= " AND ( name LIKE '%$search%' OR email LIKE '%$search%' OR mobile LIKE '%$search%' ) ";
            }

            $name = $request->input('name', null);
            if ($name != null) {
                $name = Helper::string_sanitize($name);
                $defaultCondition .= " AND `name` LIKE '%$name%' ";
            }

            $mobile = $request->input('mobile', null);
            if ($mobile != null) {
                $defaultCondition .= " AND `mobile` LIKE '%$mobile%' ";
            }
            $email = $request->input('email', null);
            if ($email != null) {
                $defaultCondition .= " AND `email` LIKE '%$email%' ";
            }
            /*$user_pin = $request->input('user_pin', null);
            if ($user_pin != null) {
                $defaultCondition .= " AND `user_pin` LIKE '%$user_pin%' ";
            }*/
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $from = isset($from_date) ? (date('Y-m-d', strtotime($from_date))) : null;
            $to = isset($to_date) ? (date('Y-m-d', strtotime($to_date))) : null;

            if (empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`users`.created_at, '%Y-%m-%d') <= '" . $to . "'";
            }
            if (!empty($from) && empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`users`.created_at, '%Y-%m-%d') >= '" . $from . "'";
            }
            if (!empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`users`.created_at, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
            }

			if ($userData->role != 1) {
                $branchIds = UserBranch::where('user_id', $userData->id)->where('status',1)->select("branch_id")->get();
                $userIds = UserBranch::where('user_id','!=',$userData->id)->whereIn('branch_id',$branchIds)->select('user_id')->get();
                if(!empty($userIds)){
                    $ids = [];
                    foreach ($userIds as $value){
                        array_push($ids, $value->user_id);
                    }
                    if(count($ids) > 0){
                        $implodeIds = implode(',',$ids);
                    } else {
                        $implodeIds = "''";
                    }
                    $defaultCondition .= " AND id IN ($implodeIds)";
                }
            }

            $userCount = User::whereNotIn('role', Roles::$notIn)
                ->whereRaw($defaultCondition)
                ->count();
            $userList = User::whereNotIn('role', Roles::$notIn)
                ->whereRaw($defaultCondition)
                ->select(
                    'id', 'uuid', 'name', 'email', 'mobile', 'profile', 'status', 'last_login', 'user_pin',
                    DB::raw('(SELECT role_name FROM role WHERE role.role_id = users.role) AS role_name')
                )
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get();

            if(!empty($userList)){
                foreach ($userList as $key => $value){
                    $branch = UserBranch::where('user_branch.user_id', $value['id'])->where('user_branch.status',1)->get();
                    if(!empty($branch)){
                        $i = 0;
                        $branch_name = '';
                        foreach ($branch as $bk => $bv){
                            $branchData = Branch::where('branch_id',$bv->branch_id)->select('name')->first();
                            $name = $branchData->name;
                            $branch_name .= $name;
                            if (count($branch) != ($i + 1)) {
                                $branch_name .= ',';
                            }
                            $i++;
                        }
                        $userList[$key]['branch_name'] = $branch_name;
                    }

                    if(empty($value['profile']) || !file_exists(public_path($value['profile']))){
                        $cusList[$key]['profile'] = config('constants.default_user');
                    }
                }
            }

            return response()->json([
                "aaData" => $userList,
                "iTotalDisplayRecords" => $userCount,
                "iTotalRecords" => $userCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('User pagination exception');
            Helper::log($exception);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_users');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $roleList = Roles::whereNotIn('role_id', Roles::$notIn)->where('role_status',1)->get();
        $userData = Auth::user();
        if($userData->role == 1){
            $branchList = Branch::where('status', 1)->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->where('status',1)->select("branch_id")->get();
            $branchList = Branch::whereIn('branch_id',$branchIds)->where('status', 1)->get();
        }
        return view('backend.users.create', compact('roleList', 'branchList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        Helper::log('User Store : start');
        try {
            $name = trim($request->name);
            $email = trim($request->email);
            $username = trim($request->username);
            $mobile = $request->mobile;
            $role_id = $request->role_id;
            $commision_percent = $request->commision_percent;
            $password = $request->password;
            $country_code = $request->country_code;
            $branch_id = $request->branch_id;
            $user_pin = $request->user_pin;

            $status = $request->status;

			$loginuserData = Auth::user();

            $checkMobile = User::where('mobile', $mobile)->count();
            $checkEmail = User::where('email', $email)->count();
            $checkUsername = User::where('username', $username)->count();

            if ($checkEmail > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.email_exists')]);
            } elseif ($checkMobile > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.mobile_exists')]);
            } elseif ($checkUsername > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.username_exists')]);
            } else {

                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'name' => $name,
                    'email' => $email,
                    'role' => $role_id,
                    'username' => $username,
                    'password' => Hash::make($request->password),
                    'country_code' => $country_code,
                    'mobile' => $mobile,
                    'commision_percent' => $commision_percent,
                    'user_pin' => $user_pin,
                    'status' => $status,
                    'is_admin' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];

                if ($file = $request->file('profile')) {
                    $profileFolder = $this->createDirectory('profile');
                    $file = $request->file('profile');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move("$profileFolder/", $fileName);
                    chmod($profileFolder . '/' . $fileName, 0777);
                    $profile = 'uploads/profile/' . $fileName;
                    $insertData['profile'] = $profile;
                } else {
                    $insertData['profile'] = config('constants.default_user');
                }

                $userData = User::create($insertData);
                $userId = $userData->id;

                /*Assign User Branch*/
                if (!empty($branch_id) && $loginuserData->role == 1) {
                    foreach ($branch_id as $key => $value) {
                        $insertUserBranch = [
                            'ub_uuid' => Helper::getUuid(),
                            'user_id' => $userId,
                            'branch_id' => $value,
                            'updated_at' => config('constants.date_time'),
                            'updated_by' => Auth::user()->id,
                        ];
                        UserBranch::create($insertUserBranch);
                    }
                } else {
                    $branchIds = UserBranch::where('user_id', $loginuserData->id)->where('status',1)->select("branch_id")->first();
                    if(!empty($branchIds)){

                            $insertUserBranch = [
                                'ub_uuid' => Helper::getUuid(),
                                'user_id' => $userId,
                                'branch_id' => $branchIds->branch_id,
                                'updated_at' => config('constants.date_time'),
                                'updated_by' => Auth::user()->id,
                            ];
                            UserBranch::create($insertUserBranch);

                    }
                }

                /*User Permission*/
                $getRolePermission = RolePermission::join('permission', 'permission.permission_id', 'role_permission.rp_permission_id')
                    ->where('role_permission.rp_role_id', $role_id)
                    ->select('permission.permission_id')
                    ->get();
                if (!empty($getRolePermission)) {
                    foreach ($getRolePermission as $value) {
                        $insertPermission = [
                            'up_uuid' => Helper::getUuid(),
                            'user_id' => $userId,
                            'permission_id' => $value->permission_id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::user()->id,
                        ];
                        UserPermission::create($insertPermission);
                    }
                }

                /*User POS Permission*/
                $getPosPermission = PosRolePermission::join('pos_permission', 'pos_permission.pos_permission_id', 'pos_role_permission.pos_rp_permission_id')
                    ->where('pos_role_permission.pos_rp_role_id', $role_id)->where('pos_role_permission.pos_rp_permission_status',1)
                    ->select('pos_permission.pos_permission_id')
                    ->get();
                if (!empty($getPosPermission)) {
                    foreach ($getPosPermission as $value) {
                        if(!empty($value)) {
                            $insertPermission = [
                                'up_pos_uuid' => Helper::getUuid(),
                                'user_id' => $userId,
                                'pos_permission_id' => $value->pos_permission_id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::user()->id,
                            ];
                            UserPosPermission::create($insertPermission);
                        }
                    }
                }

                DB::commit();
                Helper::saveLogAction('1', 'Users', 'Store', 'Add new User ' . $userData->uuid, Auth::user()->id);
                Helper::log('User Store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('User Store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Users', 'Store', 'Add new users ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('view_users');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = User::leftjoin('role', 'role.role_id', 'users.role')->where('users.uuid', $uuid)
            ->first();
        $userData->updated_name = '';
        $created_name = User::where('id', $userData->updated_by)
            ->select('users.name')
            ->first();
        if (!empty($created_name)) {
            $userData->updated_name = $created_name->name;
        }
        $userBranch = UserBranch::leftjoin('branch', 'branch.branch_id', 'user_branch.branch_id')
            ->where('user_branch.user_id', $userData->id)
            ->where('user_branch.status',1)
            ->select('user_branch.branch_id', 'branch.name', 'branch.uuid')
            ->get();
        $userData->userBranch = $userBranch;
        $language_id = Languages::getBackLanguageId();
        if (!empty($userData)) {
            return view('backend.users.view', compact('userData'));
        } else {
            return redirect()->route('admin.users.index')->with('error', trans('backend/common.oops'));
        }
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
        $checkPermission = Permissions::checkActionPermission('edit_users');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
		$user = Auth::user();
        $userData = User::where('uuid', $uuid)->first();
        $roleList = Roles::whereNotIn('role_id', Roles::$notIn)->where('role_status',1)->get();
        $userBranch = UserBranch::where('user_id', $userData->id)->where('status',1)->select('branch_id')->get();
        $userBranchIds = array();
        if (!empty($userBranch)) {
            foreach ($userBranch as $value) {
                array_push($userBranchIds, $value->branch_id);
            }
        }
        $generatePin = $userData->user_pin;
        //$branchList = Branch::where('status', 1)->get();
		if($user->role == 1){
            $branchList = Branch::where('status', 1)->get();
        } else {
            $branchIds = UserBranch::where('user_id', $user->id)->where('status',1)->select("branch_id")->get();
            $branchList = Branch::whereIn('branch_id',$branchIds)->where('status', 1)->get();
        }
        return view('backend.users.edit', compact('userData', 'roleList', 'branchList', 'userBranchIds', 'generatePin'));
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
        DB::beginTransaction();
        Helper::log('user update : start');
        try {
            $mobile = trim($request->mobile);
            $username = trim($request->username);
            $name = trim($request->name);
            $email = trim($request->email);
            $role_id = $request->role_id;
            $commision_percent = $request->commision_percent;
            $country_code = $request->country_code;
            $branch_id = $request->branch_id;

            $checkMobile = User::where('mobile', $mobile)->where('uuid', '!=', $uuid)->count();
            $checkEmail = User::where('email', $email)->where('uuid', '!=', $uuid)->count();
            $checkUsername = User::where('username', $username)->where('uuid', '!=', $uuid)->count();

            if ($checkEmail > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.email_exists')]);
            } elseif ($checkMobile > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.mobile_exists')]);
            } elseif ($checkUsername > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.username_exists')]);
            } else {
                $loginId = Auth::user()->id;
                $userData = User::where('uuid', $uuid)->first();
                $userId = $userData->id;
                $status = $request->status;

                $updateData = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role_id,
                    'username' => $username,
                    'country_code' => $country_code,
                    'mobile' => $mobile,
                    'commision_percent' => $commision_percent,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                if ($request->password) {
                    $updateData['password'] = Hash::make($request->password);
                }

                if ($file = $request->file('profile')) {
                    $profileFolder = $this->createDirectory('profile');
                    $file = $request->file('profile');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move("$profileFolder/", $fileName);
                    chmod($profileFolder . '/' . $fileName, 0777);
                    $profile = 'uploads/profile/' . $fileName;
                    $updateData['profile'] = $profile;
                }

                User::where('uuid', $uuid)->update($updateData);


                /*Assign User Branch*/
                $userBranchData = UserBranch::where('user_id', $userId)->get()->toArray();
                if (isset($userBranchData) && !empty($userBranchData)) {
                    $existBranchArray = array();
                    foreach ($userBranchData as $key => $val) {
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
                                    $insertUserBranch = [
                                        'ub_uuid' => Helper::getUuid(),
                                        'user_id' => $userId,
                                        'branch_id' => $value,
                                        'updated_at' => config('constants.date_time'),
                                        'updated_by' => Auth::user()->id,
                                    ];
                                    UserBranch::create($insertUserBranch);
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

                                    UserBranch::where(['branch_id' => $value, 'user_id' => $userId])->update($updateObj);
                                }
                            }
                            if (isset($removeBranchArray) && !empty($removeBranchArray)) {
                                foreach ($removeBranchArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Auth::user()->id
                                    ];

                                    UserBranch::where(['branch_id' => $value, 'user_id' => $userId])->update($updateObj);
                                }
                            }
                        }

						if ($is_exist == true) {
							if (isset($existBranchArray) && !empty($existBranchArray)) {
								foreach ($existBranchArray as $key => $value) {
									$updateObj = [
										'status' => 2,
										'updated_at' => date('Y-m-d H:i:s'),
										'updated_by' => Auth::user()->id
									];
									UserBranch::where(['branch_id' => $value, 'user_id' => $userId])->update($updateObj);
								}
							}
						}
					}

                } else {
                    if (isset($branch_id) && !empty($branch_id)) {
                        foreach ($branch_id as $key => $value) {
                            $insertUserBranch = [
                                'ub_uuid' => Helper::getUuid(),
                                'user_id' => $userId,
                                'branch_id' => $value,
                                'updated_at' => config('constants.date_time'),
                                'updated_by' => Auth::user()->id,
                            ];
                            UserBranch::create($insertUserBranch);
                        }
                    }
                }

                /*User Permission*/
                if ($userData->role != $role_id) {
                    //UserPermission::where('user_id', $userId)->delete();
                    $rolePermissionData = array();
                    $getrolePermissionData = RolePermission::where('rp_role_id',$role_id)->select('rp_permission_id')->get()->toArray();
                    foreach ($getrolePermissionData as $value){
                        array_push($rolePermissionData, $value['rp_permission_id']);
                    }
                    $userPermissionData = UserPermission::where('user_id',$userId)->select('permission_id')->get()->toArray();
                    if (isset($userPermissionData) && !empty($userPermissionData)) {
                        $existPermissionArray = array();
                        foreach ($userPermissionData as $key => $val) {
                            array_push($existPermissionArray, $val['permission_id']);
                        }
                        $is_exist = true;
                        if (isset($rolePermissionData) && !empty($rolePermissionData)) {
                            $newPermission = array_diff($rolePermissionData, $existPermissionArray);
                            $oldPermissionArray = array_diff($existPermissionArray, $rolePermissionData);
                            $updatePermissionArray = array_intersect($existPermissionArray, $rolePermissionData);
                            $removePermissionArray = array_diff($oldPermissionArray, $rolePermissionData);
                            if (empty($oldPermissionArray) && empty($updatePermissionArray) && empty($newPermission)) {
                                $is_exist = true;
                            } else {
                                $is_exist = false;
                                /*New insert*/
                                if (isset($newPermission) && !empty($newPermission)) {
                                    foreach ($newPermission as $key => $value) {
                                        $insertRoleUserPermission = [
                                            'up_uuid' => Helper::getUuid(),
                                            'user_id' => $userId,
                                            'status' => 1,
                                            'permission_id' => $value,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id
                                        ];
                                        UserPermission::create($insertRoleUserPermission);
                                    }
                                }

                                /*status update*/
                                if (isset($updatePermissionArray) && !empty($updatePermissionArray)) {
                                    foreach ($updatePermissionArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 1,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id
                                        ];

                                        UserPermission::where(['permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                                    }
                                }

                                if (isset($removePermissionArray) && !empty($removePermissionArray)) {
                                    foreach ($removePermissionArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 2,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id
                                        ];

                                        UserPermission::where(['permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                                    }
                                }
                            }
                        }

                        if ($is_exist == true) {
                            if (isset($existPermissionArray) && !empty($existPermissionArray)) {
                                foreach ($existPermissionArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Auth::user()->id
                                    ];
                                    UserPermission::where(['permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                                }
                            }
                        }
                    } else {
                        $getRolePermission = RolePermission::leftjoin('permission', 'permission.permission_id', 'role_permission.rp_permission_id')
                            ->where('role_permission.rp_role_id', $role_id)
                            ->select('permission.permission_id')
                            ->get();
                        if (!empty($getRolePermission)) {
                            foreach ($getRolePermission as $value) {
                                $insertPermission = [
                                    'up_uuid' => Helper::getUuid(),
                                    'user_id' => $userId,
                                    'permission_id' => $value->permission_id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id,
                                ];
                                UserPermission::create($insertPermission);
                            }
                        }
                    }

                    /*POS Permission*/
                    $rolePosPermissionData = array();
                    $getrolePosPermissionData = PosRolePermission::where(['pos_rp_role_id'=>$role_id,'pos_rp_permission_status'=>1])->select('pos_rp_permission_id')->get()->toArray();
                    foreach ($getrolePosPermissionData as $value){
                        array_push($rolePosPermissionData, $value['pos_rp_permission_id']);
                    }
                    $userPosPermissionData = UserPosPermission::where('user_id',$userId)->select('pos_permission_id')->get()->toArray();
                    if (isset($userPosPermissionData) && !empty($userPosPermissionData)) {
                        $existPosPermissionArray = array();
                        foreach ($userPosPermissionData as $key => $val) {
                            array_push($existPosPermissionArray, $val['pos_permission_id']);
                        }
                        $is_exist = true;
                        if (isset($rolePosPermissionData) && !empty($rolePosPermissionData)) {
                            $newPosPermission = array_diff($rolePosPermissionData, $existPosPermissionArray);
                            $oldPosPermissionArray = array_diff($existPosPermissionArray, $rolePosPermissionData);
                            $updatePosPermissionArray = array_intersect($existPosPermissionArray, $rolePosPermissionData);
                            $removePosPermissionArray = array_diff($oldPosPermissionArray, $rolePosPermissionData);
                            if (empty($oldPosPermissionArray) && empty($updatePosPermissionArray) && empty($newPosPermission)) {
                                $is_exist = true;
                            } else {
                                $is_exist = false;
                                /*New insert*/
                                if (isset($newPosPermission) && !empty($newPosPermission)) {
                                    foreach ($newPosPermission as $key => $value) {
                                        $insertRoleUserPosPermission = [
                                            'up_pos_uuid' => Helper::getUuid(),
                                            'user_id' => $userId,
                                            'status' => 1,
                                            'pos_permission_id' => $value,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id
                                        ];
                                        UserPosPermission::create($insertRoleUserPosPermission);
                                    }
                                }

                                /*status update*/
                                if (isset($updatePosPermissionArray) && !empty($updatePosPermissionArray)) {
                                    foreach ($updatePosPermissionArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 1,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id
                                        ];

                                        UserPosPermission::where(['pos_permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                                    }
                                }

                                if (isset($removePosPermissionArray) && !empty($removePosPermissionArray)) {
                                    foreach ($removePosPermissionArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 2,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id
                                        ];

                                        UserPosPermission::where(['pos_permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                                    }
                                }
                            }
                        }

                        if ($is_exist == true) {
                            if (isset($existPosPermissionArray) && !empty($existPosPermissionArray)) {
                                foreach ($existPosPermissionArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Auth::user()->id
                                    ];
                                    UserPosPermission::where(['pos_permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                                }
                            }
                        }
                    } else {
                        $getRolePermission = PosRolePermission::leftjoin('permission', 'permission.permission_id', 'pos_role_permission.pos_rp_permission_id')
                            ->where('pos_role_permission.pos_rp_role_id', $role_id)
                            ->select('permission.permission_id')
                            ->get();
                        if (!empty($getRolePermission)) {
                            foreach ($getRolePermission as $value) {
                                $insertPermission = [
                                    'up_pos_uuid' => Helper::getUuid(),
                                    'user_id' => $userId,
                                    'pos_permission_id' => $value->permission_id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id,
                                ];
                                UserPosPermission::create($insertPermission);
                            }
                        }
                    }

                }

                DB::commit();
                Helper::saveLogAction('1', 'Users', 'Update', 'Update User ' . $uuid, Auth::user()->id);
                Helper::log('user update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('user update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Users', 'Update', 'Update User ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_users');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.users.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        DB::beginTransaction();
        Helper::log('user delete : start');
        try {
            $userId = Auth::user()->id;
            User::where('uuid', $uuid)->update([
                'deleted_at' => config('constants.date_time'),
                'deleted_by' => $userId,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ]);
            Helper::log('user delete : finish');
            DB::commit();
            Helper::saveLogAction('1', 'Users', 'Destroy', 'Destroy User ' . $uuid, Auth::user()->id);
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('user delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Users', 'Destroy', 'Destroy User ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    public function userPermissons($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_users_permission');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = User::where('uuid', $uuid)->first();
        $userId = $userData->id;
        $roleId = $userData->role;
        $allowPermissionList = [];
        $allowPosPermissionList = [];

        $permissionList = UserPermission::leftjoin('permission', 'permission.permission_id', 'user_permission.permission_id')
            ->where('user_permission.user_id', $userId)->where('status', 1)->get();
        foreach ($permissionList as $value) {
            array_push($allowPermissionList, $value->permission_name);
        }
        $userData->allowPermission = $allowPermissionList;
        $userData->actionList = Permissions::$actionList;
        $userData->moduleList = Permissions::$allPermissionList;
        $userData->permissionList = Permissions::allPermissions();

        /*User POS Permission*/

        $pospermissionList = UserPosPermission::leftjoin('pos_permission', 'pos_permission.pos_permission_id', 'user_pos_permission.pos_permission_id')
            ->where('user_pos_permission.user_id', $userId)->where('status', 1)->get();
        /* foreach ($pospermissionList as $value) {
            array_push($allowPosPermissionList, $value->pos_permission_name);
        } */
        $userData->allowPosPermission = $pospermissionList->pluck('pos_permission_name')->toArray();//$allowPosPermissionList;
        $userData->posactionList = PosPermission::$actionListPOS;
        $userData->posmoduleList = PosPermission::pluck('pos_permission_name')->toArray();//PosPermission::$allPOSPermissionList;
        $userData->posPermissionList = PosPermission::allPOSPermissions();//PosPermission::pluck('pos_permission_name')->toArray();//
        return view('backend.users.permissions', compact('uuid', 'userData'));
    }

    /**
     * Role Permission update
     *
     * @param Request $request
     * @param $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissionStore(Request $request, $uuid)
    {
        Helper::log('User Permission store : start');
        DB::beginTransaction();
        try {

            $userData = User::where('uuid', $uuid)->first();
            $userId = $userData->id;
            //UserPermission::where('user_id', $userId)->delete();
            $getpermissions = $request->permissions;
            $pos_permissions = $request->pos_permissions;

            $permissions = array();
            if (isset($getpermissions) && count($getpermissions) > 0) {
                foreach ($getpermissions as $value) {
                    $permissionData = Permissions::where('permission_name', $value)->first();
                    if (!empty($permissionData)) {
                        array_push($permissions, $permissionData->permission_id);
                    }
                }
            }

            $userPermissionData = UserPermission::where('user_id',$userId)->select('permission_id')->get()->toArray();

            if (isset($userPermissionData) && !empty($userPermissionData)) {
                $existPermissionArray = array();
                foreach ($userPermissionData as $pkey => $pval) {
                    array_push($existPermissionArray, $pval['permission_id']);
                }
                $is_exist = true;
                if (isset($permissions) && count($permissions) > 0) {
                    $newPermission = array_diff($permissions, $existPermissionArray);
                    $oldPermissionArray = array_diff($existPermissionArray, $permissions);
                    $updatePermissionArray = array_intersect($existPermissionArray, $permissions);
                    $removePermissionArray = array_diff($oldPermissionArray, $permissions);
                    if (empty($oldPermissionArray) && empty($updatePermissionArray) && empty($newPermission)) {
                        $is_exist = true;
                    } else {
                        $is_exist = false;
                        /*New insert*/
                        if (isset($newPermission) && !empty($newPermission)) {
                            foreach ($newPermission as $key => $value) {
                                $insertUserPermission = [
                                    'up_uuid' => Helper::getUuid(),
                                    'user_id' => $userId,
                                    'status' => 1,
                                    'permission_id' => $value,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id
                                ];
                                UserPermission::create($insertUserPermission);
                            }
                        }

                        /*status update*/
                        if (isset($updatePermissionArray) && !empty($updatePermissionArray)) {
                            foreach ($updatePermissionArray as $key => $value) {
                                $updateObj = [
                                    'status' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id
                                ];

                                UserPermission::where(['permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                            }
                        }

                        if (isset($removePermissionArray) && !empty($removePermissionArray)) {
                            foreach ($removePermissionArray as $key => $value) {
                                $updateObj = [
                                    'status' => 2,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id
                                ];

                                UserPermission::where(['permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                            }
                        }
                    }
                }

                if ($is_exist == true) {
                    if (isset($existPermissionArray) && !empty($existPermissionArray)) {
                        foreach ($existPermissionArray as $key => $value) {
                            $updateObj = [
                                'status' => 2,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::user()->id
                            ];
                            UserPermission::where(['permission_id' => $value, 'user_id' => $userId])->update($updateObj);
                        }
                    }
                }

            } else {
                if (isset($getpermissions) && count($getpermissions) > 0) {
                    foreach ($getpermissions as $value) {
                        $permissionData = Permissions::where('permission_name', $value)->first();
                        if (!empty($permissionData)) {
                            $insertPermission = [
                                'up_uuid' => Helper::getUuid(),
                                'user_id' => $userId,
                                'permission_id' => $permissionData->permission_id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::user()->id,
                            ];
                            UserPermission::create($insertPermission);
                        }
                    }
                }
            }

            /* POS Permission */
            $userPermissionData = UserPosPermission::leftjoin('pos_permission','pos_permission.pos_permission_id','user_pos_permission.pos_permission_id')->where('user_pos_permission.user_id', $userId)
                ->select('pos_permission_name')
                ->get()->toArray();
            if (isset($userPermissionData) && !empty($userPermissionData)) {
                $existPosPermissionArray = array();
                foreach ($userPermissionData as $key => $val) {
                    array_push($existPosPermissionArray, $val['pos_permission_name']);
                }
                $is_exist = true;
                if (isset($pos_permissions) && !empty($pos_permissions)) {
                    $newPermission = array_diff($pos_permissions, $existPosPermissionArray);
                    $oldPermissionArray = array_diff($existPosPermissionArray, $pos_permissions);
                    $updatePermissionArray = array_intersect($existPosPermissionArray, $pos_permissions);
                    $removePermissionArray = array_diff($oldPermissionArray, $pos_permissions);
                    if (empty($oldPermissionArray) && empty($updatePermissionArray) && empty($newPermission)) {
                        $is_exist = true;
                    } else {
                        $is_exist = false;
                        /*New insert*/
                        if (isset($newPermission) && !empty($newPermission)) {
                            foreach ($newPermission as $key => $value) {
                                $permissionData = '';
                                if (strpos($value, 'action') === 0) {
                                    $permissionData = PosPermission::where('pos_permission_name', substr($value, strpos($value, "_")+1))->first();
                                } else {
                                    $permissionData = PosPermission::where('pos_permission_name', $value)->first();
                                }
                                $insertUserPosPermission = [
                                    'up_pos_uuid' => Helper::getUuid(),
                                    'user_id' => $userId,
                                    'pos_permission_id' => $permissionData->pos_permission_id,
                                    'status' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id
                                ];
                                UserPosPermission::create($insertUserPosPermission);
                            }
                        }
                        /*status update*/
                        if (isset($updatePermissionArray) && !empty($updatePermissionArray)) {
                            foreach ($updatePermissionArray as $key => $value) {
                                $permissionData = '';
                                if (strpos($value, 'action') === 0) {
                                    $permissionData = PosPermission::where('pos_permission_name', substr($value, strpos($value, "_")+1))->first();
                                } else {
                                    $permissionData = PosPermission::where('pos_permission_name', $value)->first();
                                }
                                $updateObj = [
                                    'status' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id
                                ];

                                UserPosPermission::where(['pos_permission_id' => $permissionData->pos_permission_id, 'user_id' => $userId])->update($updateObj);
                            }
                        }
                        if (isset($removePermissionArray) && !empty($removePermissionArray)) {
                            foreach ($removePermissionArray as $key => $value) {
                                $permissionData = '';
                                if (strpos($value, 'action') === 0) {
                                    $permissionData = PosPermission::where('pos_permission_name', substr($value, strpos($value, "_")+1))->first();
                                } else {
                                    $permissionData = PosPermission::where('pos_permission_name', $value)->first();
                                }
                                $updateObj = [
                                    'status' => 0,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id
                                ];

                                UserPosPermission::where(['pos_permission_id' => $permissionData->pos_permission_id, 'user_id' => $userId])->update($updateObj);
                            }
                        }
                    }
                }

                if ($is_exist == true) {
                    if (isset($existPosPermissionArray) && !empty($existPosPermissionArray)) {
                        foreach ($existPosPermissionArray as $key => $value) {
                            $permissionData = '';
                            if (strpos($value, 'action') === 0) {
                                $permissionData = PosPermission::where('pos_permission_name', substr($value, strpos($value, "_")+1))->first();
                            } else {
                                $permissionData = PosPermission::where('pos_permission_name', $value)->first();
                            }
                            $updateObj = [
                                'status' => 0,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::user()->id
                            ];
                            UserPosPermission::where(['pos_permission_id' => $permissionData->pos_permission_id, 'user_id' => $userId])->update($updateObj);
                        }
                    }
                }
            } else {
                if (isset($pos_permissions) && !empty($pos_permissions)) {
                    foreach ($pos_permissions as $key => $value) {
                        $permissionData = '';
                        if (strpos($value, 'action') === 0) {
                            $permissionData = PosPermission::where('pos_permission_name', substr($value, strpos($value, "_")+1))->first();
                        } else {
                            $permissionData = PosPermission::where('pos_permission_name', $value)->first();
                        }
                        if (!empty($permissionData)) {
                            $insertUserPosPermission = [
                                'up_pos_uuid' => Helper::getUuid(),
                                'user_id' => $userId,
                                'pos_permission_id' => $permissionData->pos_permission_id,
                                'status' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::user()->id
                            ];
                            UserPosPermission::create($insertUserPosPermission);
                            # code...
                        }
                    }
                }
            }

            Helper::log('User Permission store : finish');
            DB::commit();
            Helper::saveLogAction('1', 'User Permission store', 'User Permission store', 'User Permission store' . $userId, Auth::user()->id);
            return response()->json(['status' => 200, 'message' => trans('backend/users.user_permission_updated')]);
        } catch (\Exception $exception) {
            Helper::log('User Permission store : exception');
            Helper::log($exception);
            DB::rollBack();
            Helper::saveLogAction('1', 'User Permission store', 'User Permission store', 'User Permission store Exception' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/users.user_permission_not_updated')]);
        }
    }
}
