<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "voucher";
    protected $primaryKey = "voucher_id";
    protected $guarded = ['voucher_id'];
    protected $dates = ['deleted_at'];
}
