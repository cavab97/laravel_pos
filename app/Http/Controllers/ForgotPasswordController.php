<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Helper;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * Forgot Password view
     */
    public function forgotPassword()
    {
        return view('frontend.popup.forgot-password');
    }

    /**
     * Froent forgot password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotpasswordPost(Request $request)
    {
        Helper::log('ForgotPassword send : start');
        DB::beginTransaction();
        try {
            $email = $request->email;

            $checkExists = Customer::where('email', $email)->count();
            //dd($checkExists);
            if ($checkExists == 0) {
                Helper::log('ForgotPassword : is exists');
                return response()->json(['status' => 409, 'message' => trans('frontend/common.email_not_exists')]);
            } else {
                $checkEmailToken = DB::table('password_resets')->where('email', $email)->first();
                $token = Helper::randomString(32);

                if (!empty($checkEmailToken)) {
                    $insertData = [
                        'email' => $email,
                        'token' => $token,
                        'is_admin' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    DB::table('password_resets')->where('email', $email)->update($insertData);
                } else {
                    $insertData = [
                        'email' => $email,
                        'token' => $token,
                        'is_admin' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    DB::table('password_resets')->insert($insertData);
                }
                $insertData['name'] = $email;
                $insertData['url'] = url('/reset-password/' . $token);
                Helper::sendMailAdmin($insertData, 'emails.forgot-password', 'Forgot Password', $email);
                DB::commit();
                Helper::log('Admin Forgot Password : Finish');
                return response()->json(['status' => 200, 'message' => trans('frontend/common.email_sent_successfully'), 'url'=>url()->previous()]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Forgotpassword: exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.not_save_information')]);
        }
    }

    public function resetPassword($token)
    {
        $GetResetRequest = DB::table('password_resets')->where('token', $token)->first();
        if (!empty($GetResetRequest)) {
            $fdate = config('constants.date_time');
            $tdate = $GetResetRequest->created_at;
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime1->diff($datetime2);
            $hours = $interval->format('%h') . '.' . $interval->format('%i');
            if ($hours < '2.0') {
                return view('frontend.reset-password', compact('token'));
            } else {
                $Timeup = 1;
                return view('frontend.error.reset-password-error', compact('Timeup'));
            }
        } else {
            $Timeup = 0;
            return view('frontend.error.reset-password-error', compact('Timeup'));
        }
    }

    public function resetPasswordUpdate(Request $request, $token)
    {
        DB::beginTransaction();
        Helper::log('reset password : start');
        try {
            $resetData = DB::table('password_resets')->where('token', $token)->first();
            if (empty($resetData)) {
                return response()->json(["status" => 404, "message" => "Your password reset link has expired"]);
            } else {
                $email = $resetData->email;
                DB::table('password_resets')->where('email', $email)->delete();

                $userData = Customer::where('email', $email)->first();
                $customerId = $userData->customer_id;

                $data = [
                    'password' => Hash::make($request->new_password),
                    'updated_by' => $customerId
                ];
                Customer::where('customer_id', $customerId)->update($data);

                DB::commit();
                Helper::log('reset password : finish');
                return response()->json(["status" => 200, "message" => "Your password has been reset successfully!"]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('reset password : exception');
            Helper::log($exception);
            return response()->json(["status" => 500, "message" => "Ooops...something went wrong."]);
        }
    }
}
