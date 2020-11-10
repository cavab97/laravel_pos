<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftDetails extends Model
{
    public $timestamps = false;
    protected $table = "shift_details";
    protected $primaryKey = "shift_details_id";
    protected $guarded = ['shift_details_id'];
}
