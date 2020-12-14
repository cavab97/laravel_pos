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

    static $actionPOSPermissionList = [
        'delete_item',
        'delete_order',
        'open_drawer',
        'cash_in',
        'cash_out',
        'payment',
        'discount_item',
        'discount_order',
        'entertainment_bill',
        'change_table',
        'join_table',
        'cancel_table',
        'change_pax',
        'split_table',
        'print_qr',
        'opening',
        'closing',
        'print_receipt',
        'sync',
        'refund',
        'cancel_transaction',
    ];

    static $actionListPOS = ['action'];

    static function allPOSPermissions()
    {
        $permissions = self::$actionPOSPermissionList;
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

    static function actionPOSPermissions()
    {
        $permissionsList = self::$actionPOSPermissionList;

        return $permissionsList;
    }
}
