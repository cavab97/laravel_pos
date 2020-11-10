<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class Helper extends Model
{
    public static function getUuid()
    {
        $query = DB::select('select uuid() AS uuid');
        return $query[0]->uuid;
    }

    public static function log($message)
    {
        Log::info($message);
    }

    public static function slugify($string)
    {
        $string = utf8_encode($string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $string = preg_replace('/[^a-z0-9- ]/i', '', $string);
        $string = str_replace(' ', '-', $string);
        $string = trim($string, '-');
        $string = strtolower($string);
        if (empty($string)) {
            return 'n-a';
        }
        return $string;
    }

    public static function generatePin($n)
    {
        $pin_number = mt_rand(100000, 999999);
        return $pin_number;
    }

    public static function randomNumber($n)
    {
        $characters = '0123456789';
        $randomNumber = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomNumber .= $characters[$index];
        }
        return $randomNumber;
    }

    public static function replaceNullWithEmptyString($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::replaceNullWithEmptyString($value);
            } else {
                if (is_null($value))
                    $array[$key] = "";
            }
        }
        return $array;
    }

    public static function randomString($n, $rmvTime = false)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        if ($rmvTime == true) {
            return $randomString;
        } else {
            return $randomString . time();
        }
    }

    public static function userDetail($userId)
    {
        $userData = User::where('id', $userId)->first()->toArray();
        $userData['profile'] = url($userData['profile']);
        return $userData;
    }

    public static function getSettingValue($key)
    {
        $settingValue = SystemSetting::where('key', $key)->select('value')->first();
        if ($settingValue) {
            $setting_value = $settingValue->value;
        } else {
            if ($key == 'timezone') {
                $setting_value = "Asia/Kuala_Lumpur";
            } elseif ($key == 'warning_stock_level') {
                $setting_value = 'true';
            } elseif ($key == 'sync_timer_minutes') {
                $setting_value = '20';
            }
        }
        return "$setting_value";
    }

    public static function sendMailAdmin($data, $filename, $subject, $toEmail)
    {
        try {
            $subject = "MCN - " . $subject;
            $fromEmail = config('constants.from_email');
            //$toEmail = env('APP_MAIL');
            $fromName = env('APP_NAME');

            Mail::send($filename, ['data' => $data], function ($message) use ($fromName, $fromEmail, $toEmail, $subject) {
                $message->to($toEmail, $fromName)
                    ->subject($subject);
                $message->from($fromEmail, $fromName);
            });
        } catch (\Exception $exception) {
            self::log($subject . ' : exception');
            self::log($exception);
        }
    }

    public static function bannerData()
    {
        return Banner::where('status', 1)->get()->toArray();
    }

    public static function saveLogAction($type = null, $file_name = null, $function = null, $details = null, $created_by = null)
    {
        $insertData = [
            'type' => $type,
            'file_name' => $file_name,
            'function' => $function,
            'details' => $details,
            'created_by' => $created_by,
            'ip_address' => Request::ip(),
        ];
        $actionData = Logs::create($insertData);
        $actionId = $actionData->log_id;

        return $actionId;
    }

    public static function saveTerminalLog($terminalId = null, $branchId = null, $module_name = null, $description = null, $activity_date = null, $activity_time = null, $table_name = null, $entity_id = null, $updated_by = null)
    {
        $insertData = [
            'uuid' => Helper::getUuid(),
            'terminal_id' => $terminalId,
            'branch_id' => $branchId,
            'module_name' => $module_name,
            'description' => $description,
            'activity_date' => $activity_date,
            'activity_time' => $activity_time,
            'table_name' => $table_name,
            'entity_id' => $entity_id,
            'status' => 1,
            'updated_at' => config('constants.date_time'),
            'updated_by' => $updated_by,
        ];
        $actionData = TerminalLog::create($insertData);
        $actionId = $actionData->id;

        return $actionId;
    }

    public static function string_sanitize($s)
    {
        $result = preg_replace("/[^a-zA-Z0-9]+/", " ", html_entity_decode($s, ENT_QUOTES));
        return $result;
    }

    public static function cartCounter()
    {
        if(Auth::guard('fronts')->user()){
            $customerId = Auth::guard('fronts')->user()->customer_id;
            $where = " user_id = '$customerId' ";
        }else{
            if(isset($_COOKIE['device_id']))
            {
                $deviceId = $_COOKIE['device_id'];
            }else{
                $deviceId = Helper::getUuid();
                setcookie('device_id', Helper::getUuid());
            }

            $where = " device_id = '$deviceId' ";
        }

        $cartCounter = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
            ->whereRaw($where)
            ->where(['source'=>1,'cart_payment_status'=>0])
            ->count();

        return $cartCounter;

    }

    public static function generateOrderNumber($branchId)
    {
        $branchData = Branch::where('branch_id', $branchId)->first();
        if ($branchData) {
            $prefix = $branchData->order_prefix;
            $invoice_start = $branchData->invoice_start;
            $GetLastOrder = Order::where('order_id', '!=', "")->where('branch_id', $branchId)->orderBy('order_id', 'DESC')->first();
            if (!empty($GetLastOrder)) {
                $lastOrderNumber = $GetLastOrder->invoice_no;
                $branchPrefix = substr($lastOrderNumber, 0, 2);
                $number = substr($lastOrderNumber, 2) + 1;
                $number = str_pad($number, 4, "0", STR_PAD_LEFT);
                $orderNumber = $branchPrefix . ($number);
            } else {
                $orderNumber = $prefix . $invoice_start;
            }
        }
        return $orderNumber;
    }
}
