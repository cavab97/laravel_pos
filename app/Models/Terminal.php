<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Terminal extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "terminal";
    protected $primaryKey = "terminal_id";
    protected $guarded = ['terminal_id'];
    protected $dates = ['deleted_at'];
}
