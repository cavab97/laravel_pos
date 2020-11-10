<?php

use Illuminate\Database\Seeder;
use App\Models\Permissions;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionList = Permissions::allPermissions();

        foreach ($permissionList as $value) {
            $count = Permissions::where('permission_name', $value)->count();
            if ($count == 0) {
                Permissions::create(['permission_name' => $value]);
            }
        }
    }
}
