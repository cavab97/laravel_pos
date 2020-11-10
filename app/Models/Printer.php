<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printer extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "printer";
    protected $primaryKey = "printer_id";
    protected $guarded = ['printer_id'];
    protected $dates = ['deleted_at'];
}
