<?php

namespace App\Http\Controllers\Api;

use App\Models\Assets;
use App\Models\Attendance;
use App\Models\Attributes;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Helper;
use App\Models\Kitchen;
use App\Models\Modifier;
use App\Models\Payment;
use App\Models\PriceType;
use App\Models\Printer;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductModifier;
use App\Models\ProductStoreInventory;
use App\Models\Roles;
use App\Models\Shift;
use App\Models\Terminal;
use App\Models\UserBranch;
use App\Models\Voucher;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Schema;

class AttendanceController extends Controller
{
    protected $env = 'local';

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

            Helper::log('API configs : finish');
            return response()->json(['status' => 200, 'show' => false, 'message' => trans('api.success'), 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('API configs : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /**
     * Verify Terminal Key
     */
    public function verifyAttendanceTerminalKey(Request $request, $locale)
    {
        Helper::log('Attendance Terminal key verify: start');
        DB::beginTransaction();
        try {
            App::setLocale($locale);
            $key = $request->terminal_key;
            $ter_device_id = $request->ter_device_id;
            $ter_device_token = $request->ter_device_token;

            if (empty($ter_device_id)) {
                Helper::log('Attendance Terminal key verify: device id required');
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.device_id_required')]);
            } elseif (empty($ter_device_token)) {
                Helper::log('Attendance Terminal key verify: device token required');
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.device_token_required')]);
            } elseif (empty($key)) {
                Helper::log('Attendance Terminal key verify: key required');
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.key_required')]);
            } else {
                $terminalData = Terminal::where('terminal_key', $key)->where('terminal_type',3)->withTrashed()->first();
                if (!empty($terminalData)) {
                    $terminalId = $terminalData->terminal_id;
                    $branchId = $terminalData->branch_id;
                    $updateData = [
                        'terminal_device_id' => $ter_device_id,
                        'terminal_device_token' => $ter_device_token,
                        'terminal_verified_at' => config('constants.date_time'),
                    ];
                    Terminal::where('terminal_key',$key)->update($updateData);
                    Helper::saveTerminalLog($terminalId, $branchId, 'Verify Terminal', 'Attendance Terminal verify done', date('Y-m-d'), date('H:i:s'), 'terminal');
                    DB::commit();
                    Helper::log('Attendance Terminal key verify: finish');
                    return response()->json(['status' => 200, 'show' => true, 'message' => trans('api.success'), 'terminal_id' => $terminalId, 'branch_id' => $branchId]);
                } else {
                    Helper::log('Attendance Terminal key verify: key invalid');
                    return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.key_invalid'), 'terminal_id' => 0, 'branch_id' => 0]);
                }
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Attendance Terminal key verify : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops'), 'terminal_id' => 0, 'branch_id' => 0]);
        }
    }

    /*
     * @method  : Role User TableData
     * @params  : datetime, branch id, terminal id
     */

    public function syncRoleUserTableData(Request $request, $locale)
    {
        Helper::log('Role User Table Synch : Start');
        App::setLocale($locale);
        try {
            $serverdatetime = $request->serverdatetime;
            $branchId = $request->branch_id;
            $terminalId = $request->terminal_id;

            if (empty($terminalId)) {
                Helper::log('Role User Table Synch : Terminal Id required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } elseif (empty($serverdatetime)) {
                Helper::log('Role User Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else {
                //GET SET GO...
                $timeStart = microtime(true);
                //$timezonetype = "COMPANY CONFIGURATION";
                $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
                $response['serverdatetime'] = date('Y-m-d h:i:s');

                $terminalData = Terminal::where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;

                //UserBranch Data collection
                $loadUserBranch = UserBranch::where('branch_id',$branchId)->get()->toArray();
                $response['user_branch'] = $loadUserBranch;

                //User Data collection
                $userIds = UserBranch::where('branch_id',$branchId)->where('status',1)->select('user_id')->get();
                $loadUsers = User::withTrashed()->whereIn('id',$userIds)->get()->toArray();

                $response['users'] = $loadUsers;

                //Role Data collection
                $loadRoles = Roles::where(['role_status' => 1])->get()->toArray();
                $response['role'] = $loadRoles;

                //Attendance Data collection
                $loadAttendance = Attendance::where(['branch_id' => $branchId])
                    ->select('*',DB::raw('DATE_FORMAT(in_out_datetime, "%d-%m-%Y %h:%i:%s") as in_out_datetime'),DB::raw('DATE_FORMAT(created_date, "%d-%m-%Y %h:%i:%s") as created_date'),
                        DB::raw('DATE_FORMAT(updated_date, "%d-%m-%Y %h:%i:%s") as updated_date'),DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y %h:%i:%s") as created_at'),DB::raw('DATE_FORMAT(updated_at, "%d-%m-%Y %h:%i:%s") as updated_at'))
                    ->get()->toArray();
                $response['attendance'] = $loadAttendance;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('Role User Table Synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Attendance SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'users');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Role User Table Synch : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops'), 'terminal_id' => 0, 'branch_id' => 0]);
        }
    }

    /*
     * @method  : Sync TableData From App
     * @params  : datetime, table
     */

    public function syncAttendanceTableData(Request $request, $locale)
    {
        Helper::log('Attendance Table Synch : Start');
        App::setLocale($locale);
        try {
            $getAttendance = $request->attendance;
            $terminalId = $request->terminal_id;
            $branchId = $request->branch_id;

            if (empty($getAttendance)) {
                Helper::log('Attendance Table Synch : parameters required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_required_parameter')]);
            } else {
                if (\GuzzleHttp\json_decode($getAttendance)) {
                    //GET SET GO...
                    $timeStart = microtime(true);
                    $getAttendanceArray = \GuzzleHttp\json_decode($getAttendance, true);  // convert to array

                    if (is_array($getAttendanceArray)) {  // valid array

                        $attendance = new Attendance();
                        $pushOrders = [];

                        foreach ($getAttendanceArray as $setAttendanceArray) {
                            $isExistingOrder = array_key_exists("server_id", $setAttendanceArray);

                            if ($isExistingOrder) {
                                $serverId = $setAttendanceArray['server_id'];
                                $attendance = Attendance::where('id',$serverId)->first();
                                if (empty($attendance)) {
                                    $attendance = new Attendance();

                                    $attendance->localID = $setAttendanceArray['localID'];
                                    $attendance->employee_id = $setAttendanceArray['employee_id'];
                                    $attendance->branch_id = $setAttendanceArray['branch_id'];
                                    $attendance->terminal_id = $setAttendanceArray['terminal_id'];
                                    $attendance->in_out = $setAttendanceArray['in_out'];
                                    $attendance->in_out_datetime = date('Y-m-d H:i:s', strtotime($setAttendanceArray['in_out_datetime']));
                                    $attendance->created_date = date('Y-m-d H:i:s', strtotime($setAttendanceArray['created_date']));
                                    $attendance->created_by = $setAttendanceArray['created_by'];
                                    $attendance->updated_date = ($setAttendanceArray['updated_date']) ? date('Y-m-d H:i:s', strtotime($setAttendanceArray['updated_date'])) : date('Y-m-d H:i:s', strtotime($setAttendanceArray['created_date']));
                                    $attendance->sync = 1;
                                    $attendance->created_at = date('Y-m-d H:i:s');
                                    $attendance->updated_at = date('Y-m-d H:i:s');

                                    $attendance = Attendance::create($attendance->toArray());
                                    $attendanceId = $attendance->id;  //get order id
                                    $setAttendanceArray['server_id'] = $attendanceId;
                                    $pushOrders[] = $attendanceId;
                                } else {
                                    $attendance->localID = $setAttendanceArray['localID'];
                                    $attendance->employee_id = $setAttendanceArray['employee_id'];
                                    $attendance->branch_id = $setAttendanceArray['branch_id'];
                                    $attendance->terminal_id = $setAttendanceArray['terminal_id'];
                                    $attendance->in_out = $setAttendanceArray['in_out'];
                                    $attendance->in_out_datetime = date('Y-m-d H:i:s', strtotime($setAttendanceArray['in_out_datetime']));
                                    $attendance->created_date = date('Y-m-d H:i:s', strtotime($setAttendanceArray['created_date']));
                                    $attendance->created_by = $setAttendanceArray['created_by'];
                                    $attendance->updated_date = ($setAttendanceArray['updated_date']) ? date('Y-m-d H:i:s', strtotime($setAttendanceArray['updated_date'])) : date('Y-m-d H:i:s', strtotime($setAttendanceArray['created_date']));
                                    $attendance->server_id = $setAttendanceArray['server_id'];
                                    $attendance->sync = $setAttendanceArray['sync'];
                                    $attendance->created_at = date('Y-m-d H:i:s');
                                    $attendance->updated_at = date('Y-m-d H:i:s');

                                    Attendance::where('id',$serverId)->update($attendance->toArray());
                                    $attendanceId = $attendance->id;  //get order id
                                    $setAttendanceArray['server_id'] = $serverId;
                                    $pushOrders[] = $attendanceId;
                                }
                            } else {
                                $attendance = new Attendance();

                                $attendance->localID = $setAttendanceArray['localID'];
                                $attendance->employee_id = $setAttendanceArray['employee_id'];
                                $attendance->branch_id = $setAttendanceArray['branch_id'];
                                $attendance->terminal_id = $setAttendanceArray['terminal_id'];
                                $attendance->in_out = $setAttendanceArray['in_out'];
                                $attendance->in_out_datetime = date('Y-m-d H:i:s', strtotime($setAttendanceArray['in_out_datetime']));
                                $attendance->created_date = date('Y-m-d H:i:s', strtotime($setAttendanceArray['created_date']));
                                $attendance->created_by = $setAttendanceArray['created_by'];
                                $attendance->updated_date = ($setAttendanceArray['updated_date']) ? date('Y-m-d H:i:s', strtotime($setAttendanceArray['updated_date'])) : date('Y-m-d H:i:s', strtotime($setAttendanceArray['created_date']));
                                $attendance->sync = 1;
                                $attendance->created_at = date('Y-m-d H:i:s');
                                $attendance->updated_at = date('Y-m-d H:i:s');

                                $attendance = Attendance::create($attendance->toArray());
                                $attendanceId = $attendance->id;  //get order id
                                $setAttendanceArray['server_id'] = $attendanceId;
                                $pushOrders[] = $attendanceId;
                            }

                        }
                        Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Attendance data SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'attendance');
                        DB::commit();
                        $loadAttendanceInfo = $this->attendanceInfo($pushOrders);
                        $response['attendance'] = $loadAttendanceInfo;
                        Helper::log('Attendance Table Synch : Attendance Created');
                        $message = trans('api.attendance_created');
                        return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'attendance' => $loadAttendanceInfo]);
                    } else {
                        DB::rollBack();
                        Helper::log('Attendance Table Synch : fail json to array converting');
                        Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Attendance data SynchronizeAppdata faid json to array conversation', date('Y-m-d'), date('H:i:s'), 'attendance');
                        $message = trans('api.faid_json_to_array');
                        return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                    }
                } else {
                    DB::rollBack();
                    Helper::log('Attendance Table Synch : Invalid Json String');
                    $message = trans('api.invalid_json_string');
                    Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'Create Attendance data SynchronizeAppdata invalid json string', date('Y-m-d'), date('H:i:s'), 'attendance');
                    return response()->json(['status' => 422, 'show' => true, 'message' => $message]);
                }
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Attendance Table Synch : exception');
            Helper::log($exception);
            Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', $exception->getMessage(), date('Y-m-d'), date('H:i:s'), 'attendance');
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method  : Attendance Info From App
     * @params  : datetime, table
     */

    public function attendanceInfo($attendanceIds = [])
    {
        Helper::log('Attendance Info : start');
        try {
            if (empty($attendanceIds)) {
                Helper::log('Attendance Info : enter required parameter');
                return response()->json(['status' => 422, 'show' => true, 'message' => trans('api.enter_required_parameter')]);
            }
            $pushOrder = [];
            foreach ($attendanceIds as $attendanceId) {
                $loadAttendance = Attendance::where(['id' => $attendanceId])
                    ->select(['*', 'id as server_id', 'in_out',DB::raw('DATE_FORMAT(in_out_datetime, "%d-%m-%Y %h:%i:%s") as in_out_datetime'),DB::raw('DATE_FORMAT(created_date, "%d-%m-%Y %h:%i:%s") as created_date'),
                        DB::raw('DATE_FORMAT(updated_date, "%d-%m-%Y %h:%i:%s") as updated_date'),DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y %h:%i:%s") as created_at'),DB::raw('DATE_FORMAT(updated_at, "%d-%m-%Y %h:%i:%s") as updated_at')])
                    ->first();
                $pushOrder[] = $loadAttendance;
            }
            return $pushOrder;
            /*$message = trans('api.attendance_info');
            return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $pushOrder]);*/
        } catch (\Exception $exception) {
            Helper::log('Attendance Info : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }


    /*
     * @method  : TableData
     * @params  : datetime, table
     */

    public function singleTableData(Request $request, $locale)
    {
        Helper::log('Table Synch : Start');
        App::setLocale($locale);
        try {

            $serverdatetime = $request->serverdatetime;
            $table = $request->table;
            //$branchId = $request->branch_id;
            $terminalId = $request->terminal_id;

            if (empty($table)) {
                Helper::log('Table Synch : Table required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.table_name_required')]);
            } elseif (empty($serverdatetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } /*elseif (empty($branchId)) {
                Helper::log('Table Synch : Branch required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.branch_id_required')]);
            }*/ elseif (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                // chekc table name is exist or not
                $timeZone = Helper::getSettingValue('timezone');//config('app.timezone');
                $isExistsTable = Schema::hasTable($table);//DB::getSchemaBuilder()->hasTable($table);
                if (!$isExistsTable) {
                    Helper::log('Table Synch : Table not exists');
                    return response()->json(['status' => 422, 'show' => true, "message" => trans('api.table_not_exists')]);
                }

                $d = new \DateTime('', new \DateTimeZone($timeZone));
                $response['postdatetime'] = $serverdatetime;
                $serverdatetime = $d->format('Y-m-d H:i:s');

                $force_update = false;
                if ($force_update) {
                    $interval = new \DateInterval('P1D'); // 1 day
                    $serverdatetime = $d->sub($interval)->format('Y-m-d H:i:s');
                }

                $terminalData = Terminal::where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;

                $response['timezone'] = $timeZone;
                $response['serverdatetime'] = $serverdatetime;
                $response['branch_id'] = $branchId;
                $response['terminal_id'] = $terminalId;

                switch ($table) {
                    case "branch":
                        return $this->loadBranch($response);
                        break;
                    case "printer":
                        return $this->loadPrinter($response);
                        break;
                    case "category":
                        return $this->loadCategory($response);
                        break;
                    case "product":
                        return $this->loadProduct($response);
                        break;
                    case "product_store_inventory":
                        return $this->loadProductStoreInventory($response);
                        break;
                    case "product_attribute":
                        return $this->loadProductAttribute($response);
                        break;
                    case "product_modifier":
                        return $this->loadProductModifier($response);
                        break;
                    case "kitchen_department":
                        return $this->loadKitchenDepartment($response);
                        break;
                    case "asset":
                        return $this->loadProductimage($response);
                        break;
                    case "product_category":
                        return $this->loadProductCategory($response);
                        break;
                    case "role":
                        return $this->loadRole($response);
                        break;
                    case "users":
                        return $this->loadUser($response);
                        break;
                    case "payment":
                        return $this->loadPayment($response);
                        break;
                    case "price_type":
                        return $this->loadPriceType($response);
                        break;
                    case "attributes":
                        return $this->loadAttributes($response);
                        break;
                    case "modifier":
                        return $this->loadModifier($response);
                        break;
                    case "voucher":
                        return $this->loadVoucher($response);
                        break;
                    case "shift":
                        return $this->loadShift($response);
                        break;
                    case "attendance":
                        return $this->loadAttendance($response);
                        break;
                    case "user_branch":
                        return $this->loadUserBranch($response);
                        break;
                    default:
                        return response()->json(['status' => 422, 'show' => true, "message" => trans('api.enter_valid_table_name')]);
                }

            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Table Synch : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Branch
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadBranch($response)
    {
        try {
            // Branch Data Collection
            // $uploadsPath = str_replace('api/', '/backend/web/uploads/', Url::home(true));
            $pushBranch = [];
            $loadBranch = Branch::withTrashed()->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            if (!empty($loadBranch)) {
                foreach ($loadBranch as $branchInfo) {
                    $branchInfo['branch_image_base64'] = '';
                    if ($branchInfo['branch_banner'] != '') {
                        $file = asset($branchInfo['branch_banner']);
                        $branchInfo['branch_image_base64'] = $this->getImageDataFromUrl($file);
                    }
                    $pushBranch[] = $branchInfo;
                }
            }
            $response['branch'] = Helper::replaceNullWithEmptyString($pushBranch);
            $message = trans('api.retrive_branch_updated_data');

            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table branch Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Printer
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadPrinter($response)
    {
        try {

            $loadPrinter = Printer::withTrashed()->where('updated_at', '>=', $response['postdatetime'])->where('branch_id',$response['branch_id'])->get()->toArray();
            $response['printer'] = Helper::replaceNullWithEmptyString($loadPrinter);
            $message = trans('api.retrive_printer_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table printer Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Category
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadCategory($response)
    {
        try {

            $loadCategory = Category::withTrashed()->leftjoin('category_branch','category_branch.category_id','category.category_id')
                ->where('category.updated_at', '>=', $response['postdatetime'])
                ->where('category_branch.branch_id', $response['branch_id'])

                ->select('category.*')
                ->get()->toArray();
            $response['category'] = Helper::replaceNullWithEmptyString($loadCategory);
            $message = trans('api.retrive_category_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table category Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Category
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadProductCategory($response)
    {
        try {

            $loadProductsCategories = ProductCategory::where('branch_id', $response['branch_id'])->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['product_category'] = Helper::replaceNullWithEmptyString($loadProductsCategories);
            $message = trans('api.retrive_product_category_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table product category Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Product
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadProduct($response)
    {
        try {

            $loadProduct = Product::withTrashed()->leftjoin('product_branch','product_branch.product_id','product.product_id')
                ->where('product_branch.updated_at', '>=', $response['postdatetime'])
                ->where('product_branch.branch_id', $response['branch_id'])

                ->select('product.*')
                ->get()->toArray();
            $response['product'] = Helper::replaceNullWithEmptyString($loadProduct);
            $message = trans('api.retrive_product_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table product Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Product store Inventory
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadProductStoreInventory($response)
    {
        try {

            $loadProductInventory = ProductStoreInventory::where('branch_id',$response['branch_id'])->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['product_store_inventory'] = Helper::replaceNullWithEmptyString($loadProductInventory);
            $message = trans('api.retrive_product_store_inventory_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table product store inventory Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Product Attribute
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadProductAttribute($response)
    {
        try {

            $loadProductAttribute = ProductAttribute::leftjoin('product_branch','product_branch.product_id','product_attribute.product_id')
                ->where('product_attribute.updated_at', '>=', $response['postdatetime'])
                ->where('product_branch.branch_id', $response['branch_id'])
                ->select('product_attribute.*')
                ->get()->toArray();
            $response['product_attribute'] = Helper::replaceNullWithEmptyString($loadProductAttribute);
            $message = trans('api.retrive_product_attribute_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table product attribute Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Product Modifier
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadProductModifier($response)
    {
        try {

            $loadProductModifier = ProductModifier::leftjoin('product_branch','product_branch.product_id','product_modifier.product_id')
                ->where('product_modifier.updated_at', '>=', $response['postdatetime'])
                ->where('product_branch.branch_id', $response['branch_id'])
                ->select('product_modifier.*')
                ->get()->toArray();
            $response['product_modifier'] = Helper::replaceNullWithEmptyString($loadProductModifier);
            $message = trans('api.retrive_product_modifier_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table product modifier Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : KitchenDepartment
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadKitchenDepartment($response)
    {
        try {

            $loadKitchen = Kitchen::withTrashed()->where('branch_id',$response['branch_id'])->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['kitchen_department'] = Helper::replaceNullWithEmptyString($loadKitchen);
            $message = trans('api.retrive_kitchen_department_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table kitchen department Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Payment
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadPayment($response)
    {
        try {

            $loadPayment = Payment::where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['payment'] = Helper::replaceNullWithEmptyString($loadPayment);
            $message = trans('api.retrive_payment_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table payment Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Price Type
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadPriceType($response)
    {
        try {

            $loadPriceType = PriceType::withTrashed()->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['price_type'] = Helper::replaceNullWithEmptyString($loadPriceType);
            $message = trans('api.retrive_price_type_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table price type Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Attributes
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadAttributes($response)
    {
        try {
            $loadAttributes = Attributes::where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['attributes'] = Helper::replaceNullWithEmptyString($loadAttributes);
            $message = trans('api.retrive_attributes_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table attributes Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Modifier
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadModifier($response)
    {
        try {
            $loadModifier = Modifier::withTrashed()->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['modifier'] = Helper::replaceNullWithEmptyString($loadModifier);
            $message = trans('api.retrive_modifier_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table modifier Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Voucher
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadVoucher($response)
    {
        try {
            $loadVoucher = Voucher::withTrashed()->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['voucher'] = Helper::replaceNullWithEmptyString($loadVoucher);
            $message = trans('api.retrive_voucher_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table voucher Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Shift
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadShift($response)
    {
        try {
            $loadShift = Shift::where('branch_id',$response['branch_id'])->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['shift'] = Helper::replaceNullWithEmptyString($loadShift);
            $message = trans('api.retrive_shift_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table Shift Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : UserBranch
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadUserBranch($response)
    {
        try {
            $loadAttendance = UserBranch::where(['branch_id' => $response['branch_id']])
                ->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['user_branch'] = $loadAttendance;
            $message = trans('api.retrive_user_branch_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table Attendance Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Attendance
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadAttendance($response)
    {
        try {
            $loadAttendance = Attendance::where(['branch_id' => $response['branch_id']])
                ->where('updated_at', '>=', $response['postdatetime'])
                ->select('*',DB::raw('DATE_FORMAT(in_out_datetime, "%d-%m-%Y %h:%i:%s") as in_out_datetime'),DB::raw('DATE_FORMAT(created_date, "%d-%m-%Y %h:%i:%s") as created_date'),
                    DB::raw('DATE_FORMAT(updated_date, "%d-%m-%Y %h:%i:%s") as updated_date'),DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y %h:%i:%s") as created_at'),DB::raw('DATE_FORMAT(updated_at, "%d-%m-%Y %h:%i:%s") as updated_at'))
                ->get()->toArray();
            $response['attendance'] = $loadAttendance;
            $message = trans('api.retrive_attendance_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table Attendance Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Product store Inventory
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadProductImage($response)
    {
        try {
            $pushImage = [];
            $loadProductimage = Assets::where('updated_at', '>=', $response['postdatetime'])->get()->toArray();
            if (!empty($loadProductimage)) {
                foreach ($loadProductimage as $imageInfo) {
                    $imageInfo['base64'] = '';
                    if ($imageInfo['asset_path'] != '') {
                        $file = asset($imageInfo['asset_path']);
                        $imageInfo['base64'] = $this->getImageDataFromUrl($file);
                    }
                    $pushImage[] = $imageInfo;
                }
            }
            $response['product_image'] = Helper::replaceNullWithEmptyString($pushImage);
            $message = trans('api.retrive_product_image_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table product image Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Role
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadRole($response)
    {
        try {
            $loadRole = Roles::whereNotIn('role_id',Roles::$notIn)->where('role_updated_at', '>=', $response['postdatetime'])->get()->toArray();
            $response['role'] = Helper::replaceNullWithEmptyString($loadRole);
            $message = trans('api.retrive_role_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table Role Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Role
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadUser($response)
    {
        try {
            /*$loadUser = User::select(['id', 'name', 'email', 'username', 'country_code', 'mobile', 'user_pin', 'role', 'status', 'last_login', 'updated_at', 'updated_by'])
                ->where('updated_at', '>=', $response['postdatetime'])->get()->toArray();*/
            $loadUser = User::withTrashed()->leftjoin('user_branch','user_branch.user_id','users.id')
                ->where('users.updated_at', '>=', $response['postdatetime'])
                ->where(['users.status' => 1, 'user_branch.branch_id' => $response['branch_id']])
                ->select('users.*')
                ->get()->toArray();
            $response['users'] = $loadUser;
            $message = trans('api.retrive_user_updated_data');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table User Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Appdata
     * @params   : datetime, branchId
     * @respose  : Json updated data secound time
     */

    public static function getImageDataFromUrl($url)
    {

        $urlParts = pathinfo($url);
        $extension = $urlParts['extension'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($response);
        return $base64;
    }


}
