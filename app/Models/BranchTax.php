<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchTax extends Model
{
    public $timestamps = false;
    protected $table = "branch_tax";
    protected $primaryKey = "id";
    protected $guarded = ['id'];
}
