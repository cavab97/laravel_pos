<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    public $timestamps = false;
    protected $table = "shift";
    protected $primaryKey = "shift_id";
    protected $guarded = ['shift_id'];
}
