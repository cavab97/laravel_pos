<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = "log";
    protected $primaryKey = "log_id";
    protected $guarded = ['log_id'];
}
