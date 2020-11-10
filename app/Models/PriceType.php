<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceType extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "price_type";
    protected $primaryKey = "pt_id";
    protected $guarded = ['pt_id'];
}
