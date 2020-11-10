<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Permissions extends Model
{
    public $timestamps = false;
    protected $table = "permission";
    protected $primaryKey = "permission_id";
    protected $guarded = ['permission_id'];

    static $allPermissionList = [
        'dashboard', 'roles', 'customer', 'branch', 'cashier', 'waiter', 'attributes', 'modifier', 'category', 'price_type', 'printer', 'table', 'kitchen',
        'banner', 'product', 'product_inventory', 'tax', 'category_attribute', 'logs', 'attendance', 'users',
        'rac', 'box', 'setmeal'
    ];

    static $actionList = ['view', 'add', 'edit', 'delete'];

    static function allPermissions()
    {
        $permissions = self::$allPermissionList;
        $actions = self::$actionList;

        $permissionList = [];
        foreach ($permissions as $permission) {
            $name = strtolower(str_replace(' ', '_', trim($permission)));
            foreach ($actions as $action) {
                $actionName = strtolower(str_replace(' ', '_', trim($action)));

                $permissionName = $actionName . "_" . $name;

                if ($permissionName == 'view_dashboard') {
                    $permissionList[] = $permissionName;
                    break;
                } else {
                    $permissionList[] = $permissionName;
                }
            }
        }
        return $permissionList;
    }

    static function checkActionPermission($permissionName)
    {
        $userData = Auth::user();
        if (!empty($userData)) {
            $roleId = $userData->role;
            if ($roleId == 1) {
                return true;
            }

            if (!is_array($permissionName)) {
                $permissionName = [$permissionName];
            }

            /*$checkPermission = RolePermission::join('permission AS P', 'P.permission_id', '=', 'role_permission.rp_permission_id')
                ->where('role_permission.rp_role_id', $roleId)
                ->whereIn('P.permission_name', $permissionName)
                ->count();*/
            $checkUserPermission = UserPermission::join('permission AS P', 'P.permission_id', '=', 'user_permission.permission_id')
                ->where('user_permission.user_id', $userData->id)->where('user_permission.status', 1)
                ->whereIn('P.permission_name', $permissionName)
                ->count();
            if ($checkUserPermission == 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

}
