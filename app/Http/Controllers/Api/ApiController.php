<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Helper;
use App\Models\Terminal;
use App\Models\Roles;
use App\Models\UserBranch;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    protected $env = 'local';

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->env = env('APP_ENV');
    }

    /**
     * Default config data api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function configs(Request $request, $locale)
    {

        Helper::log('API configs : start');
        try {
            App::setLocale($locale);
            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['sync_timer'] = Helper::getSettingValue('sync_timer_minutes');
            $response['serverdatetime'] = date('Y-m-d h:i:s');
            $response['currency'] = config('constants.currency');

            Helper::log('API configs : finish');
            return response()->json(['status' => 200, 'show' => false, 'message' => trans('api.success'), 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('API configs : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /**
     * User login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request, $locale)
    {
        DB::beginTransaction();
        Helper::log('API user login : start');
        try {
            App::setLocale($locale);
            $username = $request->username;
            $userPin = $request->user_pin;
            $deviceType = $request->device_type;
            $deviceToken = $request->device_token;
            $deviceId = $request->device_id;
            $terminalId = $request->terminal_id;

            if (empty($username)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.username_required')]);
            } elseif (empty($userPin)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.pin_required')]);
            } elseif (empty($deviceType)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.device_type_required')]);
            } elseif (empty($deviceToken)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.device_token_required')]);
            } elseif (empty($terminalId)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.terminal_id_required')]);
            } else {
                if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    $userDetail = User::where('email', $username)->where('user_pin', $userPin)->whereIn('role', Roles::$In)->first();
                } else {
                    $userDetail = User::where('username', $username)->where('user_pin', $userPin)->whereIn('role', Roles::$In)->first();
                }

                if (!empty($userDetail)) {
                    if ($userDetail->role_id == config('constants.roles')['admin']) {
                        Auth::logout();
                        return response()->json(["status" => 404, 'show' => true, "message" => trans('api.user_not_allow')]);
                    } elseif ($userDetail->status != 1) {
                        Auth::logout();
                        return response()->json(["status" => 404, 'show' => true, "message" => trans('api.user_inactive')]);
                    } else {
                        $userId = $userDetail->id;

                        $lastLoginUpdate = [
                            'api_token' => Helper::randomString(50),
                            'last_login' => date('Y-m-d H:i:s')
                        ];
                        User::where('id', $userId)->update($lastLoginUpdate);

                        $userData = Helper::userDetail($userId);
                        $response = Helper::replaceNullWithEmptyString($userData);

                        Helper::log('API user login : finish');
                        DB::commit();
                        return response()->json(['status' => 200, 'show' => false, 'message' => trans('api.login_success'), 'data' => $response]);
                    }
                } else {
                    return response()->json(["status" => 404, 'show' => true, "message" => trans('api.username_pin_not_exists')]);
                }
            }
        } catch (\Exception $exception) {

            DB::rollBack();
            Helper::log('API user login : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }
    public function verifyPIN(Request $request, $locale) {

        DB::beginTransaction();
        Helper::log('API user login : start');
        try {

            App::setLocale($locale);
            $branchId = $request->branch_id;
            $userPin = $request->user_pin;
            $deviceType = $request->device_type;
            $deviceId = $request->device_id;
            $terminalId = $request->terminal_id;
            if (empty($userPin)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.pin_required')]);
            } elseif (empty($deviceType)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.device_type_required')]);
            } elseif (empty($terminalId)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.terminal_id_required')]);
            } else {
                $userList = UserBranch::where('branch_id',$branchId)->get();
                $userData = User::whereIn('user_id', $userList)->where('user_pin', $userPin)->first();
                $response = Helper::replaceNullWithEmptyString($userData);
                Log::debug($response);
                return response()->json(['status' => 200, 'show' => false, 'message' => trans('api.login_success'), 'data' => $response]);
            }
            Helper::log('API user login : finish');
            DB::commit();
        } catch (\Exception $exception) {

            DB::rollBack();
            Helper::log('API user login : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }
    /* Get User Profile Details*/
    public function profile(Request $request, $locale)
    {

        Helper::log('User Profile : start');
        DB::beginTransaction();
        try {

            App::setLocale($locale);
            $apiToken = $request->api_token;
            if (empty($apiToken)) {
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.api_token_required')]);
            } else {
                $user = Auth::user();

                if (Auth::user()) {
                    $userId = Auth::user()->id;

                    $userData = Helper::userDetail($userId);

                    $response = Helper::replaceNullWithEmptyString($userData);
                    Helper::log('User Profile : finish');
                    DB::commit();
                    return response()->json(['status' => 200, 'show' => false, 'message' => trans('api.success'), 'data' => $response]);
                } else {
                    return response()->json(["status" => 404, 'show' => true, "message" => trans('api.user_not_allow')]);
                }
            }

        } catch (\Exception $exception) {
            Helper::log('User Profile : exception');
            Helper::log($exception);
            return response()->json([
                'status' => 500,
                'show' => true,
                'message' => trans('api.ooops')
            ]);
        }
    }

    /**
     * Verify Terminal Key
     */
    public function verifyTerminalKey(Request $request, $locale)
    {
        Helper::log('Terminal key verify: start');
        DB::beginTransaction();
        try {
            App::setLocale($locale);
            $key = $request->terminal_key;
            $ter_device_id = $request->ter_device_id;
            $ter_device_token = $request->ter_device_token;

            if (empty($ter_device_id)) {
                Helper::log('Terminal key verify: device id required');
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.device_id_required')]);
            } elseif (empty($ter_device_token)) {
                Helper::log('Attendance Terminal key verify: device token required');
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.device_token_required')]);
            } elseif (empty($key)) {
                Helper::log('Terminal key verify: key required');
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.key_required')]);
            } else {
                $terminalData = Terminal::where(DB::raw('BINARY terminal_key'), $key)->first();
                if (!empty($terminalData)) {
                    $terminalId = $terminalData->terminal_id;
                    $branchId = $terminalData->branch_id;
					$terminal_device_id = $terminalData->terminal_device_id;
					if(!empty($terminal_device_id)){
						if($terminal_device_id == $ter_device_id){
                            $updateData = [
                                'terminal_device_id' => $ter_device_id,
                                'terminal_device_token' => $ter_device_token,
                                'terminal_verified_at' => config('constants.date_time'),
                            ];
                            Terminal::where('terminal_key', $key)->update($updateData);
                            DB::commit();
                            Helper::log('Terminal key verify: finish');
                            return response()->json(['status' => 200, 'show' => true, 'message' => trans('api.success'), 'terminal_id' => $terminalId, 'branch_id' => $branchId]);
                        } else {
                            Helper::log('Terminal key verify: Device invalid');
                            return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.device_invalid'), 'terminal_id' => 0, 'branch_id' => 0]);
                        }
					} else {
						$updateData = [
							'terminal_device_id' => $ter_device_id,
							'terminal_device_token' => $ter_device_token,
							'terminal_verified_at' => config('constants.date_time'),
						];
						Terminal::where('terminal_key',$key)->update($updateData);
						DB::commit();
						Helper::log('Terminal key verify: finish');
						return response()->json(['status' => 200, 'show' => true, 'message' => trans('api.success'), 'terminal_id' => $terminalId, 'branch_id' => $branchId]);
					}
                } else {
                    Helper::log('Terminal key verify: key invalid');
                    return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.key_invalid'), 'terminal_id' => 0, 'branch_id' => 0]);
                }
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Terminal key verify : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops'), 'terminal_id' => 0, 'branch_id' => 0]);
        }
    }

}
