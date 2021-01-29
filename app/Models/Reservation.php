<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = "reservation";
    protected $primaryKey = "id";
    protected $guarded = ['id'];
}
