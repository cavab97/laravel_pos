<?php

namespace App\Http\Controllers;

use App\Models\BranchTax;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\CartSubDetail;
use App\Models\Customer;
use App\Models\Helper;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use SimpleSoftwareIO\QrCode\Facade;


class LoginController extends Controller
{
    /**
     * LoginController constructor.
     */
    public
    function __construct()
    {
        //
    }

    public function login()
    {
        return view('frontend.popup.login');
    }

    public function signup()
    {
        return view('frontend.popup.registration');
    }

    public function signupPost(Request $request)
    {
        Helper::log('Registration store : start');
        DB::beginTransaction();
        try {

            $name = $request->name;
            $username = $request->username;
            $mobile = $request->mobile;
            $email = $request->email;
            $password = '';
            if ($request->reg_password) {
                $password = Hash::make($request->reg_password);
            }
            $deviceId = $_COOKIE['device_id'];

            $checkExists = Customer::where('email', $email)->where('role', config('constants.roles')["customer"])->count();
            $checkUsernameExists = Customer::where('username', $username)->where('role', config('constants.roles')["customer"])->count();
            $checkMobileExists = Customer::where('mobile', $mobile)->where('role', config('constants.roles')["customer"])->count();
            if ($checkExists > 0) {
                Helper::log('Registration store : email is exists');
                return response()->json(['status' => 409, 'message' => trans('frontend/common.email_exists')]);
            } elseif ($checkUsernameExists > 0) {
                Helper::log('Registration store : username is exists');
                return response()->json(['status' => 409, 'message' => trans('frontend/common.username_exists')]);
            } elseif ($checkMobileExists > 0) {
                Helper::log('Registration store : mobile is exists');
                return response()->json(['status' => 409, 'message' => trans('frontend/common.mobile_exists')]);
            } else {
                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'mobile' => $mobile,
                    'password' => $password,
                    'role' => config('constants.roles')["customer"],
                    'status' => 1,
                    'created_at' => config('constants.date_time'),
                    'updated_at' => config('constants.date_time'),
                ];

                $userData = Customer::create($insertData);
                $customer_id = $userData->customer_id;

                $credentials = [
                    'email' => $email,
                    'password' => $request->reg_password,
                ];
                if (Auth::guard('fronts')->attempt($credentials)) {
                    $data = Auth::guard('fronts')->user();
                    $customerId = $data->customer_id;

                    $updateData = [
                        'last_login' => config('constants.date_time'),
                        'created_by' => $customerId,
                        'updated_by' => $customerId,
                    ];
                    Customer::where('customer_id',$customerId)->update($updateData);

                    $sub_total = 0; $total_discount = 0; $total_quantity = 0; $total_tax = 0;
                    $total_att_price = 0; $total_mod_price = 0;

                    /** Check Cart **/
                    $cart = Cart::where('device_id', $deviceId)
                        ->where(['source'=>1, 'cart_payment_status'=>0])
                        ->count();
                    if ($cart > 0) {
                        $maincart = Cart::where('device_id', $deviceId)
                            ->where(['source'=>1, 'cart_payment_status'=>0])
                            ->first();
                        $mainCartId = $maincart->cart_id;
                        $userCart = Cart::where('user_id', $customerId)
                            ->where(['source'=>1, 'cart_payment_status'=>0])
                            ->first();
                        if(empty($userCart)){
                            $updateMainCart = [
                                'user_id' => $customerId
                            ];
                            Cart::where('cart_id',$mainCartId)->where('device_id',$deviceId)->update($updateMainCart);
                        } else {
                            $userCartId = $userCart->cart_id;
                            $cartData = Cart::leftJoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                                ->where('device_id', $deviceId)
                                ->where(['source' => 1, 'cart_payment_status' => 0])
                                ->get();
                            foreach ($cartData as $key => $value) {

                                $updateCartDetail = [
                                    'cart_id' => $userCartId
                                ];

                                CartDetail::where('cart_detail_id',$value->cart_detail_id)->update($updateCartDetail);
                                CartSubDetail::where('cart_detail_id',$value->cart_detail_id)->update($updateCartDetail);
                            }

                            $usercartData = Cart::leftJoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                                ->where('user_id', $customerId)
                                ->where(['source' => 1, 'cart_payment_status' => 0])
                                ->get();
                            foreach ($usercartData as $key => $value) {
                                $sub_total += $value->product_price * $value->product_qty;
                                $total_quantity += $value->product_qty;

                                /* Cart Attribute */
                                $cartAttribute = CartSubDetail::where(['cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('attribute_id', '!=', null)->get();
                                if (!empty($cartAttribute)) {
                                    foreach ($cartAttribute as $akey => $avalue) {
                                        $total_att_price += $avalue->attribute_price * $value->product_qty;
                                    }
                                }
                                /* Cart Modifier */
                                $cartModifier = CartSubDetail::where(['cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('modifier_id', '!=', null)->get();
                                if (!empty($cartModifier)) {
                                    foreach ($cartModifier as $akey => $avalue) {
                                        $total_mod_price += $avalue->modifire_price * $value->product_qty;
                                    }
                                }
                            }

                            $total = $sub_total + $total_att_price + $total_mod_price;
                            $total_item = count($usercartData);

                            $updateUserCart = [
                                'sub_total' => $total,
                                'total_item' => $total_item,
                                'total_qty' => $total_quantity,
                            ];

                            $branchId = $userCart->branch_id;
                            $branchTax = BranchTax::where('branch_id', $branchId)->get();
                            if (!empty($branchTax)) {
                                foreach ($branchTax as $key => $value) {
                                    $taxId = $value->tax_id;
                                    $taxData = Tax::where('tax_id', $taxId)->first();
                                    $taxName = $taxData->code;
                                    $taxRate = $value->rate;
                                    $tax = $total * $taxRate / 100;
                                    $branchTax[$key]['taxAmount'] = number_format($tax,2);;
                                    $branchTax[$key]['taxCode'] = $taxName;
                                    $total_tax += number_format($tax,2);;
                                }
                                $updateUserCart['tax_json'] = $branchTax;
                            }
                            $grand_total = $total + $total_tax;

                            $updateUserCart['tax'] = $total_tax;
                            $updateUserCart['grand_total'] = $grand_total;

                            Cart::where('cart_id',$userCartId)->where('user_id',$customerId)->update($updateUserCart);
                            Cart::where('device_id',$deviceId)->delete();

                        }
                    }

                    unset($_COOKIE['device_id']);

                    DB::commit();
                    Helper::log('Registration store : finish registration');
                    return response()->json(['status' => 200, 'message' => trans('frontend/common.registration_sucess'), 'url' => url()->previous()]);
                } else {
                    Helper::log('Registration store : error');
                    DB::rollBack();
                    return response()->json(['status' => 500, 'message' => trans('frontend/common.not_save_information')]);
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Registration store : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    /**
     * Login Post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function loginPost(Request $request)
    {
        Helper::log('Login Post : start');
        try{
            $username = $request->username;
            $password = $request->password;
            $role_id = config('constants.roles')['customer'];
            $deviceId = $_COOKIE['device_id'];
            $credentials = [
                'password' => $password,
                'role' => $role_id
            ];
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $credentials['email'] = $username;
            } else {
                $credentials['username'] = $username;
            }
            if (Auth::guard('fronts')->attempt($credentials)) {
                $data = Auth::guard('fronts')->user();
                $customerId = $data->customer_id;

                $sub_total = 0; $total_discount = 0; $total_quantity = 0; $total_tax = 0;
                $total_att_price = 0; $total_mod_price = 0;

                /** Check Cart **/
                $cart = Cart::where('device_id', $deviceId)
                    ->where(['source'=>1, 'cart_payment_status'=>0])
                    ->count();
                if ($cart > 0) {
                    $maincart = Cart::where('device_id', $deviceId)
                        ->where(['source'=>1, 'cart_payment_status'=>0])
                        ->first();
                    $mainCartId = $maincart->cart_id;
                    $userCart = Cart::where('user_id', $customerId)
                        ->where(['source'=>1, 'cart_payment_status'=>0])
                        ->first();
                    if(empty($userCart)){
                        $updateMainCart = [
                            'user_id' => $customerId
                        ];
                        Cart::where('cart_id',$mainCartId)->where('device_id',$deviceId)->update($updateMainCart);
                    } else {
                        $userCartId = $userCart->cart_id;
                        $cartData = Cart::leftJoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                            ->where('device_id', $deviceId)
                            ->where(['source' => 1, 'cart_payment_status' => 0])
                            ->get();
                        foreach ($cartData as $key => $value) {

                            $updateCartDetail = [
                                'cart_id' => $userCartId
                            ];

                            CartDetail::where('cart_detail_id',$value->cart_detail_id)->update($updateCartDetail);
                            CartSubDetail::where('cart_detail_id',$value->cart_detail_id)->update($updateCartDetail);
                        }

                        $usercartData = Cart::leftJoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                            ->where('user_id', $customerId)
                            ->where(['source' => 1, 'cart_payment_status' => 0])
                            ->get();
                        foreach ($usercartData as $key => $value) {
                            $sub_total += $value->product_price * $value->product_qty;
                            $total_quantity += $value->product_qty;

                            /* Cart Attribute */
                            $cartAttribute = CartSubDetail::where(['cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('attribute_id', '!=', null)->get();
                            if (!empty($cartAttribute)) {
                                foreach ($cartAttribute as $akey => $avalue) {
                                    $total_att_price += $avalue->attribute_price * $value->product_qty;
                                }
                            }
                            /* Cart Modifier */
                            $cartModifier = CartSubDetail::where(['cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('modifier_id', '!=', null)->get();
                            if (!empty($cartModifier)) {
                                foreach ($cartModifier as $akey => $avalue) {
                                    $total_mod_price += $avalue->modifire_price * $value->product_qty;
                                }
                            }
                        }

                        $total = $sub_total + $total_att_price + $total_mod_price;
                        $total_item = count($usercartData);

                        $updateUserCart = [
                            'sub_total' => $total,
                            'total_item' => $total_item,
                            'total_qty' => $total_quantity,
                        ];

                        $branchId = $userCart->branch_id;
                        $branchTax = BranchTax::where('branch_id', $branchId)->get();
                        if (!empty($branchTax)) {
                            foreach ($branchTax as $key => $value) {
                                $taxId = $value->tax_id;
                                $taxData = Tax::where('tax_id', $taxId)->first();
                                $taxName = $taxData->code;
                                $taxRate = $value->rate;
                                $tax = $total * $taxRate / 100;
                                $branchTax[$key]['taxAmount'] = number_format($tax,2);;
                                $branchTax[$key]['taxCode'] = $taxName;
                                $total_tax += number_format($tax,2);;
                            }
                            $updateUserCart['tax_json'] = $branchTax;
                        }
                        $grand_total = $total + $total_tax;

                        $updateUserCart['tax'] = $total_tax;
                        $updateUserCart['grand_total'] = $grand_total;

                        Cart::where('cart_id',$userCartId)->where('user_id',$customerId)->update($updateUserCart);
                        Cart::where('device_id',$deviceId)->delete();

                    }
                }
                unset($_COOKIE['device_id']);
                $urlPrevious = url()->previous();
                $urlBase = url()->to('/');
                if (($urlPrevious != $urlBase . '/login') && (substr($urlPrevious, 0, strlen($urlBase)) === $urlBase)) {
                    $backUrlPrevious = $urlPrevious;
                } else {
                    $backUrlPrevious = $urlBase . '/home';
                }
                DB::commit();
                Helper::log('User login post : finish');
                return response()->json(['status' => 200, 'message' => trans('frontend/common.login_sucess'), 'data' => $data, 'url' => $backUrlPrevious]);

            } else {
                return response()->json(['status' => 500, 'message' => trans('frontend/common.username_pass_wrong')]);
            }

        } catch (\Exception $exception){
            Helper::log('User Login Front: exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }



    /**
     * logout user
     */
    public function logout()
    {
        DB::beginTransaction();
        Helper::log('User logout : start');
        try {
            $userUuid = Auth::guard('fronts')->user()->uuid;
            $updateData = [
                'last_login' => config('constants.date_time')
            ];
            Customer::where('uuid', $userUuid)->update($updateData);
            unset($_COOKIE['device_id']);
            $device_id = Helper::randomString(32);
            setcookie('device_id', $device_id);
            Auth::logout();
            Session::flush();
            Helper::log('User logout : finish');
            DB::commit();
            return redirect()->back()->with('success','Logout Successfully');
        } catch (\Exception $exception) {
            Helper::log('User logout : exception');
            Helper::log($exception);
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }
}
