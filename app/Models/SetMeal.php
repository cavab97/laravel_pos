<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SetMeal extends Model
{
    public $timestamps = false;
    protected $table = "setmeal";
    protected $primaryKey = "setmeal_id";
    protected $guarded = ['setmeal_id'];

    static public function getSetmealByBranch($branchId, $search = null)
    {
        if(!empty($search)){
            $whereProduct = " ( name LIKE '%$search%' ) AND status = 1";
            $getProdIds = self::whereRaw($whereProduct)->select('setmeal_id')->get()->toArray();
            $getProdId = [];
            foreach ($getProdIds as $value) {
                array_push($getProdId, $value['setmeal_id']);
            }
            $productData = self::leftJoin('setmeal_branch', 'setmeal_branch.setmeal_id', 'setmeal.setmeal_id')
                ->select('setmeal.*',
                    DB::raw('(Select asset_path from asset where asset_type = 2 AND asset_type_id = setmeal.setmeal_id limit 1) AS product_image')
                )->whereIn('setmeal.setmeal_id',$getProdId)->where(['setmeal.status' => 1,
                    'setmeal_branch.status' => 1,
                    'setmeal_branch.branch_id' => $branchId,
                ])->orderBy('setmeal.setmeal_id', 'ASC')->paginate(config('constants.page_limit'));
            return $productData;
        } else {
            $productData = self::leftJoin('setmeal_branch', 'setmeal_branch.setmeal_id', 'setmeal.setmeal_id')
                ->select('setmeal.*',
                    DB::raw('(Select asset_path from asset where asset_type = 2 AND asset_type_id = setmeal.setmeal_id limit 1) AS product_image')
                )->where(['setmeal.status' => 1,
                    'setmeal_branch.status' => 1,
                    'setmeal_branch.branch_id' => $branchId,
                ])->orderBy('setmeal.setmeal_id', 'ASC')->paginate(config('constants.page_limit'));
            return $productData;
        }
    }
}
