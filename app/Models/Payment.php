<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payment";
    protected $primaryKey = "payment_id";
    protected $guarded = ['payment_id'];
    public $timestamps = false;
    const ACTIVE = 1;
    const DEACTIVE = 0;

    static function getPaymentTreeIDsLibrary($paymentId)
    {
        $result = self::where('payment_id', $paymentId)
            ->select('payment_id', 'is_parent','name')
            ->first();

        $path = array();
        if (isset($result) && !empty($result)) {

            if ($result['is_parent'] != '' || $result['is_parent'] != 0) {
                $path[] = array('name' => $result['name'], 'payment_id' => $result['payment_id'], 'is_parent' => $result['is_parent']);
                $path = array_merge(self::getPaymentTreeIDsLibrary($result['is_parent']), $path);
            } else {
                $path[] = array('name' => $result['name'], 'payment_id' => $result['payment_id'], 'is_parent' => $result['is_parent']);
            }

        }
        return $path;
    }
}
