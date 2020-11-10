<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminalLog extends Model
{
    public $timestamps = false;
    protected $table = "terminal_log";
    protected $primaryKey = "id";
    protected $guarded = ['id'];
}
