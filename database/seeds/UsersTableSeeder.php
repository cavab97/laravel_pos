<?php

use Illuminate\Database\Seeder;
use App\Models\Helper;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Y-m-d H:i:s');
        $username = 'admin';
        $insertData = [
            'uuid' => Helper::getUuid(),
            'name' => 'Administrator',
            'role' => 1,
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Admin@123'),
            'country_code' => '91',
            'mobile' => '1234567890',
            'user_pin' => Helper::generatePin(6),
            'profile' => 'backend/images/user.png',
            'status' => '1',
            'is_admin' => '1',
            'created_at' => $date,
            'updated_at' => $date,
            'created_by' => 1
        ];

        $count = User::where('username', $username)->count();
        if ($count == 0) {
            User::create($insertData);
        }
    }
}
