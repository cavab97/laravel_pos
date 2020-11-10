<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(PosPermissionTableSeeder::class);
        $this->call(PaymentSeeder::class);
		//$this->call(CountriesTableSeeder::class);
        //$this->call(StatesTableSeeder::class);
        //$this->call(CitiesTableSeeder::class);
    }
}
