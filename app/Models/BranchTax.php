<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchTax extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "branch_tax";
    protected $primaryKey = "id";
    protected $guarded = ['id'];
}
