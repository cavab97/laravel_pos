<?php

namespace App\Http\Controllers\Admin;

use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\PosPermission;
use App\Models\PosRolePermission;
use App\Models\RolePermission;
use App\Models\Roles;
use App\Http\Controllers\Controller;
use App\Models\UserPermission;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $roleList = Roles::whereNotIn('role_id', Roles::$notIn)->get();
        return view('backend.role.index', compact('roleList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $rolePermission = [];
        $allowPermissionList = [];
        $rolePermission['allowPermission'] = $allowPermissionList;

        $rolePermission['actionList'] = Permissions::$actionList;
        $rolePermission['moduleList'] = Permissions::$allPermissionList;
        $rolePermission['allPermissionList'] = Permissions::allPermissions();

        $posRolePermission = [];
        $allowPOSPermissionList = [];
        $posRolePermission['allowPOSPermission'] = $allowPermissionList;

        $posRolePermission['actionList'] = PosPermission::$actionListPOS;
        $posRolePermission['moduleList'] = PosPermission::pluck('pos_permission_name')->toArray();//PosPermission::$allPOSPermissionList;
        $posRolePermission['allPOSPermissionList'] = PosPermission::allPOSPermissions();

        return view('backend.role.create', compact('rolePermission','posRolePermission'));
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
        Helper::log('role create : start');
        try {
            $roleName = trim($request->role_name);
            $role_status = $request->role_status;

            $checkName = Roles::where('role_name', $roleName)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/roles.name_exists')]);
            } else {

                $roleData = Roles::create([
                    'uuid' => Helper::getUuid(),
                    'role_name' => $roleName,
                    'role_status' => $role_status
                ]);

                $roleId = $roleData->role_id;
                $permissions = $request->permissions;
                $pos_permissions = $request->pos_permissions;

                $moduleList = Permissions::$allPermissionList;
                if (isset($permissions) && count($permissions) > 0) {
                    foreach ($permissions as $value) {
                        $permissionData = Permissions::where('permission_name', $value)->first();
                        if (!empty($permissionData)) {
                            $getPermission = DB::table('role_permission')->where('rp_role_id', $roleId)->where('rp_permission_id', $permissionData->permission_id)->count();
                            if ($getPermission == 0) {
                                $data = [
                                    'rp_uuid' => Helper::getUuid(),
                                    'rp_role_id' => $roleId,
                                    'rp_permission_id' => $permissionData->permission_id,
                                    'rp_updated_at' => date('Y-m-d H:i:s'),
                                    'rp_updated_by' => Auth::user()->id,
                                ];
                                DB::table('role_permission')->insert($data);
                            }
                        }
                    }
                }

                /*POS Permission*/
                if (isset($pos_permissions) && count($pos_permissions) > 0) {
                    foreach ($pos_permissions as $value) {
                        $permissionData = PosPermission::where('pos_permission_name', $value)->first();
                        if (!empty($permissionData)) {
                            $getPermission = DB::table('pos_role_permission')->where('pos_rp_role_id', $roleId)->where('pos_rp_permission_id', $permissionData->pos_permission_id)->count();
                            if ($getPermission == 0) {
                                $data = [
                                    'pos_rp_uuid' => Helper::getUuid(),
                                    'pos_rp_role_id' => $roleId,
                                    'pos_rp_permission_id' => $permissionData->pos_permission_id,
                                    'pos_rp_updated_at' => date('Y-m-d H:i:s'),
                                    'pos_rp_updated_by' => Auth::user()->id,
                                ];
                                DB::table('pos_role_permission')->insert($data);
                            }
                        }
                    }
                }

                DB::commit();
                Helper::log('role create : finish');
                Helper::saveLogAction('1', 'Role', 'Store', 'Add new Role ' . $roleId, Auth::user()->id);

                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('role create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Role', 'Create role exception :' . $exception->getMessage(), Auth::user()->id);

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
        $roleData = Roles::where('uuid', $uuid)->first();
        $roleId = $roleData->role_id;
        $allowPermissionList = [];
        $rolePermission = [];
        $rolePermissionList = DB::table('role_permission')->join('permission AS P', 'P.permission_id', '=', 'role_permission.rp_permission_id')
            ->where('role_permission.rp_role_id', $roleId)
            ->select(['permission_name'])
            ->get();
        foreach ($rolePermissionList as $value) {
            array_push($allowPermissionList, $value->permission_name);
        }
        $rolePermission['allowPermission'] = $allowPermissionList;
        $rolePermission['actionList'] = Permissions::$actionList;
        $rolePermission['moduleList'] = Permissions::$allPermissionList;
        $rolePermission['allPermissionList'] = Permissions::allPermissions();

        $posRolePermission = [];
        $allowPOSPermissionList = [];
        $PosrolePermissionList = DB::table('pos_role_permission')->join('pos_permission AS P', 'P.pos_permission_id', '=', 'pos_role_permission.pos_rp_permission_id')
            ->where('pos_role_permission.pos_rp_role_id', $roleId)->where('pos_role_permission.pos_rp_permission_status',1)
            //->select(['pos_permission_name'])
            ->get();
        /*foreach ($PosrolePermissionList as $value) {
            array_push($allowPOSPermissionList, $value->pos_permission_name);
        }*/
        $posRolePermission['allowPOSPermission'] = $PosrolePermissionList->pluck('pos_permission_name')->toArray();//$allowPOSPermissionList;
        $posRolePermission['actionList'] = PosPermission::$actionListPOS;
        $posRolePermission['moduleList'] = PosPermission::pluck('pos_permission_name')->toArray();//PosPermission::$allPOSPermissionList;
        $posRolePermission['allPOSPermissionList'] = PosPermission::allPOSPermissions();

        return view('backend.role.edit', compact('roleData', 'rolePermission','posRolePermission'));
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
        Helper::log('role update : start');
        try {
            $roleName = trim($request->role_name);
            $role_status = $request->role_status;
            $checkName = Roles::where('role_name', $roleName)->where('uuid', '!=', $uuid)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/roles.name_exists')]);
            } else {

                $roleData = Roles::where('uuid', $uuid)->first();
                $roleId = $roleData->role_id;
                $permissions = $request->permissions;
                $pos_permissions = $request->pos_permissions;

                $moduleList = Permissions::$allPermissionList;
                $existPermission = DB::table('role_permission')->select('rp_permission_id')->where('rp_role_id', $roleId)->get()->toArray();

                Roles::where('uuid', $uuid)->update(['role_name' => $roleName, 'role_status' => $role_status]);
				
				$existRolePermissionArray = array();
                
				foreach ($existPermission as $key => $value) {
					array_push($existRolePermissionArray, $value->rp_permission_id);
                }
				
				if (isset($permissions) && !empty($permissions)) {
                    $permissionsIds = array();
                    foreach ($permissions as $value) {
                        $permissionData = Permissions::where('permission_name', $value)->first();
                        if (!empty($permissionData)) {
                            array_push($permissionsIds,$permissionData->permission_id);
                        }
                    }
                    $is_exist = true;
                    $newRolePermission = array_diff($permissionsIds, $existRolePermissionArray);
                    $oldRolePermissionArray = array_diff($existRolePermissionArray, $permissionsIds);
                    $updateRolePermissionArray = array_intersect($existRolePermissionArray, $permissionsIds);
                    $removeRolePermissionArray = array_diff($oldRolePermissionArray, $permissionsIds);
                    if (empty($oldRolePermissionArray) && empty($updateRolePermissionArray) && empty($newRolePermission)) {
                        $is_exist = true;
                    } else {
                        $is_exist = false;
                        /*New insert*/
                        if (isset($newRolePermission) && !empty($newRolePermission)) {
                            foreach ($newRolePermission as $key => $value) {
                                $getPermission = DB::table('role_permission')->where('rp_role_id', $roleId)->where('rp_permission_id', $value)->count();
                                if ($getPermission == 0) {
                                    $data = [
                                        'rp_uuid' => Helper::getUuid(),
                                        'rp_role_id' => $roleId,
                                        'rp_permission_id' => $value,
                                        'rp_updated_at' => date('Y-m-d H:i:s'),
                                        'rp_updated_by' => Auth::user()->id,
                                    ];
                                    DB::table('role_permission')->insert($data);
                                }
                            }
                        }

                        /*status update*/
                        if (isset($updateRolePermissionArray) && !empty($updateRolePermissionArray)) {
                            foreach ($updateRolePermissionArray as $key => $value) {
                                $updateObj = [
                                    'rp_updated_at' => date('Y-m-d H:i:s'),
                                    'rp_updated_by' => Auth::user()->id
                                ];
                                DB::table('role_permission')->where('rp_role_id', $roleId)->where('rp_permission_id', $value)->update($updateObj);
                            }
                        }

                        if (isset($removeRolePermissionArray) && !empty($removeRolePermissionArray)) {
                            foreach ($removeRolePermissionArray as $key => $value) {
                                DB::table('role_permission')->where('rp_role_id', $roleId)->where('rp_permission_id', $value)->delete();
                            }
                        }
                    }
                    if ($is_exist == true) {
                        if (isset($existRolePermissionArray) && !empty($existRolePermissionArray)) {
                            foreach ($existRolePermissionArray as $key => $value) {
                                DB::table('role_permission')->where('rp_role_id', $roleId)->where('rp_permission_id', $value)->delete();
                            }
                        }
                    }
                } else {
                    if (isset($existRolePermissionArray) && !empty($existRolePermissionArray)) {
                        foreach ($existRolePermissionArray as $key => $value) {
                            DB::table('role_permission')->where('rp_role_id', $roleId)->where('rp_permission_id', $value)->delete();
                        }
                    }
                }

                $rolePermissionData = array();
                $getrolePermissionData = RolePermission::where('rp_role_id',$roleId)->select('rp_permission_id')->get()->toArray();
                foreach ($getrolePermissionData as $value){
                    array_push($rolePermissionData, $value['rp_permission_id']);
                }
                $roleUsers = User::where('role',$roleId)->select('id')->get();
                if(!empty($roleUsers)){
                    foreach ($roleUsers as $rkey => $rvalue){
                        $userId = $rvalue->id;
                        $userPermissionData = UserPermission::where('user_id',$userId)->select('permission_id')->get()->toArray();
                        if (isset($userPermissionData) && !empty($userPermissionData)) {
                            $existPermissionArray = array();
                            foreach ($userPermissionData as $pkey => $pval) {
                                array_push($existPermissionArray, $pval['permission_id']);
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
                        }

                    }
                }

                /* POS Permission */
                $rolePermissionData = PosRolePermission::leftjoin('pos_permission','pos_permission.pos_permission_id','pos_role_permission.pos_rp_permission_id')->where('pos_rp_role_id', $roleId)
                    ->select('pos_permission_name')
                    ->get()->toArray();
                if (isset($rolePermissionData) && !empty($rolePermissionData)) {
                    $existPosPermissionArray = array();
                    foreach ($rolePermissionData as $key => $val) {
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
                                    if (!empty($permissionData)) {
                                        $insertRolePermission = [
                                            'pos_rp_uuid' => Helper::getUuid(),
                                            'pos_rp_role_id' => $roleId,
                                            'pos_rp_permission_id' => $permissionData->pos_permission_id,
                                            'pos_rp_permission_status' => 1,
                                            'pos_rp_updated_at' => date('Y-m-d H:i:s'),
                                            'pos_rp_updated_by' => Auth::user()->id
                                        ];
                                        PosRolePermission::create($insertRolePermission);
                                    }
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
                                        'pos_rp_permission_status' => 1,
                                        'pos_rp_updated_at' => date('Y-m-d H:i:s'),
                                        'pos_rp_updated_by' => Auth::user()->id
                                    ];

                                    PosRolePermission::where(['pos_rp_permission_id' => $permissionData->pos_permission_id, 'pos_rp_role_id' => $roleId])->update($updateObj);
                                }
                            }
                            if (isset($removePermissionArray) && !empty($removePermissionArray)) {
                                foreach ($removePermissionArray as $key => $value) {
                                    $permissionData = PosPermission::where('pos_permission_name', $value)->first();
                                    $updateObj = [
                                        'pos_rp_permission_status' => 2,
                                        'pos_rp_updated_at' => date('Y-m-d H:i:s'),
                                        'pos_rp_updated_by' => Auth::user()->id
                                    ];

                                    PosRolePermission::where(['pos_rp_permission_id' => $permissionData->pos_permission_id, 'pos_rp_role_id' => $roleId])->update($updateObj);
                                }
                            }
                        }
                    }

                    if ($is_exist == true) {
                        if (isset($existPosPermissionArray) && !empty($existPosPermissionArray)) {
                            foreach ($existPosPermissionArray as $key => $value) {
                                $permissionData = PosPermission::where('pos_permission_name', $value)->first();
                                $updateObj = [
                                    'pos_rp_permission_status' => 2,
                                    'pos_rp_updated_at' => date('Y-m-d H:i:s'),
                                    'pos_rp_updated_by' => Auth::user()->id
                                ];
                                PosRolePermission::where(['pos_rp_permission_id' => $permissionData->pos_permission_id, 'pos_rp_role_id' => $roleId])->update($updateObj);
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
                                $insertRolePermission = [
                                    'pos_rp_uuid' => Helper::getUuid(),
                                    'pos_rp_role_id' => $roleId,
                                    'pos_rp_permission_id' => $permissionData->pos_permission_id,
                                    'pos_rp_updated_at' => date('Y-m-d H:i:s'),
                                    'pos_rp_updated_by' => Auth::user()->id
                                ];
                                PosRolePermission::create($insertRolePermission);
                            }
                        }
                    }
                }

                DB::commit();
                Helper::saveLogAction('1', 'Role', 'Update', 'Update role ' . $roleId, Auth::user()->id);
                Helper::log('role update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('role update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Role', 'Update role exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
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
