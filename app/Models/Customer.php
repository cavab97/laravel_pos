<?php

namespace App\Models;

use App\Http\Middleware\Authenticate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends Model implements Authenticatable
{
    use AuthenticableTrait;
    use SoftDeletes;
    public $timestamps = true;
    protected $table = "customer";
    protected $primaryKey = "customer_id";
    protected $guarded = ['customer_id'];
    protected $dates = ['deleted_at'];

    protected $guard = 'fronts';
}
