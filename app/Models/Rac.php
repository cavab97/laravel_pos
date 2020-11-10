<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rac extends Model
{
    public $timestamps = false;
    protected $table = "rac";
    protected $primaryKey = "rac_id";
    protected $guarded = ['rac_id'];
}
