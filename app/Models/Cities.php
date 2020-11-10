<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cities extends Model
{
    use SoftDeletes;
    protected $table = "city";
    protected $primaryKey = "city_id";

    protected $guard = 'admin';

    protected $guarded = ['city_id'];

    static function listByState($stateId)
    {
        return self::where('state_id', $stateId)->orderBy('name', 'ASC')->get();
    }

    static function nameById($cityId)
    {
        $cityData = self::where('city_id', $cityId)->first();
        $cityName = '';
        if (!empty($cityData)) {
            $cityName = $cityData->name;
        }
        return $cityName;
    }
}
