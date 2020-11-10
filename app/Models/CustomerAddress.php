<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "customer_address";
    protected $primaryKey = "address_id";
    protected $guarded = ['address_id'];
    protected $dates = ['deleted_at'];
}
