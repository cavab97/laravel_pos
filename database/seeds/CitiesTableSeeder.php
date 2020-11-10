<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\States;
use App\Models\Cities;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = File::get('database/seeds/data-json/malasiyacities.json');
        $cityList = json_decode($data, true);

        foreach ($cityList['cities'] as $key => $value) {
            $cityName = $value['name'];
            $stateId = $value['state_id'];
            $slug = strtolower(str_replace(' ', '-', $cityName));

            $checkState = States::where('state_id', $stateId)->count();
            if ($checkState > 0) {
                $count = Cities::where('slug', $slug)->where('state_id', $stateId)->count();
                if ($count == 0) {
                    $insertData = [
                        'city_id' => $value['city_id'],
                        'name' => $cityName,
                        'slug' => $slug,
                        'state_id' => $stateId
                    ];
                    Cities::create($insertData);
                }
            }
        }
    }
}
