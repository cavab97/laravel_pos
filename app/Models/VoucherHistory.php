<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherHistory extends Model
{
    protected $table = "voucher_history";
    protected $primaryKey = "voucher_history_id";
    protected $guarded = ['voucher_history_id'];
    public $timestamps = false;
}
