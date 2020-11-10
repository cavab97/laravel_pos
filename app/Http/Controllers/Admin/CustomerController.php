<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomerAddress;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\RolePermission;
use App\Models\Roles;
use App\Models\Branch;
use App\Models\Customer;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use function Symfony\Component\String\u;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_customer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.customer.index');
    }

    /**
     * Pagination for backend customer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];

            $defaultCondition = 'uuid != ""';
            if (!empty($search)) {
                $search = Helper::string_sanitize($search);
                $defaultCondition .= " AND ( name LIKE '%$search%' OR email LIKE '%$search%' OR mobile LIKE '%$search%' ) ";
            }

            $name = $request->input('name', null);
            if ($name != null) {
                $name = Helper::string_sanitize($name);
                $defaultCondition .= " AND `name` LIKE '%$name%' ";
            }

            $mobile = $request->input('mobile', null);
            if ($mobile != null) {
                $defaultCondition .= " AND `mobile` LIKE '%$mobile%' ";
            }
            $email = $request->input('email', null);
            if ($email != null) {
                $defaultCondition .= " AND `email` LIKE '%$email%' ";
            }

            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $from = isset($from_date) ? (date('Y-m-d', strtotime($from_date))) : null;
            $to = isset($to_date) ? (date('Y-m-d', strtotime($to_date))) : null;

            if (empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') <= '" . $to . "'";
            }
            if (!empty($from) && empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') >= '" . $from . "'";
            }
            if (!empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
            }
            $cusCount = Customer::whereRaw($defaultCondition)
                ->count();
            $cusList = Customer::whereRaw($defaultCondition)
                ->select(
                    'customer_id', 'uuid', 'name', 'email', 'mobile', 'profile', 'status', 'last_login', 'created_at')
                ->orderBy($order_by_field, $order_by)
                ->limit($page_length)
                ->offset($start)
                ->get()->toArray();
            foreach ($cusList as $key => $value) {
                $cusList[$key]['created_at'] = date('Y-m-d H:i A', strtotime($value['created_at']));

                if(empty($value['profile']) || !file_exists(public_path($value['profile']))){
                    $cusList[$key]['profile'] = config('constants.default_user');
                }
            }

            return response()->json([
                "aaData" => $cusList,
                "iTotalDisplayRecords" => $cusCount,
                "iTotalRecords" => $cusCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('User pagination exception');
            Helper::log($exception);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_customer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $roleList = Roles::whereNotIn('role_id', Roles::$notIn)->get();
        return view('backend.customer.create', compact('roleList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Languages::setBackLang();
        Helper::log('Customer store : start');
        DB::beginTransaction();
        try {
            $name = $request->name;
            $username = $request->username;
            $mobile = $request->mobile;
            $email = $request->email;
            $status = $request->status;
            $password = '';
            if ($request->password) {
                $password = Hash::make($request->password);
            }

            $checkMobile = Customer::where('mobile', $mobile)->where('role', config('constants.roles')["customer"])->count();
            $checkEmail = Customer::where('email', $email)->where('role', config('constants.roles')["customer"])->count();
            $checkUsername = Customer::where('username', $username)->where('role', config('constants.roles')["customer"])->count();

            if ($checkEmail > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.email_exists')]);
            } elseif ($checkMobile > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/customer.mobile_exists')]);
            } elseif ($checkUsername > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.username_exists')]);
            } else {

                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'name' => $name,
                    'email' => $email,
                    'role' => config('constants.roles')["customer"],
                    'username' => $username,
                    'password' => $password,
                    'mobile' => $mobile,
                    'status' => $status,
                    'is_admin' => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];

                if ($file = $request->file('profile')) {
                    $customerFolder = $this->createDirectory('customers');
                    $fileName = time() . '.' . $file->getClientOriginalExtension();
                    $file->move("$customerFolder", $fileName);
                    chmod($customerFolder . $fileName, 0777);
                    $profile = 'uploads/customers/' . $fileName;
                    $insertData['profile'] = $profile;
                } else {
					$profile = config('constants.default_user');
					$insertData['profile'] = $profile;
				}
				
                $customer = Customer::create($insertData);

                DB::commit();
                $subject = 'Customer registration';
                //  Helper::sendMailUser($request, 'emails.registration', 'Customer created', $insertData['email'], $subject);

                Helper::saveLogAction('1', 'Customers', 'Store', 'Add new Customers ' . $customer->uuid, Auth::user()->id);
                Helper::log('Customer store : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information'), 'uuid' => $customer->uuid]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Customer  store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Customers', 'Store', 'Add new Customers ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_save_information')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('view_customer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $customerData = Customer::leftjoin('role', 'role.role_id', 'customer.role')->where('customer.uuid', $uuid)
            ->first();
        $customerData->updated_name = '';
        $created_name = User::where('id', $customerData->updated_by)
            ->select('users.name')
            ->first();
        if (!empty($created_name)) {
            $customerData->updated_name = $created_name->name;
        }

        if (!empty($customerData)) {
            return view('backend.customer.view', compact('customerData'));
        } else {
            return redirect()->route('admin.customer.index')->with('error', trans('backend/common.oops'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('edit_customer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $customerData = Customer::where('uuid', $uuid)->first();

        $defCustomerAddress = CustomerAddress::where('user_id', $customerData->customer_id)->where('is_default', 1)->first();
        if ($defCustomerAddress) {
            $customerData->address_line1 = $defCustomerAddress->address_line1;
            $customerData->address_line2 = $defCustomerAddress->address_line2;
            $customerData->is_default = $defCustomerAddress->is_default;
            $customerData->longitude = $defCustomerAddress->longitude;
            $customerData->latitude = $defCustomerAddress->latitude;
            $customerData->status = $defCustomerAddress->status;
        }

        $customerAddress = CustomerAddress::where('user_id', $customerData->customer_id)->orderBy('is_default', 'DESC')->get();

        return view('backend.customer.edit', compact('customerData', 'customerAddress'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        Languages::setBackLang();
        Helper::log('Customer Update : start');
        DB::beginTransaction();
        try {
            $name = $request->name;
            $username = $request->username;
            $mobile = $request->mobile;
            $email = $request->email;
            $status = $request->status;
            $password = '';
            if ($request->password) {
                $password = Hash::make($request->password);
            }

            $checkMobile = Customer::where('mobile', $mobile)->where('role', config('constants.roles')["customer"])->where('uuid', '!=', $uuid)->count();
            $checkEmail = Customer::where('email', $email)->where('role', config('constants.roles')["customer"])->where('uuid', '!=', $uuid)->count();
            $checkUsername = Customer::where('username', $username)->where('role', config('constants.roles')["customer"])->where('uuid', '!=', $uuid)->count();

            if ($checkEmail > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.email_exists')]);
            } elseif ($checkMobile > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/customer.mobile_exists')]);
            } elseif ($checkUsername > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/users.username_exists')]);
            } else {

                $customerData = Customer::where('uuid', $uuid)->first();
                $customerId = $customerData->customer_id;

                $updateData = [
                    'name' => $name,
                    'email' => $email,
                    'username' => $username,
                    'mobile' => $mobile,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                if ($file = $request->file('profile')) {
                    $customerFolder = $this->createDirectory('customers');
                    $fileName = time() . '.' . $file->getClientOriginalExtension();
                    $file->move("$customerFolder", $fileName);
                    chmod($customerFolder . $fileName, 0777);
                    $profile = 'uploads/customers/' . $fileName;
                    $updateData['profile'] = $profile;
                }
                $password = $request->password;
                if ($password) {
                    $updateData['password'] = Hash::make($password);
                }

                Customer::where('uuid', $uuid)->update($updateData);
                DB::commit();

                Helper::saveLogAction('1', 'Customers', 'Update', 'Update Customers ' . $uuid, Auth::user()->id);
                Helper::log('Customer Update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Customer Update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Customers', 'Update', 'Update Customers ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_customer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.customer.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        DB::beginTransaction();
        Helper::log('Customer delete : start');
        try {
            $userId = Auth::user()->id;
            Customer::where('uuid', $uuid)->update([
                'deleted_at' => config('constants.date_time'),
                'deleted_by' => $userId,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ]);
            Helper::log('Customer delete : finish');
            DB::commit();
            Helper::saveLogAction('1', 'Customers', 'Destroy', 'Destroy Customers ' . $uuid, Auth::user()->id);
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Customer delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Customers', 'Destroy', 'Destroy Customers ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    /**
     * Store a newly created Address resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addressStore(Request $request)
    {
        Languages::setBackLang();
        Helper::log('Customer Address store : start');
        DB::beginTransaction();
        try {
            /* Address Data */
            $address_line1 = $request->address_line1;
            $address_line2 = $request->address_line2;
            $is_default = $request->is_default;
            $longitude = $request->longitude;
            $latitude = $request->latitude;
            $status = $request->status;

            $user_uuid = $request->user_uuid;
            $user = Customer::where('uuid', $user_uuid)->first();
            $user_id = $user->customer_id;

            CustomerAddress::where('user_id', $user_id)->delete();

            $address_line1 = $request->address_line1;
            if ($address_line1) {
                foreach ($request->address_line1 as $key => $value) {

                    $insertAddressData = [
                        'uuid' => Helper::getUuid(),
                        'user_id' => $user_id,
                        'address_line1' => $address_line1[$key],
                        'address_line2' => $address_line2[$key],
                        'is_default' => $is_default[$key],
                        'longitude' => $longitude[$key],
                        'latitude' => $latitude[$key],
                        'status' => $status[$key],
                        'updated_at' => config('constants.date_time'),
                        'updated_by' => Auth::user()->id
                    ];
                    $cusAdd = CustomerAddress::create($insertAddressData);
                }
            }
            DB::commit();

            Helper::saveLogAction('1', 'Customers Address', 'Customers Address Store', 'Add new Customers Address ' . $cusAdd->uuid, Auth::user()->id);
            Helper::log('Customer Address store : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Customer Address store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Customers Address', 'Customers Address Update', 'Add new Customers Address ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_save_information')]);
        }
    }

    /**
     * Update a created Address resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addressUpdate(Request $request, $uuid)
    {
        Languages::setBackLang();
        Helper::log('Customer Address update : start');
        DB::beginTransaction();
        try {

            /* Address Data */
            $address_line1 = $request->address_line1;
            $address_line2 = $request->address_line2;
            $is_default = $request->is_default;
            $longitude = $request->longitude;
            $latitude = $request->latitude;
            $status = $request->status;

            $user = Customer::where('uuid', $uuid)->select('customer_id')->first();
            $user_id = $user->customer_id;
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            CustomerAddress::where('user_id', $user_id)->update($deleteData);

            $address_line1 = $request->address_line1;
            if ($address_line1) {
                foreach ($address_line1 as $key => $value) {

                    $insertAddressData = [
                        'uuid' => Helper::getUuid(),
                        'user_id' => $user_id,
                        'address_line1' => $value,
                        'address_line2' => $address_line2[$key],
                        'is_default' => $is_default[$key],
                        'longitude' => $longitude[$key],
                        'latitude' => $latitude[$key],
                        'status' => $status[$key],
                        'updated_at' => config('constants.date_time'),
                        'updated_by' => Auth::user()->id
                    ];

                    if ($is_default[$key] == 1) {
                        $defaultUpdate = [
                            'is_default' => 0
                        ];
                        CustomerAddress::where('user_id', $user_id)->update($defaultUpdate);
                    }

                    CustomerAddress::create($insertAddressData);
                }
            }

            DB::commit();

            Helper::saveLogAction('1', 'Customers Address', 'Customers Address Update', 'Update Customers Address ' . $user_id, Auth::user()->id);
            Helper::log('Customer Address update : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Customer Address update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Customers Address', 'Customers Address Update', 'Update Customers Address ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }
}
