<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use App\Models\Attendance;
use App\Models\Attributes;
use App\Models\Banner;
use App\Models\Branch;
use App\Models\BranchTax;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\CartSubDetail;
use App\Models\Category;
use App\Models\CategoryBranch;
use App\Models\ContactUs;
use App\Models\Customer;
use App\Models\Helper;
use App\Models\Modifier;
use App\Models\Order;
use App\Models\OrderAttributes;
use App\Models\OrderDetail;
use App\Models\OrderModifier;
use App\Models\OrderPayment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductBranch;
use App\Models\ProductCategory;
use App\Models\ProductModifier;
use App\Models\ProductStoreInventory;
use App\Models\ProductStoreInventoryLog;
use App\Models\SetMeal;
use App\Models\SetmealAttribute;
use App\Models\SetMealProduct;
use App\Models\Table;
use App\Models\Tax;
use App\Models\Voucher;
use App\Models\VoucherHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Matrix\add;
use SimpleSoftwareIO\QrCode\Facade;

class HomeController extends Controller
{

    /**
     * HomeController constructor.
     */
    public
    function __construct()
    {
        //
    }

    public function index()
    {
        if (!isset($_COOKIE['device_id'])) {
            $device_id = Helper::randomString(32);
            setcookie('device_id', $device_id);
        } else {
            $device_id = ($_COOKIE['device_id']);
        }
        $branchList = Branch::where('status', 1)->get()->toArray();
        //$tableData = Table::where(['available_status' => 1])->first();
        return view('frontend.index', compact('branchList'));
    }

    public function scanQr(Request $request, $uuid)
    {
        try {
            $tableData = Table::where('available_status', 1)->first();
            $branchData = Branch::where(['uuid' => $uuid, 'status' => 1])->first();
            if (!empty($tableData) && !empty($branchData)) {
                $tableuuid = $tableData->uuid;
                $branchSlug = $branchData->slug;
                $link = url('/category/' . $branchSlug . '/' . $tableuuid);
                return view('frontend.scan-qr', compact('link'));
            } else {
                Helper::log('Branch and table not found for scan');
                return redirect()->route('home');
            }
        } catch (\Exception $exception) {
            Helper::log('Scan exception :', $exception);
            return redirect()->route('home');
        }
    }

    public function categoryList(Request $request, $slug)
    {
        try {
            $branchData = Branch::getBranchDataBySlug($slug);
            if (!empty($branchData)) {
                $branchSlug = $slug;
                $branchId = $branchData['branch_id'];
                $branchName = $branchData['name'];

                /* Check Cart Data*/
                $deviceId = $_COOKIE['device_id'];
                if (Auth::guard('fronts')->user()) {
                    $customer = Auth::guard('fronts')->user();
                    $customerId = $customer->customer_id;
                } else {
                    $customerId = '';
                }

                $where = "uuid != ''";
                if ($customerId) {
                    $where = " user_id = '$customerId' ";
                } else {
                    if ($deviceId) {
                        $where = " device_id = '$deviceId' ";
                    }
                }

                /*$cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                    ->whereRaw($where)
                    ->where('cart.branch_id','!=',$branchId)
                    ->where(['source' => 1, 'cart_payment_status' => 0])
                    ->get();
                if (!empty($cartData)) {
                    foreach ($cartData as $value) {
                        $cart_id = $value->cart_id;
                        if ($branchId != $value->branch_id) {
                            Cart::where(['cart_id' => $cart_id])->whereRaw($where)->delete();
                            CartDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id])->delete();
                            CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id])->delete();
                        }
                    }
                }*/

                $categoryList = Category::getCategoryByBranch($branchId);
                return view('frontend.category', compact('categoryList', 'branchSlug','branchName'));
            } else {
                Helper::log('Branch and table not found for scan');
                return redirect()->route('home');
            }
        } catch (\Exception $exception) {
            Helper::log('Exception:', $exception);
            return redirect()->route('home');
        }
    }

    public function checkBranchProductCart(Request $request)
    {
        Helper::log('Check Branch Product In Cart: Start');
        try{

            $branchSlug = $request->branchSlug;
            $branchData = Branch::getBranchDataBySlug($branchSlug);
            if (!empty($branchData)) {
                $branch_slug = $branchSlug;
                $branchId = $branchData['branch_id'];
                $branchName = $branchData['name'];

                /* Branch Product */
                $branchProduct = ProductBranch::where(['branch_id'=>$branchId,'status'=>1])->select('product_id')->get();

                /* Check Cart Data*/
                $deviceId = $_COOKIE['device_id'];
                if (Auth::guard('fronts')->user()) {
                    $customer = Auth::guard('fronts')->user();
                    $customerId = $customer->customer_id;
                } else {
                    $customerId = '';
                }

                $where = "uuid != ''";
                if ($customerId) {
                    $where = " user_id = '$customerId' ";
                } else {
                    if ($deviceId) {
                        $where = " device_id = '$deviceId' ";
                    }
                }
                $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                    ->whereRaw($where)
                    //->whereNotIn('cart_detail.product_id',$branchProduct)
                    ->where('cart.branch_id','!=',$branchId)
                    ->where(['source' => 1, 'cart_payment_status' => 0])
                    ->count();
                if($cartData > 0){
                    return response()->json(['status' => 422, 'message' => trans('frontend/common.branch_product_exist_in_cart')]);
                } else {
                    return response()->json(['status' => 200, 'message' => trans('frontend/common.success')]);
                }
            }

        } catch (\Exception $exception) {
            Helper::log('Check Branch Product In Cart Exception:', $exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    public function cartProductRemovePopup($slug, $msg)
    {
        $branchData = Branch::getBranchDataBySlug($slug);
        return view('frontend.popup.branch-product-remove-cart', compact('msg', 'slug'));
    }

    public function clearCartBranchProduct(Request $request)
    {
        Helper::log('Clear Branch Cart: Start');
        DB::beginTransaction();
        try{

            $branchSlug = $request->branchSlug;
            $branchData = Branch::getBranchDataBySlug($branchSlug);
            if (!empty($branchData)) {

                $branch_slug = $branchSlug;
                $branchId = $branchData['branch_id'];
                $branchName = $branchData['name'];

                /* Check Cart Data*/
                $deviceId = $_COOKIE['device_id'];
                if (Auth::guard('fronts')->user()) {
                    $customer = Auth::guard('fronts')->user();
                    $customerId = $customer->customer_id;
                } else {
                    $customerId = '';
                }

                $where = "uuid != ''";
                if ($customerId) {
                    $where = " user_id = '$customerId' ";
                } else {
                    if ($deviceId) {
                        $where = " device_id = '$deviceId' ";
                    }
                }

                $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                    ->whereRaw($where)
                    ->where('cart.branch_id','!=',$branchId)
                    ->where(['source' => 1, 'cart_payment_status' => 0])
                    ->get();
                if (!empty($cartData)) {
                    foreach ($cartData as $value) {
                        $cart_id = $value->cart_id;
                        if ($branchId != $value->branch_id) {
                            Cart::where(['cart_id' => $cart_id])->whereRaw($where)->delete();
                            CartDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id])->delete();
                            CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id])->delete();
                        }
                    }
                }
                DB::commit();
                Helper::log('Clear Branch Cart : Finish');
                return response()->json(['status' => 200, 'message' => trans('frontend/common.success')]);

            } else {
                return response()->json(['status' => 500, 'message' => trans('frontend/common.branch_not_exist')]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Clear Branch Cart Exception:', $exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    public function productList(Request $request, $slug, $category)
    {
        try {
            $branchData = Branch::getBranchDataBySlug($slug);
            $categoryData = Category::where(['slug' => $category, 'status' => 1])->first();
            if (!empty($branchData) && !empty($categoryData)) {
                $branchId = $branchData['branch_id'];
                $branchName = $branchData['name'];
                $categoryId = $categoryData->category_id;
                $branchSlug = $slug;
                if ($categoryData->is_setmeal) {
                    $productList = SetMeal::getSetmealByBranch($branchId);
                    return view('frontend.setmeal-product', compact('productList', 'branchSlug', 'category','branchName'));
                } else {
                    $productList = Product::getProductByBranchCat($branchId, $categoryId);
                    if(!empty($productList)){
                        foreach ($productList as $key => $value) {
                            $productId = $value->product_id;
                            $productAttribute = ProductAttribute::where('product_id', $productId)
                                ->where('product_attribute.status', '!=', 2)
                                ->count();
                            $productModifier = ProductModifier::where('product_id', $productId)
                                ->where('product_modifier.status', '!=', 2)
                                ->count();
                            if($productAttribute > 0 || $productModifier > 0){
                                $productList[$key]['is_addon'] = true;
                            } else {
                                $productList[$key]['is_addon'] = false;
                            }

                            if($value->has_inventory){
                                $checkInventory = ProductStoreInventory::where(['product_id'=>$productId, 'branch_id'=>$branchId])->first();
                                if(!empty($checkInventory) && $checkInventory->qty > 0){
                                    $productList[$key]['is_out_of_stock'] = "false";
                                } else {
                                    $productList[$key]['is_out_of_stock'] = "true";
                                }
                            } else {
                                $productList[$key]['is_out_of_stock'] = "false";
                            }
                        }
                    }
                    return view('frontend.product', compact('productList', 'branchSlug', 'category','branchName'));
                }
            } else {
                Helper::log('Branch and table not found for scan');
                return redirect()->route('home');
            }
        } catch (\Exception $exception) {
            Helper::log('Exception:' . $exception);
            return redirect()->route('home');
        }
    }

    public function searchProduct(Request $request)
    {
        Helper::log('Search product : start');
        try {
            $slug = $request->branchSlug;
            $category = $request->category;
            $search = $request->search;

            $branchData = Branch::getBranchDataBySlug($slug);
            $categoryData = Category::where(['slug' => $category, 'status' => 1])->first();
            $branchId = $branchData['branch_id'];
            $categoryId = $categoryData->category_id;
            $branchSlug = $slug;
            if ($categoryData->is_setmeal) {
                $productList = SetMeal::getSetmealByBranch($branchId, $search);
                return view('frontend.setmeal-product-search', compact('productList', 'branchSlug', 'category'));
            } else {
                $productList = Product::getProductByBranchCat($branchId, $categoryId, $search);
                if(!empty($productList)){
                    foreach ($productList as $key => $value) {
                        $productId = $value->product_id;
                        $productAttribute = ProductAttribute::where('product_id', $productId)
                            ->where('product_attribute.status', '!=', 2)
                            ->count();
                        $productModifier = ProductModifier::where('product_id', $productId)
                            ->where('product_modifier.status', '!=', 2)
                            ->count();
                        if($productAttribute > 0 || $productModifier > 0){
                            $productList[$key]['is_addon'] = true;
                        } else {
                            $productList[$key]['is_addon'] = false;
                        }

                        if($value->has_inventory){
                            $checkInventory = ProductStoreInventory::where(['product_id'=>$productId, 'branch_id'=>$branchId])->first();
                            if(!empty($checkInventory) && $checkInventory->qty > 0){
                                $productList[$key]['is_out_of_stock'] = "false";
                            } else {
                                $productList[$key]['is_out_of_stock'] = "true";
                            }
                        } else {
                            $productList[$key]['is_out_of_stock'] = "false";
                        }
                    }
                }
                return view('frontend.product-search', compact('productList', 'branchSlug', 'category'));
            }

        } catch (\Exception $exception) {
            Helper::log('Search product Exception:' . $exception);
            return redirect()->route('home');
        }
    }

    public function productDetails($uuid, $slug)
    {
        try {
            $productData = Product::where('uuid', $uuid)->first();
            $branchData = Branch::getBranchDataBySlug($slug);
            //$tableData = Table::getAvilableTable($tableUuid);
            if (!empty($branchData) && !empty($productData)) {

                $productId = $productData->product_id;
                //$tableId = $tableData->table_id;
                $branchId = $branchData['branch_id'];

                $catAtt = ProductAttribute::leftjoin('category_attribute', 'category_attribute.ca_id', 'product_attribute.ca_id')
                    ->where('product_id', $productId)
                    ->where('category_attribute.status', '!=', 2)
                    ->select('product_attribute.ca_id', 'category_attribute.name')
                    ->groupBy('ca_id')
                    ->get();
                if (!empty($catAtt)) {
                    foreach ($catAtt as $key => $value) {
                        $productAttribute = ProductAttribute::leftjoin('attributes', 'attributes.attribute_id', 'product_attribute.attribute_id')
                            ->where('product_id', $productId)
                            ->where('product_attribute.ca_id', $value->ca_id)
                            ->where('product_attribute.status', '!=', 2)
                            ->where('attributes.status', '!=', 2)
                            ->select('product_attribute.*', 'attributes.name','attributes.is_default')
                            ->get();
                        $catAtt[$key]['attribute'] = $productAttribute;
                    }
                }
                $productData->attribute = $catAtt;

                $productData->modifier = ProductModifier::leftjoin('modifier', 'modifier.modifier_id', 'product_modifier.modifier_id')
                    ->where('product_id', $productId)
                    ->where('product_modifier.status', 1)
                    ->select('product_modifier.*', 'modifier.name','modifier.is_default')
                    ->get()->toArray();

                return view('frontend.popup.product', compact('productData', 'slug'));
            } else {
                return response()->json(['status' => 500, 'message' => trans('frontend/common.product_not_exist')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Exception:' . $exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    public function setmealProductDetails($uuid, $slug)
    {
        try {
            $setmealData = SetMeal::where('uuid', $uuid)->first();
            $branchData = Branch::getBranchDataBySlug($slug);
            //$tableData = Table::getAvilableTable($tableUuid);
            if (!empty($branchData) && !empty($setmealData)) {

                $setmealId = $setmealData->setmeal_id;
                //$tableId = $tableData->table_id;
                $branchId = $branchData['branch_id'];

                $setmealData->product = SetMealProduct::leftjoin('setmeal', 'setmeal.setmeal_id', 'setmeal_product.setmeal_id')
                    ->leftjoin('product', 'product.product_id', 'setmeal_product.product_id')
                    ->where('setmeal_product.setmeal_id', $setmealId)
                    ->where('setmeal_product.status', 1)
                    ->select('setmeal.*', 'setmeal_product.product_id', 'setmeal_product.quantity', 'product.name AS product_name')
                    ->get();
                foreach($setmealData->product as $key => $value){
                    $productId = $value->product_id;
                    $catAtt = ProductAttribute::leftjoin('category_attribute', 'category_attribute.ca_id', 'product_attribute.ca_id')
                        ->where('product_id', $productId)
                        ->where('category_attribute.status', '!=', 2)
                        ->select('product_attribute.ca_id', 'category_attribute.name')
                        ->groupBy('ca_id')
                        ->get();
                    if (!empty($catAtt)) {
                        foreach ($catAtt as $ckey => $cvalue) {
                            $productAttribute = ProductAttribute::leftjoin('attributes', 'attributes.attribute_id', 'product_attribute.attribute_id')
                                ->where('product_id', $productId)
                                ->where('product_attribute.ca_id', $cvalue->ca_id)
                                ->where('product_attribute.status', '!=', 2)
                                ->where('attributes.status', '!=', 2)
                                ->select('product_attribute.*', 'attributes.name','attributes.is_default')
                                ->get();
                            $catAtt[$ckey]['attribute'] = $productAttribute;
                        }
                    }
                    $setmealData->product[$key]->attribute = $catAtt;
                }

                return view('frontend.popup.setmeal-product', compact('setmealData', 'slug'));
            } else {
                return response()->json(['status' => 500, 'message' => trans('frontend/common.product_not_exist')]);
            }
        } catch (\Exception $exception) {dd($exception->getMessage());
            Helper::log('Exception:' . $exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    public function addToCart(Request $request)
    {
        Helper::log('Add To cart : start');
        DB::beginTransaction();
        try {

            if ($request->is_setmeal) {
                $setmealId = $request->setmeal_id;
                $quantity = $request->quantity;
                $product = $request->product;
                $attribute = $request->attribute;
                $is_setmeal = $request->is_setmeal;
                $slug = $request->branch_slug;

                $discountPrice = 0;
                $total = 0;
                $sub_total = 0;
                $total_mod_price = 0;
                $total_att_price = 0;
                $total_qty = 0;
                $cartCounter = 0;
                $tax = 0;
                $totalTax = 0;
                $taxId = '';
                $customerId = '';

                $deviceId = $_COOKIE['device_id'];

                if (Auth::guard('fronts')->user()) {
                    $customerId = Auth::guard('fronts')->user()->customer_id;
                    $customer = Customer::where('customer_id', $customerId)->first();
                }

                $branchData = Branch::getBranchDataBySlug($slug);
                if (!empty($branchData)) {

                    $branchId = $branchData['branch_id'];
                    $productData = SetMeal::where('setmeal_id', $setmealId)->first();
                    $price = $productData->price;
                    $productName = $productData->name;

                    if ($quantity == 0) {
                        $quantity = 1;
                    }

                    $where = "uuid != ''";
                    if ($customerId) {
                        $where = " user_id = '$customerId' ";
                    } else {
                        if ($deviceId) {
                            $where = " device_id = '$deviceId' ";
                        }
                    }

                    $cartCount = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where(['source' => 1, 'cart_payment_status' => 0])
                        ->whereRaw($where)->count();

                    if ($cartCount > 0) {
                        $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where(['source' => 1, 'cart_payment_status' => 0])
                            ->whereRaw($where)->first();
                        $cart_id = $cartData->cart_id;

                        /* Cart Details */
                        $insertCartDetail = [
                            'cart_id' => $cart_id,
                            'product_id' => $setmealId,
                            'product_name' => $productName,
                            'product_price' => $price,
                            'product_qty' => $quantity,
                            'product_detail' => \GuzzleHttp\json_encode($productData),
                            'discount' => $discountPrice,
                            'created_at' => config('constants.date_time')
                        ];

                        if ($is_setmeal) {
                            $setmealProduct = SetMealProduct::leftjoin('product', 'product.product_id', 'setmeal_product.product_id')
                                ->where('setmeal_product.setmeal_id', $setmealId)->where('setmeal_product.status', 1)
                                ->get();
                            $insertCartDetail['setmeal_product_detail'] = \GuzzleHttp\json_encode($setmealProduct);
                            $insertCartDetail['issetMeal'] = $is_setmeal;
                        }

                        $cart_details = CartDetail::create($insertCartDetail);
                        $cart_detail_id = $cart_details->cart_detail_id;

                        /* Cart Attributes Details */
                        if (!empty($attribute)) {
                            foreach ($attribute as $key => $value) {
                                $attributeData = Attributes::where(['attribute_id' => $value, 'status' => 1])->first();
                                $prodAttData = SetmealAttribute::where(['setmeal_id' => $setmealId, 'attribute_id' => $value, 'ca_id' => $attributeData->ca_id, 'status' => 1])->first();
                                $insertAtt = [
                                    'cart_detail_id' => $cart_detail_id,
                                    'cart_id' => $cart_id,
                                    'product_id' => $setmealId,
                                    'attribute_id' => $value,
                                    'attribute_price' => $prodAttData->price,
                                    'ca_id' => $attributeData->ca_id,
                                ];
                                CartSubDetail::create($insertAtt);
                            }
                        }

                        $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                            ->whereRaw($where)
                            ->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                            ->select('cart.*', 'cart_detail.cart_detail_id', 'cart_detail.product_id', 'cart_detail.product_qty', 'cart_detail.product_price', 'cart_detail.product_name', 'cart_detail.issetMeal', 'cart_detail.setmeal_product_detail', 'cart_detail.tax_id', 'cart_detail.tax_value', 'cart_detail.discount', 'cart_detail.discount_type')
                            ->get();

                        foreach ($cartData as $key => $value) {
                            $sub_total += $value->product_price * $value->product_qty;
                            $total_qty += $value->product_qty;

                            /* Cart Attribute */
                            $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('attribute_id', '!=', null)->get();
                            if (!empty($cartAttribute)) {
                                foreach ($cartAttribute as $akey => $avalue) {
                                    $total_att_price += $avalue->attribute_price * $value->product_qty;
                                }
                            }
                            /* Cart Modifier */
                            $cartModifier = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('modifier_id', '!=', null)->get();
                            if (!empty($cartModifier)) {
                                foreach ($cartModifier as $akey => $avalue) {
                                    $total_mod_price += $avalue->modifire_price * $value->product_qty;
                                }
                            }

                        }

                        $total_item = count($cartData);

                        $total = $sub_total + $total_att_price + $total_mod_price;

                        $branchTax = BranchTax::where('branch_id', $branchId)->get();
                        if (!empty($branchTax)) {
                            foreach ($branchTax as $key => $value) {
                                $taxId = $value->tax_id;
                                $taxData = Tax::where('tax_id', $taxId)->first();
                                $taxName = $taxData->code;
                                $taxRate = $taxData->rate;
                                $tax = $total * $taxRate / 100;
                                $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                                $branchTax[$key]['taxCode'] = $taxName;
                                $totalTax += $tax;
                            }
                        }

                        $grandtotal = number_format($total + $totalTax ,2);
                        $n = explode('.',$grandtotal);
                        $t = 5 * round($n[1] / 5);
                        if($t >= 100){
                            $grand_total = number_format($n[0] + 1,2);
                        } else {
                            $grand_total = $n[0].'.'.$t;
                        }

                        $updateData = [
                            'sub_total' => $total,
                            'grand_total' => $grand_total,
                            'tax' => $totalTax,
                            'total_qty' => $total_qty,
                            'total_item' => $total_item,
                            'created_at' => config('constants.date_time')
                        ];

                        if (!empty($branchTax) && $totalTax > 0) {
                            $updateData['tax_json'] = \GuzzleHttp\json_encode($branchTax);
                        }

                        $cart = Cart::where('cart_id', $cart_id)->update($updateData);

                    } else {

                        $sub_total = $price * $quantity;

                        $total = $sub_total + $total_att_price + $total_mod_price;

                        $branchTax = BranchTax::where(['branch_id' => $branchId, 'status' => 1])->get();
                        if (!empty($branchTax)) {
                            foreach ($branchTax as $key => $value) {
                                $taxId = $value->tax_id;
                                $taxData = Tax::where('tax_id', $taxId)->first();
                                $taxName = $taxData->code;
                                $taxRate = $taxData->rate;
                                $tax = $total * $taxRate / 100;
                                $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                                $branchTax[$key]['taxCode'] = $taxName;
                                $totalTax += $tax;
                            }
                        }

                        $grandtotal = number_format($total + $totalTax ,2);
                        $n = explode('.',$grandtotal);
                        $t = 5 * round($n[1] / 5);
                        if($t >= 100){
                            $grand_total = number_format($n[0] + 1,2);
                        } else {
                            $grand_total = $n[0].'.'.$t;
                        }

                        $insertData = [
                            'uuid' => Helper::getUuid(),
                            //'product_id' => $productId,
                            'branch_id' => $branchId,
                            'sub_total' => $total,
                            'grand_total' => $grand_total,
                            'tax' => $totalTax,
                            'total_qty' => $quantity,
                            'total_item' => 1,
                            'created_at' => config('constants.date_time')
                        ];

                        if (Auth::guard('fronts')->user()) {
                            $insertData['user_id'] = $customerId;
                        } else {
                            $insertData['device_id'] = $deviceId;
                        }

                        if (!empty($branchTax) && $tax > 0) {
                            $insertData['tax_json'] = \GuzzleHttp\json_encode($branchTax);
                        }

                        $cart = Cart::create($insertData);
                        $cart_id = $cart->cart_id;

                        $setmealProduct = SetMealProduct::leftjoin('product', 'product.product_id', 'setmeal_product.product_id')
                            ->where('setmeal_product.setmeal_id', $setmealId)->where('setmeal_product.status', 1)
                            ->get();

                        /* Cart Details */
                        $insertCartDetail = [
                            'cart_id' => $cart_id,
                            'product_id' => $setmealId,
                            'product_name' => $productName,
                            'product_price' => $price,
                            'product_qty' => $quantity,
                            'product_detail' => \GuzzleHttp\json_encode($productData),
                            'setmeal_product_detail' => \GuzzleHttp\json_encode($setmealProduct),
                            'issetMeal' => $is_setmeal,
                            'discount' => $discountPrice,
                            'created_at' => config('constants.date_time')
                        ];

                        $cart_details = CartDetail::create($insertCartDetail);
                        $cart_detail_id = $cart_details->cart_detail_id;
                    }

                    $message = trans('frontend/common.item_add_to_cart');

                    if (!empty($customerId)) {
                        $cartCounter = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where('user_id', $customerId)
                            ->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->count();
                    } else {
                        $cartCounter = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where('device_id', $deviceId)
                            ->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->count();
                    }

                    Helper::log('Add To Cart : finish');

                    DB::commit();
                    return response()->json([
                        'status' => 200,
                        'url' => url()->previous(),
                        'cart_counter' => $cartCounter,
                        'message' => $message,
                    ]);

                } else {
                    Helper::log('Add To cart : Branch not exist');
                    return response()->json(['status' => 500, 'message' => trans('frontend/common.branch_not_exist')]);
                }


            } else {
                $productId = $request->product_id;
                $quantity = $request->quantity;
                $modifier = $request->modifier;
                $attribute = $request->attribute;
                $slug = $request->branch_slug;
                $is_setmeal = $request->is_setmeal;

                $discountPrice = 0;
                $total = 0;
                $sub_total = 0;
                $total_mod_price = 0;
                $total_att_price = 0;
                $total_qty = 0;
                $cartCounter = 0;
                $tax = 0;
                $totalTax = 0;
                $taxId = '';
                $customerId = '';

                $deviceId = $_COOKIE['device_id'];

                if (Auth::guard('fronts')->user()) {
                    $customerId = Auth::guard('fronts')->user()->customer_id;
                    $customer = Customer::where('customer_id', $customerId)->first();
                }

                $branchData = Branch::getBranchDataBySlug($slug);
                if (!empty($branchData)) {

                    $branchId = $branchData['branch_id'];

                    $productData = Product::where('product_id', $productId)->first();
                    $price = $productData->price;
                    $oldPrice = $productData->old_price;
                    $productName = $productData->name;

                    $addonItems = [];

                    if ($quantity == 0) {
                        $quantity = 1;
                    }

                    $where = "uuid != ''";
                    if ($customerId) {
                        $where = " user_id = '$customerId' ";
                    } else {
                        if ($deviceId) {
                            $where = " device_id = '$deviceId' ";
                        }
                    }

                    $cartCount = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where(['source' => 1, 'cart_payment_status' => 0])
                        ->whereRaw($where)->count();
                    if ($cartCount > 0) {
                        $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where(['source' => 1, 'cart_payment_status' => 0])
                            ->whereRaw($where)->first();
                        $cart_id = $cartData->cart_id;

                        $checkProdCart = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where(['source' => 1, 'cart_payment_status' => 0])
                            ->whereRaw($where)
                            ->where(['cart_detail.product_id' => $productId,'cart_detail.cart_id'=>$cart_id])->count();
                        if($checkProdCart > 0 && empty($attribute) && empty($modifier)){
                            $cartDetail = CartDetail::where(['cart_id' => $cart_id,'product_id'=>$productId])->first();
                            $quantity = $cartDetail->product_qty + 1;
                            $insertCartDetail = [
                                'product_qty' => $quantity,
                                'created_at' => config('constants.date_time')
                            ];
                            CartDetail::where(['cart_id' => $cart_id,'product_id'=>$productId])->update($insertCartDetail);

                        } else {

                            /* Cart Details */
                            $insertCartDetail = [
                                'cart_id' => $cart_id,
                                'product_id' => $productId,
                                'product_name' => $productName,
                                'product_price' => $price,
                                'product_old_price' => $oldPrice,
                                'product_qty' => $quantity,
                                'product_detail' => \GuzzleHttp\json_encode($productData),
                                'discount' => $discountPrice,
                                'created_at' => config('constants.date_time')
                            ];

                            $cart_details = CartDetail::create($insertCartDetail);
                            $cart_detail_id = $cart_details->cart_detail_id;

                            /* Cart Attributes Details */
                            if (!empty($attribute)) {
                                foreach ($attribute as $key => $value) {
                                    $attributeData = Attributes::where(['attribute_id' => $value, 'status' => 1])->first();
                                    $prodAttData = ProductAttribute::where(['product_id' => $productId, 'attribute_id' => $value, 'ca_id' => $attributeData->ca_id, 'status' => 1])->first();
                                    $insertAtt = [
                                        'cart_detail_id' => $cart_detail_id,
                                        'cart_id' => $cart_id,
                                        'product_id' => $productId,
                                        'attribute_id' => $value,
                                        'attribute_price' => $prodAttData->price,
                                        'ca_id' => $attributeData->ca_id,
                                    ];
                                    CartSubDetail::create($insertAtt);
                                }
                            }

                            /* Cart Modifiers Details */
                            if (!empty($modifier)) {
                                foreach ($modifier as $value) {
                                    $modifierData = ProductModifier::where(['product_id' => $productId, 'modifier_id' => $value, 'status' => 1])->first();
                                    $insertAtt = [
                                        'cart_detail_id' => $cart_detail_id,
                                        'cart_id' => $cart_id,
                                        'product_id' => $productId,
                                        'modifier_id' => $value,
                                        'modifire_price' => $modifierData->price,
                                    ];
                                    CartSubDetail::create($insertAtt);
                                }
                            }
                        }

                        $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                            ->whereRaw($where)
                            ->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                            ->select('cart.*', 'cart_detail.cart_detail_id', 'cart_detail.product_id', 'cart_detail.product_qty', 'cart_detail.product_price', 'cart_detail.product_name', 'cart_detail.issetMeal', 'cart_detail.setmeal_product_detail', 'cart_detail.tax_id', 'cart_detail.tax_value', 'cart_detail.discount', 'cart_detail.discount_type')
                            ->get();

                        foreach ($cartData as $key => $value) {
                            $sub_total += $value->product_price * $value->product_qty;
                            $total_qty += $value->product_qty;

                            /* Cart Attribute */
                            $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('attribute_id', '!=', null)->get();
                            if (!empty($cartAttribute)) {
                                foreach ($cartAttribute as $akey => $avalue) {
                                    $total_att_price += $avalue->attribute_price * $value->product_qty;
                                }
                            }
                            /* Cart Modifier */
                            $cartModifier = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('modifier_id', '!=', null)->get();
                            if (!empty($cartModifier)) {
                                foreach ($cartModifier as $akey => $avalue) {
                                    $total_mod_price += $avalue->modifire_price * $value->product_qty;
                                }
                            }

                        }

                        $total_item = count($cartData);

                        $total = $sub_total + $total_att_price + $total_mod_price;

                        $branchTax = BranchTax::where('branch_id', $branchId)->get();
                        if (!empty($branchTax)) {
                            foreach ($branchTax as $key => $value) {
                                $taxId = $value->tax_id;
                                $taxData = Tax::where('tax_id', $taxId)->first();
                                $taxName = $taxData->code;
                                $taxRate = $taxData->rate;
                                $tax = $total * $taxRate / 100;
                                $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                                $branchTax[$key]['taxCode'] = $taxName;
                                $totalTax += $tax;
                            }
                        }

                        $grandtotal = number_format($total + $totalTax ,2);
                        $n = explode('.',$grandtotal);
                        $t = 5 * round($n[1] / 5);
                        if($t >= 100){
                            $grand_total = number_format($n[0] + 1,2);
                        } else {
                            $grand_total = $n[0].'.'.$t;
                        }

                        $updateData = [
                            'sub_total' => $total,
                            'grand_total' => $grand_total,
                            'tax' => $totalTax,
                            'total_qty' => $total_qty,
                            'total_item' => $total_item,
                            'created_at' => config('constants.date_time')
                        ];

                        if (!empty($branchTax) && $totalTax > 0) {
                            $updateData['tax_json'] = \GuzzleHttp\json_encode($branchTax);
                        }

                        $cart = Cart::where('cart_id', $cart_id)->update($updateData);

                    } else {

                        $sub_total = $price * $quantity;

                        if (!empty($attribute)) {
                            foreach ($attribute as $value) {
                                $attributeData = Attributes::where(['attribute_id' => $value, 'status' => 1])->first();
                                $prodAttData = ProductAttribute::where(['product_id' => $productId, 'attribute_id' => $value, 'ca_id' => $attributeData->ca_id, 'status' => 1])->first();
                                $total_att_price += $prodAttData->price * $quantity;
                                $addonItems['attribute_id'][] = $value;

                            }
                        }

                        if (!empty($modifier)) {
                            foreach ($modifier as $value) {
                                $modifierData = ProductModifier::where(['product_id' => $productId, 'modifier_id' => $value, 'status' => 1])->first();
                                $total_mod_price += $modifierData->price * $quantity;
                                $addonItems['modifier_id'][] = $value;
                            }
                        }

                        $total = $sub_total + $total_att_price + $total_mod_price;

                        $branchTax = BranchTax::where(['branch_id' => $branchId, 'status' => 1])->get();
                        if (!empty($branchTax)) {
                            foreach ($branchTax as $key => $value) {
                                $taxId = $value->tax_id;
                                $taxData = Tax::where('tax_id', $taxId)->first();
                                $taxName = $taxData->code;
                                $taxRate = $taxData->rate;
                                $tax = $total * $taxRate / 100;
                                $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                                $branchTax[$key]['taxCode'] = $taxName;
                                $totalTax += $tax;
                            }
                        }

                        $grandtotal = number_format($total + $totalTax ,2);
                        $n = explode('.',$grandtotal);
                        $t = 5 * round($n[1] / 5);
                        if($t >= 100){
                            $grand_total = number_format($n[0] + 1, 2);
                        } else {
                            $grand_total = $n[0].'.'.$t;
                        }

                        $insertData = [
                            'uuid' => Helper::getUuid(),
                            //'product_id' => $productId,
                            'branch_id' => $branchId,
                            'sub_total' => $total,
                            'grand_total' => $grand_total,
                            'tax' => $totalTax,
                            'total_qty' => $quantity,
                            'total_item' => 1,
                            'created_at' => config('constants.date_time')
                        ];

                        if (Auth::guard('fronts')->user()) {
                            $insertData['user_id'] = $customerId;
                        } else {
                            $insertData['device_id'] = $deviceId;
                        }

                        if (!empty($branchTax) && $tax > 0) {
                            $insertData['tax_json'] = \GuzzleHttp\json_encode($branchTax);
                        }

                        $cart = Cart::create($insertData);
                        $cart_id = $cart->cart_id;

                        /* Cart Details */
                        $insertCartDetail = [
                            'cart_id' => $cart_id,
                            'product_id' => $productId,
                            'product_name' => $productName,
                            'product_price' => $price,
                            'product_old_price' => $oldPrice,
                            'product_qty' => $quantity,
                            'product_detail' => \GuzzleHttp\json_encode($productData),
                            'discount' => $discountPrice,
                            'created_at' => config('constants.date_time')
                        ];

                        $cart_details = CartDetail::create($insertCartDetail);
                        $cart_detail_id = $cart_details->cart_detail_id;

                        /* Cart Attributes Details */
                        if (!empty($attribute)) {
                            foreach ($attribute as $key => $value) {
                                $attributeData = Attributes::where(['attribute_id' => $value, 'status' => 1])->first();
                                $prodAttData = ProductAttribute::where(['product_id' => $productId, 'attribute_id' => $value, 'ca_id' => $attributeData->ca_id, 'status' => 1])->first();
                                $insertAtt = [
                                    'cart_detail_id' => $cart_detail_id,
                                    'cart_id' => $cart_id,
                                    'product_id' => $productId,
                                    'attribute_id' => $value,
                                    'attribute_price' => $prodAttData->price,
                                    'ca_id' => $attributeData->ca_id,
                                ];
                                CartSubDetail::create($insertAtt);
                            }
                        }

                        /* Cart Modifiers Details */
                        if (!empty($modifier)) {
                            foreach ($modifier as $value) {
                                $modifierData = ProductModifier::where(['product_id' => $productId, 'modifier_id' => $value, 'status' => 1])->first();
                                $insertAtt = [
                                    'cart_detail_id' => $cart_detail_id,
                                    'cart_id' => $cart_id,
                                    'product_id' => $productId,
                                    'modifier_id' => $value,
                                    'modifire_price' => $modifierData->price,
                                ];
                                CartSubDetail::create($insertAtt);
                            }
                        }
                    }

                    $message = trans('frontend/common.item_add_to_cart');

                    if (!empty($customerId)) {
                        $cartCounter = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where('user_id', $customerId)
                            ->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->count();
                    } else {
                        $cartCounter = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')->where('device_id', $deviceId)
                            ->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->count();
                    }

                    Helper::log('Add To Cart : finish');

                    DB::commit();
                    return response()->json([
                        'status' => 200,
                        'url' => url()->previous(),
                        'cart_counter' => $cartCounter,
                        'message' => $message,
                    ]);


                } else {
                    Helper::log('Add To cart : Branch not exist');
                    return response()->json(['status' => 500, 'message' => trans('frontend/common.branch_not_exist')]);
                }
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Add To cart : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    public function cartCheckOut($branchSlug)
    {
        try {
            $branchData = Branch::getBranchDataBySlug($branchSlug);
            if (!empty($branchData)) {
                $branchId = $branchData['branch_id'];
                $branchName = $branchData['name'];
                $cartTotal = 0;
                $grandTotal = 0;
                $taxTotal = 0;
                $discountTotal = 0;
                $addonPrice = 0;
                $deviceId = $_COOKIE['device_id'];
                if (Auth::guard('fronts')->user()) {
                    $customer = Auth::guard('fronts')->user();
                    $customerId = $customer->customer_id;
                } else {
                    $customerId = '';
                }

                $where = "uuid != ''";
                if ($customerId) {
                    $where = " user_id = '$customerId' ";
                } else {
                    if ($deviceId) {
                        $where = " device_id = '$deviceId' ";
                    }
                }

                $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                    ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                    ->select('cart.*', 'cart_detail.cart_detail_id', 'cart_detail.product_id', 'cart_detail.product_qty', 'cart_detail.product_price', 'cart_detail.product_name', 'cart_detail.issetMeal', 'cart_detail.setmeal_product_detail', 'cart_detail.tax_id', 'cart_detail.tax_value', 'cart_detail.discount', 'cart_detail.discount_type')
                    ->get();

                if (!empty($cartData)) {
                    foreach ($cartData as $key => $value) {

                        $productId = $value->product_id;
                        $cart_id = $value->cart_id;

                        if ($value->issetMeal) {

                            $productData = SetMeal::where(['setmeal_id' => $productId])->first();
                            $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'product_id' => $productId, 'cart_detail_id' => $value->cart_detail_id])->where('attribute_id', '!=', null)->get()->toArray();

                            $addonPrice = 0;
                            $attributeText = '';
                            if (!empty($cartAttribute)) {
                                $i = 0;
                                foreach ($cartAttribute as $akey => $avalue) {
                                    $attribute = Attributes::where('attribute_id', $avalue['attribute_id'])->first();
                                    $attributeText .= $attribute->name . '(' . $avalue['attribute_price'] . ')';
                                    if (count($cartAttribute) != ($i + 1)) {
                                        $attributeText .= ', ';
                                    }
                                    $i++;
                                    $cartAttribute[$akey]['attribute_name'] = $attribute->name;

                                    $addonPrice += $avalue['attribute_price'] * $value->product_qty;
                                }
                            }
                            $cartData[$key]['cart_attribute'] = $cartAttribute;
                            $cartData[$key]['cart_modifier'] = [];
                            $cartData[$key]['cart_addon'] = $attributeText;
                            $cartData[$key]['product_total'] = $value->product_price * $value->product_qty + $addonPrice;
                        } else {

                            $productData = Product::where(['product_id' => $productId])->first();

                            $cartData[$key]['product_name'] = $productData->name;

                            $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'product_id' => $productId, 'cart_detail_id' => $value->cart_detail_id])->where('attribute_id', '!=', null)->get()->toArray();

                            $addonPrice = 0;
                            $attributeText = '';
                            if (!empty($cartAttribute)) {
                                $i = 0;
                                foreach ($cartAttribute as $akey => $avalue) {
                                    $attribute = Attributes::where('attribute_id', $avalue['attribute_id'])->first();
                                    $attributeText .= $attribute->name . '(' . $avalue['attribute_price'] . ')';
                                    if (count($cartAttribute) != ($i + 1)) {
                                        $attributeText .= ', ';
                                    }
                                    $i++;
                                    $cartAttribute[$akey]['attribute_name'] = $attribute->name;

                                    $addonPrice += $avalue['attribute_price'] * $value->product_qty;
                                }
                            }
                            $cartData[$key]['cart_attribute'] = $cartAttribute;


                            $cartModifierIds = [];
                            $cartModifier = CartSubDetail::where(['cart_id' => $cart_id, 'product_id' => $productId, 'cart_detail_id' => $value->cart_detail_id])->where('modifier_id', '!=', null)->get()->toArray();
                            if (!empty($cartModifier)) {
                                $i = 0;
                                if (!empty($attributeText)) {
                                    $attributeText .= ", ";
                                }
                                foreach ($cartModifier as $akey => $avalue) {
                                    $modifier = Modifier::where('modifier_id', $avalue['modifier_id'])->first();
                                    $attributeText .= $modifier->name . '(' . $avalue['modifire_price'] . ')';
                                    if (count($cartModifier) != ($i + 1)) {
                                        $attributeText .= ', ';
                                    }
                                    $i++;
                                    $cartModifier[$akey]['modifier_name'] = $modifier->name;
                                    $addonPrice += $avalue['modifire_price'] * $value->product_qty;
                                }
                            }
                            $cartData[$key]['cart_modifier'] = $cartModifier;
                            $cartData[$key]['cart_addon'] = $attributeText;
                            $cartData[$key]['product_total'] = $value->product_price * $value->product_qty + $addonPrice;
                        }
                    }

                }

                /* Branch Tax */
                //$branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')->where('branch_tax.branch_id', $branchId)->where('branch_tax.status', 1)->get();
                $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                    ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                    ->select('tax.*')
                    ->get();

                return view('frontend.checkout', compact('cartData', 'branchTax', 'discountTotal', 'branchSlug','branchName'));
            } else {
                return redirect()->back()->with('error', trans('frontend/common.branch_not_exist'));
            }


        } catch (\Exception $exception) {
            Helper::log('Cart Checkout Exception:' . $exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }

    }

    /**
     * Update Cart     *
     */
    public function updateCart(Request $request)
    {
        Helper::log('Update cart : start');
        DB::beginTransaction();
        try {
            $cart_id = $request->cart_id;
            $cart_detail_id = $request->cart_detail_id;
            $productId = $request->product_id;
            $quantity = $request->quantity;
            $voucher_id = $request->voucher_id;
            $branchSlug = $request->slug;
            $cust_email = $request->cust_email;
            $cust_mobile = $request->cust_mobile;

            $discountPrice = 0;
            $total_att_price = 0;
            $total_mod_price = 0;
            $total = 0;
            $sub_total = 0;
            $addonPrice = 0;
            $tax = 0;
            $totalTax = 0;
            $discountTotal = 0;
            $total_qty = 0;
            $discount_type = '';

            $deviceId = $_COOKIE['device_id'];

            $branchData = Branch::getBranchDataBySlug($branchSlug);
            if (!empty($branchData)) {
                $branchId = $branchData['branch_id'];


                if (Auth::guard('fronts')->user()) {
                    $customerId = Auth::guard('fronts')->user()->customer_id;
                    $customer = Customer::where('customer_id', $customerId)->first();
                }
                $where = "uuid != ''";
                if (!empty($customerId)) {
                    $where = " user_id = '$customerId' ";
                } else {
                    if ($deviceId) {
                        $where = " device_id = '$deviceId' ";
                    }
                }
                $cart = Cart::whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->first();

                /*$product = Product::where('product_id', $productId)->first();
                $price = $product->price;
                $oldPrice = $product->old_price;*/

                if ($cart) {
                    $cart_id = $cart->cart_id;
                    $branchId = $cart->branch_id;
                    $cartDetail = CartDetail::where('cart_detail_id', $cart_detail_id)->first();
                    if ($quantity > 0) {

                        /* Cart Details */
                        $updateCartDetails = [
                            'product_qty' => $quantity,
                            'created_at' => config('constants.date_time')
                        ];

                        CartDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $cart_detail_id, 'product_id' => $productId])->update($updateCartDetails);

                        $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                            ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                            ->select('cart.*', 'cart_detail.cart_detail_id', 'cart_detail.product_id', 'cart_detail.product_qty', 'cart_detail.product_price', 'cart_detail.product_name', 'cart_detail.issetMeal', 'cart_detail.setmeal_product_detail', 'cart_detail.tax_id', 'cart_detail.tax_value', 'cart_detail.discount', 'cart_detail.discount_type')
                            ->get();

                        foreach ($cartData as $key => $value) {
                            $sub_total += $value->product_price * $value->product_qty;
                            $total_qty += $value->product_qty;

                            /* Cart Attribute */
                            $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('attribute_id', '!=', null)->get();
                            if (!empty($cartAttribute)) {
                                foreach ($cartAttribute as $akey => $avalue) {
                                    $total_att_price += $avalue->attribute_price * $value->product_qty;
                                }
                            }
                            /* Cart Modifier */
                            $cartModifier = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('modifier_id', '!=', null)->get();
                            if (!empty($cartModifier)) {
                                foreach ($cartModifier as $akey => $avalue) {
                                    $total_mod_price += $avalue->modifire_price * $value->product_qty;
                                }
                            }
                        }

                        $total_item = count($cartData);
                        $total = $sub_total + $total_att_price + $total_mod_price;

                        //$branchTax = BranchTax::where(['branch_id' => $branchId,'status'=>1])->get();
                        $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                            ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                            ->select('tax.*')
                            ->get();
                        if (!empty($branchTax)) {
                            foreach ($branchTax as $key => $value) {
                                $taxId = $value->tax_id;
                                //$taxData = Tax::where('tax_id', $taxId)->first();
                                $taxName = $value->code;
                                $taxRate = $value->rate;
                                $tax = $total * $taxRate / 100;
                                $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                                $branchTax[$key]['taxCode'] = $taxName;
                                $totalTax += $tax;
                            }
                        }

                        $grandtotal = number_format($total + $totalTax ,2);
                        $n = explode('.',$grandtotal);
                        $t = 5 * round($n[1] / 5);
                        if($t >= 100){
                            $grand_total = number_format($n[0] + 1, 2);
                        } else {
                            $grand_total = $n[0].'.'.$t;
                        }

                        $updateData = [
                            'sub_total' => $total,
                            'grand_total' => $grand_total,
                            'total_qty' => $total_qty,
                            'total_item' => $total_item,
                            'tax' => $totalTax,
                            'created_at' => config('constants.date_time')
                        ];

                        if (!empty($branchTax) && $tax > 0) {
                            $updateData['tax_json'] = \GuzzleHttp\json_encode($branchTax);
                        }

                        Cart::where('cart_id', $cart_id)->update($updateData);

                        $message = trans('frontend/common.item_update_to_cart');

                    } else {
                        $cartCount = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                            ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->count();
                        if ($cartCount == 1) {
                            Cart::where('cart_id', $cart_id)->where(['source' => 1, 'cart_payment_status' => 0])->delete();
                        }
                        CartDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $cart_detail_id])->delete();
                        CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $cart_detail_id])->delete();

                        $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                            ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                            ->select('cart.*', 'cart_detail.cart_detail_id', 'cart_detail.product_qty', 'cart_detail.product_id', 'cart_detail.product_price', 'cart_detail.product_name', 'cart_detail.tax_id', 'cart_detail.tax_value', 'cart_detail.discount', 'cart_detail.discount_type')
                            ->get();

                        if (count($cartData) > 0) {
                            foreach ($cartData as $key => $value) {
                                $sub_total += $value->product_price * $value->product_qty;
                                $total_qty += $value->product_qty;
                                $catbranchId = $value->branch_id;


                                /* Cart Attribute */
                                $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('attribute_id', '!=', null)->get();
                                if (!empty($cartAttribute)) {
                                    foreach ($cartAttribute as $akey => $avalue) {
                                        $total_att_price += $avalue->attribute_price * $value->product_qty;
                                    }
                                }
                                /* Cart Modifier */
                                $cartModifier = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('modifier_id', '!=', null)->get();
                                if (!empty($cartModifier)) {
                                    foreach ($cartModifier as $akey => $avalue) {
                                        $total_mod_price += $avalue->modifire_price * $value->product_qty;
                                    }
                                }
                            }

                            $total = $sub_total + $total_att_price + $total_mod_price;
                            $total_item = count($cartData);

                            //$branchTax = BranchTax::where(['branch_id' => $branchId,'status'=>1])->get();
                            $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                                ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                                ->select('tax.*')
                                ->get();
                            if (!empty($branchTax)) {
                                foreach ($branchTax as $key => $value) {
                                    $taxId = $value->tax_id;
                                    //$taxData = Tax::where('tax_id', $taxId)->first();
                                    $taxName = $value->code;
                                    $taxRate = $value->rate;
                                    $tax = $total * $taxRate / 100;
                                    $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                                    $branchTax[$key]['taxCode'] = $taxName;
                                    $totalTax += $tax;
                                }
                            }

                            $grandtotal = number_format($total + $totalTax ,2);
                            $n = explode('.',$grandtotal);
                            $t = 5 * round($n[1] / 5);
                            if($t >= 100){
                                $grand_total = number_format($n[0] + 1, 2);
                            } else {
                                $grand_total = $n[0].'.'.$t;
                            }

                            $updateData = [
                                'sub_total' => $total,
                                'grand_total' => $grand_total,
                                'total_qty' => $total_qty,
                                'total_item' => $total_item,
                                'tax' => $totalTax,
                                'created_at' => config('constants.date_time')
                            ];

                            if (!empty($branchTax) && $tax > 0) {
                                $updateData['tax_json'] = \GuzzleHttp\json_encode($branchTax);
                            }

                            Cart::where('cart_id', $cart_id)->update($updateData);
                        }

                        $message = trans('frontend/common.item_remove_from_cart');
                    }

                    /* Cart List Data */
                    $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                        ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                        ->select('cart.*', 'cart_detail.cart_detail_id', 'cart_detail.product_id', 'cart_detail.product_qty', 'cart_detail.product_price', 'cart_detail.product_name', 'cart_detail.issetMeal', 'cart_detail.setmeal_product_detail', 'cart_detail.tax_id', 'cart_detail.tax_value', 'cart_detail.discount', 'cart_detail.discount_type')
                        ->get();

                    if (!empty($cartData)) {
                        foreach ($cartData as $key => $value) {

                            $productId = $value->product_id;
                            $cart_id = $value->cart_id;

                            //$productData = Product::where(['product_id' => $productId])->first();

                            //$cartData[$key]['product_name'] = $productData->name;

                            $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'product_id' => $productId])->where('attribute_id', '!=', null)->get()->toArray();

                            $addonPrice = 0;
                            $attributeText = '';
                            if (!empty($cartAttribute)) {
                                $i = 0;
                                foreach ($cartAttribute as $akey => $avalue) {
                                    $attribute = Attributes::where('attribute_id', $avalue['attribute_id'])->first();
                                    $attributeText .= $attribute->name . '(' . $avalue['attribute_price'] . ')';
                                    if (count($cartAttribute) != ($i + 1)) {
                                        $attributeText .= ', ';
                                    }
                                    $i++;
                                    $cartAttribute[$akey]['attribute_name'] = $attribute->name;
                                    $addonPrice += $avalue['attribute_price'] * $value->product_qty;
                                }
                            }
                            $cartData[$key]['cart_attribute'] = $cartAttribute;


                            $cartModifierIds = [];
                            $cartModifier = CartSubDetail::where(['cart_id' => $cart_id, 'product_id' => $productId])->where('modifier_id', '!=', null)->get()->toArray();
                            if (!empty($cartModifier)) {
                                $i = 0;
                                if (!empty($attributeText)) {
                                    $attributeText .= ", ";
                                }
                                foreach ($cartModifier as $akey => $avalue) {
                                    $modifier = Modifier::where('modifier_id', $avalue['modifier_id'])->first();
                                    $attributeText .= $modifier->name . '(' . $avalue['modifire_price'] . ')';
                                    if (count($cartModifier) != ($i + 1)) {
                                        $attributeText .= ', ';
                                    }
                                    $i++;
                                    $cartModifier[$akey]['modifier_name'] = $modifier->name;
                                    $addonPrice += $avalue['modifire_price'] * $value->product_qty;
                                }
                            }
                            $cartData[$key]['cart_modifier'] = $cartModifier;
                            $cartData[$key]['cart_addon'] = $attributeText;
                            $cartData[$key]['product_total'] = $value->product_price * $value->product_qty + $addonPrice;
                        }

                        if (!empty($voucher_id)) {
                            $voucherData = Voucher::where('voucher_id', $voucher_id)->where('status', 1)->first();
                            if (!empty($voucherData)) {
                                $ids = [];
                                if (!empty($voucherData->voucher_products) && empty($voucherData->voucher_categories)) {
                                    $voucher_product = explode(',', $voucherData->voucher_products);
                                    array_merge($ids, $voucher_product);
                                } elseif (empty($voucherData->voucher_products) && !empty($voucherData->voucher_categories)) {
                                    $voucher_category = explode(',', $voucherData->voucher_categories);
                                    $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                                    if (!empty($cartcategory)) {
                                        foreach ($cartcategory as $catKey => $catValue) {
                                            array_push($ids, $catValue->product_id);
                                        }
                                    }
                                } elseif (!empty($voucherData->voucher_products) && !empty($voucherData->voucher_categories)) {
                                    $voucher_product = explode(',', $voucherData->voucher_products);
                                    $ids = array_unique(array_merge($ids, $voucher_product));
                                    $voucher_category = explode(',', $voucherData->voucher_categories);
                                    $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                                    if (!empty($cartcategory)) {
                                        foreach ($cartcategory as $catKey => $catValue) {
                                            array_push($ids, $catValue->product_id);
                                        }
                                    }
                                } else {
                                    if ($voucherData->voucher_discount_type == 1) {
                                        $discount = $voucherData->voucher_discount;
                                        $discount_type = 1;
                                    } else {
                                        $discount = ($sub_total * $voucherData->voucher_discount) / 100;
                                        $discount_type = 2;
                                    }

                                    $discountTotal = $discount;

                                }
                                $ids = array_unique($ids);
                                $cartCatProduct = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                                    ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->whereIn('cart_detail.product_id', $ids)->get();
                                if (count($cartCatProduct) > 0) {
                                    foreach ($cartCatProduct as $key => $value) {
                                        $prodTotal = $value->product_price * $value->product_qty;

                                        if ($voucherData->voucher_discount_type == 1) {
                                            $discount = $voucherData->voucher_discount;
                                            $discount_type = 1;
                                        } else {
                                            $discount = ($prodTotal * $voucherData->voucher_discount) / 100;
                                            $discount_type = 2;
                                        }

                                        $discountTotal += $discount;
                                    }
                                } else {
                                    if ($voucherData->voucher_discount_type == 1) {
                                        $discount = $voucherData->voucher_discount;
                                        $discount_type = 1;
                                    } else {
                                        $discount = ($sub_total * $voucherData->voucher_discount) / 100;
                                        $discount_type = 2;
                                    }

                                    $discountTotal = $discount;

                                }
                            }
                        }

                    }

                    /* Branch Tax */
                    $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                        ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                        ->select('tax.*')
                        ->get();

                    Helper::log('Update To Cart : finish');

                    DB::commit();
                    return view('frontend.checkout_table', compact('cartData', 'branchTax', 'discountTotal', 'branchSlug', 'voucher_id', 'cust_email', 'cust_mobile'));
                    /*return response()->json([
                        'status' => 200,
                        'url' => url()->previous(),
                        'message' => $message,
                    ]);*/
                }
            } else {
                return response()->json(['status' => 404, 'message' => trans('frontend/common.branch_not_exist')]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Update cart : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    /**
     * Remove Item From Cart     *
     */
    public function removeCart(Request $request)
    {
        Helper::log('Remove cart : start');
        DB::beginTransaction();
        try {
            $cart_detail_id = $request->cart_detail_id;
            $branchSlug = $request->slug;
            $voucher_id = $request->voucher_id;
            $cust_email = $request->cust_email;
            $cust_mobile = $request->cust_mobile;

            $discountPrice = 0;
            $total_att_price = 0;
            $total_mod_price = 0;
            $discountTotal = 0;
            $total = 0;
            $sub_total = 0;
            $total_qty = 0;
            $totalTax = 0;

            $deviceId = $_COOKIE['device_id'];

            if (Auth::guard('fronts')->user()) {
                $customerId = Auth::guard('fronts')->user()->customer_id;
                $customer = Customer::where('customer_id', $customerId)->first();
            }
            $where = "uuid != ''";
            if (!empty($customerId)) {
                $where = " user_id = '$customerId' ";
            } else {
                if ($deviceId) {
                    $where = " device_id = '$deviceId' ";
                }
            }

            if (!empty($cart_detail_id)) {

                $branchData = Branch::getBranchDataBySlug($branchSlug);
                $branchId = $branchData['branch_id'];

                $cartDetail = CartDetail::where('cart_detail_id', $cart_detail_id)->first();
                $cart_id = $cartDetail->cart_id;
                $cartCount = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                    ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->count();
                if ($cartCount == 1) {
                    Cart::where('cart_id', $cart_id)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->delete();
                }
                CartDetail::where('cart_detail_id', $cart_detail_id)->delete();
                CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $cart_detail_id])->delete();

                $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                    ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                    ->select('cart.*', 'cart_detail.cart_detail_id', 'cart_detail.product_qty', 'cart_detail.product_id', 'cart_detail.product_price', 'cart_detail.product_name', 'cart_detail.issetMeal', 'cart_detail.setmeal_product_detail', 'cart_detail.tax_id', 'cart_detail.tax_value', 'cart_detail.discount', 'cart_detail.discount_type')
                    ->get();

                if (count($cartData) > 0) {
                    foreach ($cartData as $key => $value) {
                        $sub_total += $value->product_price * $value->product_qty;
                        $total_qty += $value->product_qty;
                        $catbranchId = $value->branch_id;


                        /* Cart Attribute */
                        $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('attribute_id', '!=', null)->get();
                        if (!empty($cartAttribute)) {
                            foreach ($cartAttribute as $akey => $avalue) {
                                $total_att_price += $avalue->attribute_price * $value->product_qty;
                            }
                        }
                        /* Cart Modifier */
                        $cartModifier = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $value->cart_detail_id, 'product_id' => $value->product_id])->where('modifier_id', '!=', null)->get();
                        if (!empty($cartModifier)) {
                            foreach ($cartModifier as $akey => $avalue) {
                                $total_mod_price += $avalue->modifire_price * $value->product_qty;
                            }
                        }
                    }

                    $total = $sub_total + $total_att_price + $total_mod_price;
                    $total_item = count($cartData);

                    //$branchTax = BranchTax::where(['branch_id' => $branchId,'status'=>1])->get();
                    $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                        ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                        ->select('tax.*')
                        ->get();
                    if (!empty($branchTax)) {
                        foreach ($branchTax as $key => $value) {
                            $taxId = $value->tax_id;
                            //$taxData = Tax::where('tax_id', $taxId)->first();
                            $taxName = $value->code;
                            $taxRate = $value->rate;
                            $tax = $total * $taxRate / 100;
                            $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                            $branchTax[$key]['taxCode'] = $taxName;
                            $totalTax += $tax;
                        }
                    }

                    $grandtotal = number_format($total + $totalTax ,2);
                    $n = explode('.',$grandtotal);
                    $t = 5 * round($n[1] / 5);
                    if($t >= 100){
                        $grand_total = number_format($n[0] + 1, 2);
                    } else {
                        $grand_total = $n[0].'.'.$t;
                    }

                    $updateData = [
                        'sub_total' => $total,
                        'grand_total' => $grand_total,
                        'total_qty' => $total_qty,
                        'total_item' => $total_item,
                        'tax' => $totalTax,
                        'created_at' => config('constants.date_time')
                    ];

                    if (!empty($branchTax) && $tax > 0) {
                        $updateData['tax_json'] = \GuzzleHttp\json_encode($branchTax);
                    }

                    Cart::where('cart_id', $cart_id)->update($updateData);
                }

                /* Cart List Data */
                $cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                    ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                    ->select('cart.*', 'cart_detail.cart_detail_id', 'cart_detail.product_id', 'cart_detail.product_qty', 'cart_detail.product_price', 'cart_detail.product_name', 'cart_detail.issetMeal', 'cart_detail.setmeal_product_detail', 'cart_detail.tax_id', 'cart_detail.tax_value', 'cart_detail.discount', 'cart_detail.discount_type')
                    ->get();

                if (count($cartData) > 0) {
                    foreach ($cartData as $key => $value) {

                        $productId = $value->product_id;
                        $cart_id = $value->cart_id;

                        /*$productData = Product::where(['product_id' => $productId])->first();

                        $cartData[$key]['product_name'] = $productData->name;*/

                        $cartAttribute = CartSubDetail::where(['cart_id' => $cart_id, 'product_id' => $productId, 'cart_detail_id' => $value->cart_detail_id])->where('attribute_id', '!=', null)->get()->toArray();
                        $addonPrice = 0;
                        $attributeText = '';
                        if (!empty($cartAttribute)) {
                            $i = 0;
                            foreach ($cartAttribute as $akey => $avalue) {
                                $attribute = Attributes::where('attribute_id', $avalue['attribute_id'])->first();
                                $attributeText .= $attribute->name . '(' . $avalue['attribute_price'] . ')';
                                if (count($cartAttribute) != ($i + 1)) {
                                    $attributeText .= ', ';
                                }
                                $i++;
                                $cartAttribute[$akey]['attribute_name'] = $attribute->name;
                                $addonPrice += $avalue['attribute_price'] * $value->product_qty;
                            }
                        }
                        $cartData[$key]['cart_attribute'] = $cartAttribute;


                        $cartModifierIds = [];
                        $cartModifier = CartSubDetail::where(['cart_id' => $cart_id, 'product_id' => $productId, 'cart_detail_id' => $value->cart_detail_id])->where('modifier_id', '!=', null)->get()->toArray();
                        if (!empty($cartModifier)) {
                            $i = 0;
                            if (!empty($attributeText)) {
                                $attributeText .= ", ";
                            }
                            foreach ($cartModifier as $akey => $avalue) {
                                $modifier = Modifier::where('modifier_id', $avalue['modifier_id'])->first();
                                $attributeText .= $modifier->name . '(' . $avalue['modifire_price'] . ')';
                                if (count($cartModifier) != ($i + 1)) {
                                    $attributeText .= ', ';
                                }
                                $i++;
                                $cartModifier[$akey]['modifier_name'] = $modifier->name;
                                $addonPrice += $avalue['modifire_price'] * $value->product_qty;
                            }
                        }
                        $cartData[$key]['cart_modifier'] = $cartModifier;
                        $cartData[$key]['cart_addon'] = $attributeText;
                        $cartData[$key]['product_total'] = $value->product_price * $value->product_qty + $addonPrice;
                    }

                    if (!empty($voucher_id)) {
                        $voucherData = Voucher::where('voucher_id', $voucher_id)->where('status', 1)->first();
                        if (!empty($voucherData)) {
                            $ids = [];
                            if (!empty($voucherData->voucher_products) && empty($voucherData->voucher_categories)) {
                                $voucher_product = explode(',', $voucherData->voucher_products);
                                array_merge($ids, $voucher_product);
                            } elseif (empty($voucherData->voucher_products) && !empty($voucherData->voucher_categories)) {
                                $voucher_category = explode(',', $voucherData->voucher_categories);
                                $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                                if (!empty($cartcategory)) {
                                    foreach ($cartcategory as $catKey => $catValue) {
                                        array_push($ids, $catValue->product_id);
                                    }
                                }
                            } elseif (!empty($voucherData->voucher_products) && !empty($voucherData->voucher_categories)) {
                                $voucher_product = explode(',', $voucherData->voucher_products);
                                $ids = array_unique(array_merge($ids, $voucher_product));
                                $voucher_category = explode(',', $voucherData->voucher_categories);
                                $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                                if (!empty($cartcategory)) {
                                    foreach ($cartcategory as $catKey => $catValue) {
                                        array_push($ids, $catValue->product_id);
                                    }
                                }
                            } else {
                                if ($voucherData->voucher_discount_type == 1) {
                                    $discount = $voucherData->voucher_discount;
                                    $discount_type = 1;
                                } else {
                                    $discount = ($sub_total * $voucherData->voucher_discount) / 100;
                                    $discount_type = 2;
                                }

                                $discountTotal = $discount;

                            }
                            $ids = array_unique($ids);
                            $cartCatProduct = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                                ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->whereIn('cart_detail.product_id', $ids)->get();
                            if (count($cartCatProduct) > 0) {
                                foreach ($cartCatProduct as $key => $value) {
                                    $prodTotal = $value->product_price * $value->product_qty;

                                    if ($voucherData->voucher_discount_type == 1) {
                                        $discount = $voucherData->voucher_discount;
                                        $discount_type = 1;
                                    } else {
                                        $discount = ($prodTotal * $voucherData->voucher_discount) / 100;
                                        $discount_type = 2;
                                    }

                                    $discountTotal += $discount;
                                }
                            } else {
                                if ($voucherData->voucher_discount_type == 1) {
                                    $discount = $voucherData->voucher_discount;
                                    $discount_type = 1;
                                } else {
                                    $discount = ($sub_total * $voucherData->voucher_discount) / 100;
                                    $discount_type = 2;
                                }

                                $discountTotal = $discount;

                            }
                        }
                    }

                }

                /* Branch Tax */
                //$branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')->where('branch_tax.branch_id', $branchId)->where('branch_tax.status', 1)->get();
                $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                    ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                    ->select('tax.*')
                    ->get();

                DB::commit();
                Helper::log('remove cart : finish');
                return view('frontend.checkout_table', compact('cartData', 'branchTax', 'discountTotal', 'branchSlug', 'voucher_id', 'cust_mobile', 'cust_email'));
                /*return response()->json([
                    'status' => 200,
                    'url' => url()->previous(),
                    'message' => trans('frontend/common.item_remove_from_cart'),
                ]);*/
            } else {
                return response()->json([
                    'status' => 500,
                    'url' => url()->previous(),
                    'message' => trans('frontend/common.cart_item_not_found'),
                ]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Remove cart : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    /**
     * Remove Item Popup
     */
    public function removeCartPopup($cart_detail_id, $branchSlug)
    {
        return view('frontend.popup.remove_item', compact('cart_detail_id', 'branchSlug'));
    }

    /**
     * check voucher code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkVoucher(Request $request)
    {
        Helper::log('Check voucher : start');
        try {
            $voucher_code = $request->voucher_code;
            $branchSlug = $request->branchSlug;
            $amount = 0;
            $currentDate = date('Y-m-d');
            $cartProduct = 0;
            $cartCatProduct = 0;

            $checkExistCoupon = Voucher::where(DB::raw('BINARY voucher_code'), $voucher_code)->where('status', 1)->first();

            if (!empty($checkExistCoupon)) {

                $branchData = Branch::getBranchDataBySlug($branchSlug);
                $branchId = $branchData['branch_id'];

                $voucherId = $checkExistCoupon->voucher_id;
                $voucher_applicable_from = date('Y-m-d', strtotime($checkExistCoupon->voucher_applicable_from));
                $voucher_applicable_to = date('Y-m-d', strtotime($checkExistCoupon->voucher_applicable_to));
                $voucher_total_uses = $checkExistCoupon->uses_total;

                $deviceId = $_COOKIE['device_id'];
                if (Auth::guard('fronts')->user()) {
                    $customer = Auth::guard('fronts')->user();
                    $customerId = $customer->customer_id;
                } else {
                    $customerId = '';
                }
                $where = "uuid != ''";
                if ($customerId) {
                    $where = " user_id = '$customerId' ";
                } else {
                    if ($deviceId) {
                        $where = " device_id = '$deviceId' ";
                    }
                }

                $cartAmount = Cart::whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                    ->sum('cart.sub_total');
                $amount = $cartAmount;

                if ($voucher_applicable_from <= $currentDate && $voucher_applicable_to >= $currentDate) {

                    $uses_voucher = VoucherHistory::where('voucher_id', $checkExistCoupon->voucher_id)->count();
                    $voucherData = Voucher::where('voucher_id', $voucherId)->first();
                    $voucher_max_amount = $voucherData->maximum_amount;
                    $voucher_min_amount = $voucherData->minimum_amount;

                    if (!empty($voucherData->voucher_products)) {
                        $voucher_product = explode(',', $voucherData->voucher_products);
                        $cartProduct = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                            ->whereRaw($where)->whereIn('cart_detail.product_id', $voucher_product)->count();
                    }

                    if (!empty($voucherData->voucher_categories)) {
                        $voucher_category = explode(',', $voucherData->voucher_categories);
                        $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                        if (!empty($cartcategory)) {
                            $prodId = array();
                            foreach ($cartcategory as $catKey => $catValue) {
                                array_push($prodId, $catValue->product_id);
                            }
                            $cartCatProduct = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                                ->whereRaw($where)->whereIn('cart_detail.product_id', $prodId)->count();
                        }
                    }

                    $total_uses_voucher = VoucherHistory::where('voucher_id', $voucherId)->count();

                    if (floatval($amount) >= $voucher_max_amount && $voucher_max_amount != 0) {
                        return response()->json([
                            'status' => 404,
                            'show' => true,
                            'message' => 'Your amount is not grater then ' . $voucher_max_amount//trans('api.coupon_amount_not_valid')
                        ]);
                    } elseif (floatval($amount) <= $voucher_min_amount && $voucher_min_amount != 0) {
                        return response()->json([
                            'status' => 404,
                            'show' => true,
                            'message' => 'Your amount is not less then ' . $voucher_max_amount//trans('api.coupon_amount_not_valid')
                        ]);
                    } elseif ($total_uses_voucher >= $voucher_total_uses) {
                        return response()->json([
                            'status' => 404,
                            'show' => true,
                            'message' => 'Voucher uses limitation is over'//trans('api.coupon_amount_not_valid')
                        ]);
                    } elseif (floatval($amount) < $voucherData->voucher_discount && $voucherData->voucher_discount_type == 1 && $voucher_min_amount == 0 && $voucher_max_amount == 0) {
                        return response()->json([
                            'status' => 404,
                            'show' => true,
                            'message' => 'Minimum amount is required'
                        ]);
                    } else {
                        return response()->json([
                            'status' => 200,
                            'show' => true,
                            'message' => trans('frontend/common.voucher_success'),
                            'voucher_id' => $checkExistCoupon->voucher_id,
                            'branchSlug' => $branchSlug,
                        ]);
                    }

                } else {
                    return response()->json([
                        'status' => 404,
                        'show' => true,
                        'message' => trans('frontend/common.voucher_expired')
                    ]);
                }

            } else {
                return response()->json([
                    'status' => 404,
                    'show' => true,
                    'message' => trans('frontend/common.invalid_voucher_code')
                ]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Check voucher : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    /**
     * Apply voucher
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyVoucher(Request $request)
    {
        Helper::log('Apply voucher : start');
        DB::beginTransaction();
        try {
            $voucherId = $request->voucher_id;
            $branchSlug = $request->branchSlug;

            $amount = 0;
            $currentDate = date('Y-m-d');
            $grand_total = 0;
            $totalDiscount = 0;
            $subTotal = 0;
            $branchTax = '';
            $totalTax = 0;

            $branchData = Branch::getBranchDataBySlug($branchSlug);
            if ($branchData) {

                $checkExistCoupon = Voucher::where(['voucher_id' => $voucherId, 'status' => 1])->first();

                if (!empty($checkExistCoupon)) {

                    $branchId = $branchData['branch_id'];

                    $deviceId = $_COOKIE['device_id'];
                    if (Auth::guard('fronts')->user()) {
                        $customer = Auth::guard('fronts')->user();
                        $customerId = $customer->customer_id;
                    } else {
                        $customerId = '';
                    }
                    $where = "uuid != ''";
                    if ($customerId) {
                        $where = " user_id = '$customerId' ";
                    } else {
                        if ($deviceId) {
                            $where = " device_id = '$deviceId' ";
                        }
                    }

                    $cartAmount = Cart::whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])
                        ->sum('cart.sub_total');
                    $amount = $cartAmount;

                    $voucherData = Voucher::where('voucher_id', $voucherId)->first();
                    $ids = [];
                    if (!empty($voucherData->voucher_products) && empty($voucherData->voucher_categories)) {
                        $voucher_product = explode(',', $voucherData->voucher_products);
                        array_merge($ids, $voucher_product);
                    } elseif (empty($voucherData->voucher_products) && !empty($voucherData->voucher_categories)) {
                        $voucher_category = explode(',', $voucherData->voucher_categories);
                        $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                        if (!empty($cartcategory)) {
                            foreach ($cartcategory as $catKey => $catValue) {
                                array_push($ids, $catValue->product_id);
                            }
                        }
                    } elseif (!empty($voucherData->voucher_products) && !empty($voucherData->voucher_categories)) {
                        $voucher_product = explode(',', $voucherData->voucher_products);
                        $ids = array_unique(array_merge($ids, $voucher_product));
                        $voucher_category = explode(',', $voucherData->voucher_categories);
                        $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                        if (!empty($cartcategory)) {
                            foreach ($cartcategory as $catKey => $catValue) {
                                array_push($ids, $catValue->product_id);
                            }
                        }
                    } else {
                        if ($voucherData->voucher_discount_type == 1) {
                            $discount = $voucherData->voucher_discount;
                            $discount_type = 1;
                        } else {
                            $discount = ($amount * $voucherData->voucher_discount) / 100;
                            $discount_type = 2;
                        }

                        $totalDiscount = $discount;

                    }
                    $ids = array_unique($ids);
                    $cartCatProduct = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                        ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->whereIn('cart_detail.product_id', $ids)->get();
                    if (count($cartCatProduct) > 0) {
                        foreach ($cartCatProduct as $key => $value) {
                            $subTotal = $value->sub_total;

                            if ($voucherData->voucher_discount_type == 1) {
                                $discount = $voucherData->voucher_discount;
                                $discount_type = 1;
                            } else {
                                $discount = ($subTotal * $voucherData->voucher_discount) / 100;
                                $discount_type = 2;
                            }

                            $totalDiscount += $discount;
                        }
                    } else {
                        if ($voucherData->voucher_discount_type == 1) {
                            $discount = $voucherData->voucher_discount;
                            $discount_type = 1;
                        } else {
                            $discount = ($amount * $voucherData->voucher_discount) / 100;
                            $discount_type = 2;
                        }

                        $totalDiscount = $discount;

                    }


                    //$branchTax = BranchTax::where(['branch_id' => $branchId, 'status' => 1])->get();
                    $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                        ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                        ->select('tax.*')
                        ->get();
                    if (!empty($branchTax)) {
                        foreach ($branchTax as $key => $value) {
                            $taxId = $value->tax_id;
                            //$taxData = Tax::where('tax_id', $taxId)->select('code','rate')->first();
                            $branchTax[$key]['code'] = $value->code;
                            $taxRate = $value->rate;
                            $tax = ($amount - $totalDiscount) * $taxRate / 100;
                            $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                            $totalTax += $tax;
                        }
                    }


                    $amount = $amount + $totalTax - $totalDiscount;
                    $grandtotal = number_format($amount,2);
                    $n = explode('.',$grandtotal);
                    $t = 5 * round($n[1] / 5);
                    if($t >= 100){
                        $grand_total = number_format($n[0] + 1, 2);
                    } else {
                        $grand_total = $n[0].'.'.$t;
                    }

                    Helper::log('Apply voucher : finish');
                    DB::commit();
                    return response()->json(['status' => 200, 'message' => trans('frontend/common.voucher_success'), 'url' => url()->previous(), 'voucher_id' => $voucherId, 'branchTax' => $branchTax, 'discount' => number_format($totalDiscount, 2), 'amount' => number_format($grand_total, 2)]);

                } else {
                    return response()->json([
                        'status' => 404,
                        'show' => true,
                        'message' => trans('frontend/common.invalid_voucher_code')
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 404,
                    'show' => true,
                    'message' => trans('frontend/common.branch_not_exist')
                ]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Apply voucher : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    /**
     * Payment Options Popup
     * @param Request $request
     */
    public function paymentOptions($branchSlug, $mobile, $email = null)
    {
        Helper::log('Cart Select Payment : start');
        try {
            $branchData = Branch::getBranchDataBySlug($branchSlug);
            $branchId = $branchData['branch_id'];

            $paymentData = Payment::where('status', 1)->where('is_parent',0)->get();
            if(!empty($paymentData)){
                foreach ($paymentData as $key => $value){
                    $paymentImage = Assets::where(['asset_type'=>3,'asset_type_id'=>$value->payment_id,'status'=>1])->select('asset_path')->orderBy('asset_id','DESC')->first();
                    if(!empty($paymentImage)){
                        if(!empty($paymentImage->asset_path) && file_exists(public_path($paymentImage->asset_path))){
                            $paymentData[$key]['asset_path'] = $paymentImage->asset_path;
                        } else {
                            $paymentData[$key]['asset_path'] = config('constants.default_product');
                        }
                    } else {
                        $paymentData[$key]['asset_path'] = config('constants.default_product');
                    }

                    $parentPayment = Payment::where(['is_parent'=>$value->payment_id,'status'=>1])->get();
                    if(!empty($parentPayment)){
                        foreach ($parentPayment as $skey => $svalue){
                            $paymentImage = Assets::where(['asset_type'=>3,'asset_type_id'=>$svalue->payment_id,'status'=>1])->select('asset_path')->orderBy('asset_id','DESC')->first();
                            if(!empty($paymentImage)){
                                if(!empty($paymentImage->asset_path) && file_exists(public_path($paymentImage->asset_path))){
                                    $parentPayment[$skey]['asset_path'] = $paymentImage->asset_path;
                                } else {
                                    $parentPayment[$skey]['asset_path'] = config('constants.default_product');
                                }
                            } else {
                                $parentPayment[$skey]['asset_path'] = config('constants.default_product');
                            }
                        }
                        $paymentData[$key]['sub_payment'] = $parentPayment;
                    } else {
                        $paymentData[$key]['sub_payment'] = '';
                    }
                }
            }

            return view('frontend.popup.select_payment', compact('paymentData', 'branchSlug', 'mobile', 'email'));

        } catch (\Exception $exception) {
            Helper::log('Payment selection Exception:' . $exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    /**
     * Create Order
     * @param Request $request
     */
    public function createOrder(Request $request)
    {
        Helper::log('Create Order : Start');
        DB::beginTransaction();
        try {
            $branchSlug = $request->branchSlug;
            $paymentId = $request->payment_id;
            $voucher_id = $request->voucher_id;
            $voucher_amount = $request->voucher_amount;
            $cust_mobile = $request->mobile;
            $cust_email = $request->email;
            $sub_total = 0;
            $grand_total = 0;
            $discount = 0;
            $prod_discount = 0;
            $total_discount = 0;
            $total_after_discount = 0;
            $total = 0;
            $totalTax = 0;
            $currentDate = date('Y-m-d');
            $invoice_no = '';

            $branchData = Branch::getBranchDataBySlug($branchSlug);
            $branchId = $branchData['branch_id'];

            $branchData = Branch::where('branch_id', $branchId)->first();
            $invoice_no = $branchData->order_prefix . $branchData->invoice_start;

            $deviceId = $_COOKIE['device_id'];

            if (Auth::guard('fronts')->user()) {
                $customerId = Auth::guard('fronts')->user()->customer_id;
                $customer = Customer::where('customer_id', $customerId)->first();
            }
            $where = "uuid != ''";
            if (!empty($customerId)) {
                $where = " user_id = '$customerId' ";
            } else {
                if ($deviceId) {
                    $where = " device_id = '$deviceId' ";
                }
            }

            $cart = Cart::whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->first();
            //$cartCount = count($cart);
            if ($cart) {
                $cart_id = $cart->cart_id;
                $subTotal = $cart->sub_total;
                $grand_total = $cart->grand_total;
                $totalTax = $cart->tax;
                $cart_order_number = date('Ymd') . Helper::randomNumber(6);

                $updateCart = [
                    'cart_payment_id' => $paymentId,
                    'cart_payment_status' => 1,
                    'cust_mobile' => $cust_mobile,
                    'cust_email' => $cust_email,
                    'cart_order_number' => $cart_order_number
                ];

                if (!empty($voucher_id)) {
                    $insertOrder['voucher_id'] = $voucher_id;
                    $voucherData = Voucher::where('voucher_id', $voucher_id)->first();
                    $ids = [];
                    if (empty($voucherData->voucher_categories) && empty($voucherData->voucher_products)) {

                        if ($voucherData->voucher_discount_type == 1) {
                            $prod_discount = $voucherData->voucher_discount;
                            $discount_type = 1;
                        } else {
                            $prod_discount = ($subTotal * $voucherData->voucher_discount) / 100;
                            $discount_type = 2;
                        }
                        $total_discount = $prod_discount;
                        $totalTax = 0;
                        if ($total_discount > 0) {
                            //$branchTax = BranchTax::where(['branch_id' => $branchId, 'status' => 1])->get();
                            $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                                ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                                ->select('tax.*')
                                ->get();
                            if (!empty($branchTax)) {
                                foreach ($branchTax as $key => $value) {
                                    $taxId = $value->tax_id;
                                    //$taxData = Tax::where('tax_id', $taxId)->select('code','rate')->first();
                                    $branchTax[$key]['code'] = $value->code;
                                    $taxRate = $value->rate;
                                    $tax = ($subTotal - $total_discount) * $taxRate / 100;
                                    $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                                    $totalTax += $tax;
                                }

                                $updateCart['tax_json'] = \GuzzleHttp\json_encode($branchTax);
                            }
                        }

                        $updateCart['discount'] = $total_discount;
                        $updateCart['tax'] = $totalTax;
                        $updateCart['discount_type'] = $discount_type;
                        $updateCart['voucher_id'] = $voucher_id;
                        $updateCart['voucher_detail'] = \GuzzleHttp\json_encode($voucherData);

                        $total_after_discount = $sub_total - $total_discount;

                        $updateCart['sub_total_after_discount'] = $total_after_discount;

                        $grandtotal = number_format($total_after_discount + $totalTax ,2);
                        $n = explode('.',$grandtotal);
                        $t = 5 * round($n[1] / 5);
                        if($t >= 100){
                            $grand_total = number_format($n[0] + 1, 2);
                        } else {
                            $grand_total = $n[0].'.'.$t;
                        }
                        //$grand_total = $total_after_discount + $totalTax;

                        $updateCart['grand_total'] = $grand_total;
                        $updateCart['created_at'] = config('constants.date_time');

                    } else {
                        if (!empty($voucherData->voucher_products) && empty($voucherData->voucher_categories)) {
                            $voucher_product = explode(',', $voucherData->voucher_products);
                            array_merge($ids, $voucher_product);
                        } elseif (empty($voucherData->voucher_products) && !empty($voucherData->voucher_categories)) {
                            $voucher_category = explode(',', $voucherData->voucher_categories);
                            $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                            if (!empty($cartcategory)) {
                                foreach ($cartcategory as $catKey => $catValue) {
                                    array_push($ids, $catValue->product_id);
                                }
                            }
                        } elseif (!empty($voucherData->voucher_products) && !empty($voucherData->voucher_categories)) {
                            $voucher_product = explode(',', $voucherData->voucher_products);
                            $ids = array_unique(array_merge($ids, $voucher_product));
                            $voucher_category = explode(',', $voucherData->voucher_categories);
                            $cartcategory = ProductCategory::whereIn('category_id', $voucher_category)->get();
                            if (!empty($cartcategory)) {
                                foreach ($cartcategory as $catKey => $catValue) {
                                    array_push($ids, $catValue->product_id);
                                }
                            }
                        }

                        $ids = array_unique($ids);
                        $cartCatProduct = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                            ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->whereIn('cart_detail.product_id', $ids)->get();
                        if (!empty($cartCatProduct)) {
                            foreach ($cartCatProduct as $key => $value) {
                                $sub_total = $value->sub_total;

                                if ($voucherData->voucher_discount_type == 1) {
                                    $discount = $voucherData->voucher_discount;
                                    $discount_type = 1;
                                } else {
                                    $discount = ($sub_total * $voucherData->voucher_discount) / 100;
                                    $discount_type = 2;
                                }

                                $total_discount += $discount;
                            }

                            $totalTax = 0;
                            if ($total_discount > 0) {
                                //$branchTax = BranchTax::where(['branch_id' => $branchId, 'status' => 1])->get();
                                $branchTax = BranchTax::leftjoin('tax', 'tax.tax_id', 'branch_tax.tax_id')
                                    ->where(['branch_tax.branch_id' => $branchId, 'branch_tax.status' => 1])
                                    ->select('tax.*')
                                    ->get();
                                if (!empty($branchTax)) {
                                    foreach ($branchTax as $key => $value) {
                                        $taxId = $value->tax_id;
                                        //$taxData = Tax::where('tax_id', $taxId)->select('code','rate')->first();
                                        $branchTax[$key]['code'] = $value->code;
                                        $taxRate = $value->rate;
                                        $tax = ($subTotal - $total_discount) * $taxRate / 100;
                                        $branchTax[$key]['taxAmount'] = number_format($tax, 2);
                                        $totalTax += $tax;
                                    }
                                }
                            }

                            $updateCart['discount'] = $total_discount;
                            $updateCart['tax'] = $totalTax;
                            $updateCart['discount_type'] = $discount_type;
                            $updateCart['voucher_id'] = $voucher_id;
                            $updateCart['voucher_detail'] = \GuzzleHttp\json_encode($voucherData);

                            $total_after_discount = $subTotal - $total_discount;

                            $updateCart['sub_total_after_discount'] = $total_after_discount;

                            $grandtotal = number_format($total_after_discount + $totalTax ,2);
                            $n = explode('.',$grandtotal);
                            $t = 5 * round($n[1] / 5);
                            if($t >= 100){
                                $grand_total = number_format($n[0] + 1, 2);
                            } else {
                                $grand_total = $n[0].'.'.$t;
                            }

                            //$grand_total = $total_after_discount + $totalTax;
                            $updateCart['grand_total'] = $grand_total;
                            $updateCart['created_at'] = config('constants.date_time');
                        }
                    }
                }

                if (Auth::guard('fronts')->user()) {
                    $order = Cart::where(['cart_id' => $cart_id, 'user_id' => $customerId])->update($updateCart);
                } else {
                    $order = Cart::where(['cart_id' => $cart_id, 'device_id' => $deviceId])->update($updateCart);
                }

                /* Update Inventory */
                /*$cartProduct = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'cart.cart_id')
                    ->whereRaw($where)->where(['source' => 1, 'cart_payment_status' => 0, 'branch_id' => $branchId])->get();
                foreach ($cartProduct as $key => $value) {
                    $productId = $value->product_id;
                    $cartQty = $value->product_qty;
                    $productData = Product::where(['product_id'=>$productId,'has_inventory'=>1])->first();
                    if(!empty($productData)) {
                        $productInventory = ProductStoreInventory::where(['branch_id' => $branchId, 'product_id' => $productId])->first();
                        $inventoryQty = $productInventory->qty;
                        $qty = $inventoryQty - $cartQty;
                        $updateInventory = [
                            'qty' => $qty
                        ];
                        ProductStoreInventory::where(['branch_id' => $branchId, 'product_id' => $productId])->update($updateInventory);

                        $updateInventoryLog = [
                            'uuid' => Helper::getUuid(),
                            'inventory_id' => $cart_id,
                            'branch_id' => $branchId,
                            'product_id' => $productId,
                            'il_type' => 2,
                            'qty' => $qty,
                            'qty_before_change' => $inventoryQty,
                            'qty_after_change' => $qty,
                            'updated_at' => config('constants.date_time'),
                        ];
                        if (Auth::guard('fronts')->user()) {
                            $updateInventoryLog['employe_id'] = $customerId;
                            $updateInventoryLog['updated_by'] = $customerId;
                        }
                        ProductStoreInventoryLog::create($updateInventoryLog);
                    }

                }*/

                DB::commit();
                Helper::log('Create Order : finish');
                return response()->json([
                    'status' => 200,
                    'message' => trans('frontend/common.order_success'),
                    'uuid' => Helper::getUuid(),
                    'order_number' => $cart_order_number
                ]);

            }

        } catch (\Exception $exception) {
            Helper::log('Create Order Exception:' . $exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }

    public function aboutUs(Request $request)
    {
        return view('frontend.about.about-us');
    }

    public function contactUs(Request $request)
    {
        return view('frontend.contact-us.contact-us');
    }

    public function contactUsPost(Request $request)
    {
        DB::beginTransaction();
        Helper::log('Contact Us Request : start');
        try {
            $userData = Auth::guard('fronts')->user();
            $loginId = '';
            if (!empty($userData)) {
                $loginId = $userData->customer_id;
            }

            $name = $request->name;
            $email = $request->email;
            $subject = $request->subject;
            $message = $request->message;

            $checkRequest = ContactUs::where('email', $email)->count();
            if ($checkRequest > 0) {
                return response()->json(['status' => 422, 'show' => true, 'message' => 'You have already sended message!']);
            } else {
                $insertData = [
                    'uuid' => Helper::getUuid(),
                    'name' => $name,
                    'email' => $email,
                    'subject' => $subject,
                    'message' => $message,
                ];
                if ($loginId) {
                    $insertData['created_by'] = $loginId;
                }
                ContactUs::create($insertData);
                DB::commit();
                Helper::log('Contact Request : finish');
                return response()->json([
                    'status' => 200,
                    'show' => true,
                    'message' => 'Your message has been sent. Thank you!'
                ]);
            }

        } catch (\Exception $exception) {
            Helper::log('Contact Us Request : exception');
            Helper::log($exception);
            return response()->json(["status" => 500, "message" => "Ooops...something went wrong."]);
        }
    }

    public function orderSuccess($order_number, $uuid)
    {
        return view('frontend.order-success', compact('uuid', 'order_number'));
    }

    /**
     * Order List Data
     * @param $branchSlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function orderList($branchSlug)
    {
        Helper::log('Order List : Start');
        try {
            $branchData = Branch::getBranchDataBySlug($branchSlug);
            $branchId = $branchData['branch_id'];

            $customerId = Auth::guard('fronts')->user()->customer_id;
            $customer = Customer::where('customer_id', $customerId)->first();

            /* Cart Order Data */
            $orderData = Order::leftjoin('order_detail', 'order_detail.order_id', 'order.order_id')
                ->where(['customer_id' => $customerId, 'order_source' => 1])
                ->get();
            /* Cart Order Data */
            /*$cartData = Cart::leftjoin('cart_detail', 'cart_detail.cart_id', 'cart.cart_id')
                ->where(['cart_payment_status'=>1,'source' => 1])
                ->get();*/

            return view('frontend.order-list', compact('branchSlug', 'orderData'));

        } catch (\Exception $exception) {
            Helper::log('Order List Exception:' . $exception);
            return response()->json(['status' => 500, 'message' => trans('frontend/common.oops')]);
        }
    }
}
