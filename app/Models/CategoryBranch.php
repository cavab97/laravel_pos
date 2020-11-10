<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBranch extends Model
{
    protected $table = "category_branch";
    protected $primaryKey = "cb_id";
    protected $guarded = ['cb_id'];
    public $timestamps = false;
}
