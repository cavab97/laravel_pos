<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "product";
    protected $primaryKey = "product_id";
    protected $guarded = ['product_id'];
    protected $dates = ['deleted_at'];

    static public function getProductByBranchCat($branchId, $category, $search = null)
    {
        if(!empty($search)){
            $whereProduct = " ( name LIKE '%$search%' OR sku LIKE '%$search%' ) AND status = 1";
            $getProdIds = self::whereRaw($whereProduct)->select('product_id')->get()->toArray();
            $getProdId = [];
            foreach ($getProdIds as $value) {
                array_push($getProdId, $value['product_id']);
            }
            $productData = self::leftJoin('product_branch', 'product_branch.product_id', 'product.product_id')
                ->leftJoin('product_category', 'product_category.product_id', 'product.product_id')
                ->select('product.*',
                    DB::raw('(Select asset_path from asset where asset_type = 1 AND status = 1 AND asset_type_id = product.product_id limit 1) AS product_image')
                )->whereIn('product.product_id',$getProdId)->where(['product.status' => 1,
                    'product_branch.status' => 1,
                    'product_branch.branch_id' => $branchId,
                    'product_category.category_id' => $category
                ])->orderBy('product_branch.display_order', 'ASC')->paginate(config('constants.page_limit'));
            return $productData;

        } else {
            $productData = self::leftJoin('product_branch', 'product_branch.product_id', 'product.product_id')
                ->leftJoin('product_category', 'product_category.product_id', 'product.product_id')
                ->select('product.*',
                    DB::raw('(Select asset_path from asset where asset_type = 1 AND status = 1 AND asset_type_id = product.product_id limit 1) AS product_image')
                )->where(['product.status' => 1,
                    'product_branch.status' => 1,
                    'product_branch.branch_id' => $branchId,
                    'product_category.category_id' => $category
                ])->orderBy('product_branch.display_order', 'ASC')->paginate(config('constants.page_limit'));
            return $productData;
        }
    }

    static public function getProductByUUid($uuid)
    {
        $productData = self::leftJoin('product_branch', 'product_branch.product_id', 'product.product_id')
            ->leftJoin('product_category', 'product_category.product_id', 'product.product_id')
            ->select('product.*',
                DB::raw('(Select asset_path from asset where asset_type = 1 AND status = 1 AND asset_type_id = product.product_id limit 1) AS product_image')
            )->where(['product.status' => 1,
                'product_branch.status' => 1
            ])->orderBy('product_branch.display_order', 'ASC')->first()->toArray();
        return $productData;
    }
}
