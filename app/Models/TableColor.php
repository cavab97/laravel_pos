<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableColor extends Model
{
    public $timestamps = false;
    protected $table = "table_color";
    protected $primaryKey = "id";
    protected $guarded = ['id'];
}
