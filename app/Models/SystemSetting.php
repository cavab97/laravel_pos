<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemSetting extends Model
{
    use SoftDeletes;
    protected $table = "system_setting";
    protected $primaryKey = "system_setting_id";
    protected $guarded = ['system_setting_id'];
    protected $dates = ['deleted_at'];
    public $timestamps = false;
}
