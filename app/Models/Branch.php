<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "branch";
    protected $primaryKey = "branch_id";
    protected $guarded = ['branch_id'];
    protected $dates = ['deleted_at'];

    static public function getBranchDataBySlug($slug)
    {
        return self::where('slug', $slug)->first()->toArray();
    }
}
