<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class States extends Model
{
    use SoftDeletes;
    protected $table = "state";
    protected $primaryKey = "state_id";

    protected $guard = 'admin';

    protected $guarded = ['state_id'];

    static function listByCountry($countryId)
    {
        return self::where('country_id', $countryId)->orderBy('name', 'ASC')->get();
    }

    static function nameById($stateId)
    {
        $stateData = self::where('state_id', $stateId)->first();
        $stateName = '';
        if (!empty($stateData)) {
            $stateName = $stateData->name;
        }
        return $stateName;
    }
}
