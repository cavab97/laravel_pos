<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;
    protected $table = "table";
    protected $primaryKey = "table_id";
    protected $guarded = ['table_id'];
    public $timestamps = false;

    static public function getAvilableTable($uuid)
    {
        return self::where(['uuid' => $uuid, 'available_status' => 1])->first();
    }
}
