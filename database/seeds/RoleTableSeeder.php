<?php

use Illuminate\Database\Seeder;
use App\Models\Roles;
use App\Models\Helper;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleList = [
            'Super Admin','Customer','Branch'
        ];

        foreach ($roleList as $key => $value) {
            $count = Roles::where('role_name', $value)->count();
            if ($count == 0) {
                $date = date('Y-m-d H:i:s');
                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'role_name' => $value,
                    'role_updated_at' => $date,
                    'role_updated_by' => 1
                ];

                Roles::create($insertData);
            }
        }
    }
}
