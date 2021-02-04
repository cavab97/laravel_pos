<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cities;
use App\Models\Company;
use App\Models\Countries;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Product;
use App\Models\States;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        Languages::setBackLang();
        $productCount = Product::count();
        return view('backend.dashboard', compact('productCount'));
    }

    public function login()
    {
        if (!empty(auth()->user()) && auth()->user()->is_admin == 1) {
            return redirect()->route('admin.home');
        }

        Helper::log('enter login page');
        return view('backend.login');
    }

    /**
     * backend login check credentials
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginPost(Request $request)
    {
        Helper::log('admin login post : start');
        try {
            $username = $request->username;

            $credentials = ['password' => $request->password];

            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $credentials['email'] = $username;
            } else {
                $credentials['username'] = $username;
            }
            if (Auth::attempt($credentials)) {
                $userStatus = Auth::user()->status;

                if ($userStatus == 1) {
                    Helper::log('admin login post : finish');
                    Helper::saveLogAction('1', 'Admin', 'Login', 'User login ', Auth::user()->id);
                    return response()->json(['status' => 200, 'message' => 'Welcome to MCN POS!']);
                } else {
                    Auth::logout();
                    Helper::saveLogAction('1', 'Admin', 'Logout', 'User logout ', Auth::user()->id);
                    return response()->json(['status' => 403, 'message' => 'Your account is not active. Please contact support team.']);
                }
            }
            return response()->json(['status' => 500, 'message' => 'Username or password is wrong.']);
        } catch (\Exception $exception) {
            Helper::log('admin login post : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Admin', 'login', 'Admin login post'. $exception->getMessage());
            return response()->json(['status' => 500, 'message' => 'Ooops...Something went wrong! Please contact to support team']);
        }
    }

    public function logout()
    {
        DB::beginTransaction();
        Helper::log('admin logout : start');
        try {
            $userId = Auth::user()->id;

            $updateData = [
                'last_login' => config('constants.date_time')
            ];
            User::where('id', $userId)->update($updateData);

            Auth::logout();
            Session::flush();
            Helper::log('admin logout : finish');
            DB::commit();
            return redirect()->route('admin.login');
        } catch (\Exception $exception) {
            Helper::log('admin logout : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Admin', 'logout', 'Admin logout post ' . $exception->getMessage(), Auth::user()->id);
            DB::rollBack();
            return redirect()->route('admin.home');
        }
    }

    public function dashboard()
    {
        Languages::setBackLang();
/*
        if(!Storage::exists('/uploads')) {
            Storage::makeDirectory('/uploads', 0775, true);
        } */
        return view('backend.dashboard', compact('productCount'));
    }

    /**
     * Admin Profile
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $userData = Auth::user();
        return view('backend.profile', compact('userData'));
    }

    public function profilePost(Request $request)
    {
        DB::beginTransaction();
        Helper::log('admin profile update : start');
        try {
            $userId = Auth::user()->id;
            $email = $request->email;
            $checkEmail = User::where('email', $email)->where('id', '!=', $userId)->count();

            if ($checkEmail > 0) {
                return response()->json(['status' => 409, 'message' => 'That email is taken. Try another']);
            } else {

                $updateData = [
                    'name' => $request->name,
                    'email' => $email,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => $userId,
                ];

                if ($request->password) {
                    $updateData['password'] = Hash::make($request->password);
                }
                User::where('id', $userId)->update($updateData);

                Helper::log('admin profile update : finish');
                DB::commit();
                return response()->json(['status' => 200, 'message' => 'This profile information has been updated']);
            }
        } catch (\Exception $exception) {
            Helper::log('admin profile update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Admin', 'profile', 'Admin profile post ' . $exception->getMessage(), Auth::user()->id);
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => 'Ooops...Something went wrong. Please contact to support team.']);
        }
    }

    public function adminShowForgotPassword()
    {
        return view('backend.forgot-password');
    }

    public function adminForgotPassword(Request $request)
    {
        DB::beginTransaction();
        Helper::log('Admin Forgot Password : start');
        try {
            $email = $request->email;
            $checkEmail = User::where('email', $email)->first();
            if (!empty($checkEmail)) {
                $checkEmailToken = DB::table('password_resets')->where('email', $email)->first();
                $token = Helper::randomString(32);
                if (!empty($checkEmailToken)) {
                    $insertData = [
                        'email' => $email,
                        'token' => $token,
                        'is_admin' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    DB::table('password_resets')->where('email', $email)->update($insertData);
                } else {
                    $insertData = [
                        'email' => $email,
                        'token' => $token,
                        'is_admin' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    DB::table('password_resets')->insert($insertData);
                }
                $insertData['name'] = $checkEmail->name;
                $insertData['url'] = url(config('constants.admin') . '/reset/password/' . $token);

                Helper::sendMailAdmin($insertData, 'emails.forgot-password', 'Forgot Password', $email);
                DB::commit();
                Helper::log('Admin Forgot Password : Finish');
                return response()->json(['status' => 200, 'message' => trans('backend/forgot-password.email_send_done')]);
            } else {
                return response()->json(['status' => 500, 'message' => trans('backend/forgot-password.email_not_exists')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Admin Forgot Password : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Admin', 'Forgot', 'Admin Forgot post ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function showResetForm($token)
    {
        return view('backend.reset-password', compact('token'));
    }

    public function resetPassword(Request $request)
    {

        DB::beginTransaction();
        Helper::log('Admin Reset Password : start');
        try {
            $token = $request->token;
            $checkToken = DB::table('password_resets')->where('token', $token)->first();
            if (!empty($checkToken)) {
                $checkEmail = User::where('email', $checkToken->email)->first();
                if (!empty($checkEmail)) {
                    $updateData = [
                        'password' => Hash::make($request->password)
                    ];
                    User::where('email', $checkEmail->email)->update($updateData);
                    DB::table('password_resets')->where('email', $checkToken->email)->delete();
                    DB::commit();
                    Helper::log('Admin Reset Password : Finish');
                    return response()->json(['status' => 200, 'message' => 'Your password has been changed.']);
                } else {
                    return response()->json(['status' => 500, 'message' => 'Email does not exists']);
                }
            } else {
                return response()->json(['status' => 500, 'message' => 'This link has expired']);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Admin Reset Password : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Admin', 'Reset', 'Admin Reset post ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => 'Ooops...Something went wrong. Please try again.']);
        }
    }

}
