<?php

use Illuminate\Database\Seeder;
use App\Models\Languages;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $languageList = [
            [
                'name' => 'English',
                'code' => 'en',
                'country_id' => 230,
                'icon' => 'images/languages/eng.png',
                'currency' => 'USD',
                'currency_sign' => '$',
            ], [
                'name' => 'Chinese',
                'code' => 'ch',
                'country_id' => 191,
                'icon' => 'images/languages/kuv.png',
                'currency' => 'SR',
                'currency_sign' => 'SR',
            ],
        ];

        foreach ($languageList as $key => $value) {
            $count = Languages::where('name', $value['name'])->count();
            if ($count == 0) {
                Languages::create($languageList[$key]);
            }
        }
    }
}
