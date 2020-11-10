<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosPermission extends Model
{
    public $timestamps = false;
    protected $table = "pos_permission";
    protected $primaryKey = "pos_permission_id";
    protected $guarded = ['pos_permission_id'];

    static $allPOSPermissionList = [
        'order','item','report'
    ];

    static $actionListPOS = ['view', 'add', 'edit', 'delete'];

    static function allPOSPermissions()
    {
        $permissions = self::$allPOSPermissionList;
        $actions = self::$actionListPOS;

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
}
