<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model
{
    public $timestamps = false;
    protected $table = "category_attribute";
    protected $primaryKey = "ca_id";
    protected $guarded = ['ca_id'];
}
