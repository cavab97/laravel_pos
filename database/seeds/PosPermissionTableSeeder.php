<?php

use Illuminate\Database\Seeder;
use App\Models\PosPermission;

class PosPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionList = PosPermission::allPOSPermissions();

        foreach ($permissionList as $value) {
            $count = PosPermission::where('pos_permission_name', $value)->count();
            if ($count == 0) {
                PosPermission::create(['pos_permission_name' => $value,'pos_permission_updated_at' => date('Y-m-d H:i:s'),'pos_permission_updated_by' => 1]);
            }
        }
    }
}
