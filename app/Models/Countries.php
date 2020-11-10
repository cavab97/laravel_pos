<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Countries extends Model
{
    use SoftDeletes;
    protected $table = "country";
    protected $primaryKey = "country_id";

    protected $guard = 'admin';

    protected $guarded = ['country_id'];

    static function nameById($countryId)
    {
        $countryData = self::where('country_id', $countryId)->first();
        $countryName = '';
        if (!empty($countryData)) {
            $countryName = $countryData->name;
        }
        return $countryName;
    }
}
