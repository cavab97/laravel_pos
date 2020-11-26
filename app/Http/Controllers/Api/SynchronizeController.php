<?php

namespace App\Http\Controllers\Api;

use App\Models\Assets;
use App\Models\Attributes;
use App\Models\Branch;
use App\Models\BranchTax;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\CartSubDetail;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\CategoryBranch;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Helper;
use App\Models\Kitchen;
use App\Models\Modifier;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PosPermission;
use App\Models\PosRolePermission;
use App\Models\PriceType;
use App\Models\Printer;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductBranch;
use App\Models\ProductCategory;
use App\Models\ProductModifier;
use App\Models\ProductStoreInventory;
use App\Models\ProductStoreInventoryLog;
use App\Models\Roles;
use App\Models\SetMeal;
use App\Models\SetMealBranch;
use App\Models\SetMealProduct;
use App\Models\Shift;
use App\Models\States;
use App\Models\Table;
use App\Models\Tax;
use App\Models\Terminal;
use App\Models\UserBranch;
use App\Models\UserPosPermission;
use App\Models\Voucher;
use App\User;
use App\Http\Controllers\Controller;
use http\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SynchronizeController extends Controller
{

    /*
     * @method   : Appdata
     * @params   : datetime, branchId
     * @respose  : Json updated data secound time
     */

    public function appDataTable(Request $request, $locale)
    {
        Helper::log('AppData Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);
            //$timezonetype = "COMPANY CONFIGURATION";
            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');
            //$response['timezonetype'] = $timezonetype;
            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;

            if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } elseif (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;

                // Branch Data Collection
                $loadBranch = Branch::withTrashed()->where(['branch_id' => $branchId])->first();
                $pushBranch = [];
                if (!empty($loadBranch)) {
                    $loadBranch['base64'] = '';
                    if ($loadBranch->branch_banner != '') {
                        $file = asset($loadBranch->branch_banner);
                        $loadBranch['base64'] = $this->getImageDataFromUrl($file);
                    }
                    $pushBranch[] = $loadBranch;
                    /*foreach ($loadBranch as $branchInfo) {
                        $branchInfo['base64'] = '';
                        if ($branchInfo->branch_banner != '') {
                            $file = asset($branchInfo->branch_banner);
                            $branchInfo['base64'] = $this->getImageDataFromUrl($file);
                        }
                        $pushBranch[] = $branchInfo;
                    }*/
                }
                $response['branch'] = $pushBranch;

                //Branch Tax Data collection
                $loadBranchTax = BranchTax::where('branch_id', $branchId)->get();
                $response['branch_tax'] = $loadBranchTax;

                // Terminal Data collection
                $loadTerminal = Terminal::withTrashed()->where(['terminal_id' => $terminalId])->first();
                $response['terminal'] = $loadTerminal;

                // Table Data collection
                $loadTable = Table::withTrashed()->where(['branch_id' => $branchId])->get()->toArray();
                $response['table'] = $loadTable;

                // Categories Data collection
                $categoryIds = CategoryBranch::where('branch_id', $branchId)->select('category_id')->get();
                $loadCategories = Category::withTrashed()->whereIn('category_id', $categoryIds)->get()->toArray();
                $response['category'] = $loadCategories;

                // Categories Branch Data collection
                $loadCategoriesBranch = CategoryBranch::where(['branch_id' => $branchId])->get()->toArray();
                $response['category_branch'] = $loadCategoriesBranch;

                // Products Data collection
                $productIds = ProductBranch::where('branch_id', $branchId)->select('product_id')->get();
                $productIdss = ProductBranch::where('branch_id', $branchId)->pluck('product_id');
                $loadProducts = Product::whereIn('product_id', $productIds)->get()->toArray();
                $response['product'] = $loadProducts;

                // Products ProductsImage collection
                $loadProductsImage = Assets::whereIn('asset_type_id', $productIds)->where('asset_type', 1)->get()->toArray();
                $pusImage = [];
                if (!empty($loadProductsImage)) {
                    foreach ($loadProductsImage as $proImage) {
                        $proImage['base64'] = '';
                        if ($proImage['asset_path'] != "") {
                            $file = asset($proImage['asset_path']);
                            $proImage['base64'] = $this->getImageDataFromUrl($file);
                        }
                        $pusImage[] = $proImage;
                    }
                }
                $response['product_image'] = $pusImage;

                // Attribute Data collection
                $loadAttribute = Attributes::withTrashed()->get()->toArray();
                $response['attributes'] = $loadAttribute;

                // Category Attribute Data collection
                $loadCatAttribute = CategoryAttribute::get()->toArray();
                $response['category_attribute'] = $loadCatAttribute;

                // Modifier Data collection
                $loadModifier = Modifier::withTrashed()->get()->toArray();
                $response['modifier'] = $loadModifier;

                // Products Branch collection //ProductsBranch
                $loadProductsBranch = ProductBranch::where(['branch_id' => $branchId])->get()->toArray();
                $response['product_branch'] = $loadProductsBranch;  //product_branch

                // Products ProductsCategories collection
                $loadProductsCategirues = ProductCategory::whereIn('product_id', $productIds)->get()->toArray();
                $response['product_category'] = $loadProductsCategirues;

                // Products Attributes Data collection
                $loadProductsAttribute = ProductAttribute::whereIn('product_id', $productIds)->get()->toArray();
                $response['product_attribute'] = $loadProductsAttribute;

                // Products Modifiers Data collection
                $loadProductsModifiers = ProductModifier::whereIn('product_id', $productIds)->get()->toArray();
                $response['product_modifier'] = $loadProductsModifiers;
                $loadPriceType = PriceType::withTrashed()->get()->toArray();
                $response['price_type'] = $loadPriceType;

                //paymentoptions Data collection
                $loadPaymentoptions = Payment::get()->toArray();
                $response['payment'] = $loadPaymentoptions;

                //printers Data collection
                $loadPrinters = Printer::withTrashed()->get()->toArray();
                $response['printer'] = $loadPrinters;

                //product_store_inventory Data collection
                $loadStoreInventory = ProductStoreInventory::where(['branch_id' => $branchId])->get()->toArray();
                $response['product_store_inventory'] = $loadStoreInventory;

                // User Data collection
                $userIds = UserBranch::where('branch_id', $branchId)->select('user_id')->get();
                $loadUsers = User::withTrashed()->whereIn('id', $userIds)->get()->toArray();
                $response['user'] = $loadUsers;

                // Shift Data collection
                $loadShift = Shift::where(['branch_id' => $branchId])->get()->toArray();
                $response['shift'] = $loadShift;

                // Role Data collection
                $loadrole = Roles::whereNotIn('role_id',Roles::$notIn)->get()->toArray();
                $response['role'] = $loadrole;

                // Voucher Data collection
                $loadvoucher = Voucher::withTrashed()->get()->toArray();
                $response['voucher'] = $loadvoucher;

                // Customer Data collection
                $loadCustomer = Customer::withTrashed()->where('updated_at', '>=', $response['serverdatetime'])->get()->toArray();
                $response['customer'] = $loadCustomer;

                // Customer Address Data collection
                $loadCustomerAddress = CustomerAddress::withTrashed()->where('updated_at', '>=', $response['serverdatetime'])->get()->toArray();
                $response['customer_address'] = $loadCustomerAddress;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    public function appBranchUserRoleDataTable(Request $request, $locale)
    {
        Helper::log('AppDataTable1 Synch : Start');
        App::setLocale($locale);
        try {

            //GET SET GO...
            $timeStart = microtime(true);
            //$timezonetype = "COMPANY CONFIGURATION";
            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');
            //$response['timezonetype'] = $timezonetype;
            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;
            if (empty($datetime)) {
                $response['postdatetime'] = 0;
            } else {
                $response['postdatetime'] = $datetime;
            }


            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/
            if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;

                // Terminal Data
                $response['terminal'] = $terminalData;

                // Branch Data Collection
                $loadBranch = Branch::withTrashed()->where('branch_id', $branchId)->first();
                $pushBranch = [];
                if (!empty($loadBranch)) {
                    $loadBranch['base64'] = '';
                    if ($loadBranch['branch_banner'] != '') {
                        $file = asset($loadBranch['branch_banner']);
                        $loadBranch['base64'] = $this->getImageDataFromUrl($file);
                    }
                    $pushBranch[] = $loadBranch;
                }
                $response['branch'] = $pushBranch;

                //Branch Tax Data collection
                $loadBranchTax = BranchTax::where('branch_id', $branchId)->get();
                $response['branch_tax'] = $loadBranchTax;

                //Tax Data collection
                $branchIdsA = BranchTax::where('branch_id', $branchId)->select('tax_id')->get();
                $loadTax = Tax::leftjoin('branch_tax','branch_tax.tax_id','tax.tax_id')->where('branch_tax.branch_id', $branchId)->select('tax.*')->get();
                $response['tax'] = $loadTax;

                // User Data collection

                $loadUsers = User::withTrashed()->leftjoin('user_branch', 'user_branch.user_id', 'users.id')
                    ->where(DB::raw('COALESCE(users.updated_at,0)'), '>=', $response['postdatetime'])
                    ->where('user_branch.branch_id', $branchId)
                    ->select('users.*')
                    ->get()->toArray();

                $response['user'] = $loadUsers;

                // Role Data collection
                if (!empty($response['postdatetime'])) {
                    $loadrole = Roles::whereNotIn('role_id',[3,4,5])->where(DB::raw('COALESCE(role_updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                } else {
                    $loadrole = Roles::whereNotIn('role_id',Roles::$notIn)->get()->toArray();
                }
                $response['role'] = $loadrole;

                // POS Permission DATA
                $loadPosPermission = PosPermission::where(DB::raw('COALESCE(pos_permission_updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['pos_permission'] = $loadPosPermission;

                // POS Role Permission DATA
                $loadRolePosPermission = PosRolePermission::where(DB::raw('COALESCE(pos_rp_updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['pos_role_permission'] = $loadRolePosPermission;

                // POS User Permission DATA
                $loadUserPosPermission = UserPosPermission::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['user_pos_permission'] = $loadUserPosPermission;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'Branch,Users,Role');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    public function appProductCategoryDataTable(Request $request, $locale)
    {
        Helper::log('AppDataTable Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);
            //$timezonetype = "COMPANY CONFIGURATION";
            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');
            //$response['timezonetype'] = $timezonetype;
            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;
            if (empty($datetime)) {
                $response['postdatetime'] = 0;
            } else {
                $response['postdatetime'] = $datetime;
            }

            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::withTrashed()->where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;

                // Categories Data collection
                //$loadCategories = Category::where(['status' => 1])->get()->toArray();
                $loadCategories = Category::withTrashed()->leftjoin('category_branch', 'category_branch.category_id', 'category.category_id')
                    ->where(DB::raw('COALESCE(category.updated_at,0)'), '>=', $response['postdatetime'])
                    ->where('category_branch.branch_id', $branchId)
                    ->select('category.*')
                    ->get()->toArray();

                $response['category'] = $loadCategories;

                // Categories Branch Data collection
                $loadCategoriesBranch = CategoryBranch::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['category_branch'] = $loadCategoriesBranch;

                // Products Data collection
                $productIds = ProductBranch::where('branch_id', $branchId)->select("product_id")->get();
                $loadProducts = Product::withTrashed()->whereIn('product_id', $productIds)
                    ->where(DB::raw('COALESCE(updated_at,0)'),'>=', $response['postdatetime'])
                    /*->select('*',DB::raw("FORMAT(price,2) as price"),DB::raw("FORMAT(old_price,2) as old_price"))*/
                    ->get()->toArray();
                $response['product'] = $loadProducts;

                // Attribute Data collection
                $loadAttribute = Attributes::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['attributes'] = $loadAttribute;

                // Category Attribute Data collection
                $loadCatAttribute = CategoryAttribute::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['category_attribute'] = $loadCatAttribute;

                // Modifier Data collection
                $loadModifier = Modifier::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['modifier'] = $loadModifier;

                // SetMeal Data Collection
                $setmealIds = SetMealBranch::where('branch_id', $branchId)->select('setmeal_id')->get();
                $loadSetmeal = SetMeal::whereIn('setmeal_id', $setmealIds)
                    ->where(DB::raw('COALESCE(updated_at,0)'),'>=', $response['postdatetime'])
                    ->get()->toArray();
                $response['setmeal'] = $loadSetmeal;

                // Setmeal Branch Data collection
                $loadSetmealBranch = SetMealBranch::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['setmeal_branch'] = $loadSetmealBranch;

                // Setmeal Branch Data collection
                $loadSetmealProduct = SetMealProduct::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['setmeal_product'] = $loadSetmealProduct;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'category,product,attribute,modifier');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    public function appProductVariantDataTable(Request $request, $locale)
    {
        Helper::log('AppDataTable Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);
            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');
            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;

            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::withTrashed()->where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;

                if (empty($datetime)) {
                    $response['postdatetime'] = 0;
                } else {
                    $response['postdatetime'] = $datetime;
                }

                //product_store_inventory Data collection
                $loadStoreInventory = ProductStoreInventory::where(['branch_id' => $branchId])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['product_store_inventory'] = $loadStoreInventory;

                //product_store_inventory_log Data collection
                $loadStoreInventoryLog = ProductStoreInventoryLog::where(['branch_id' => $branchId])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['product_store_inventory_log'] = $loadStoreInventoryLog;

                // Products Branch collection //ProductsBranch
                $loadProductsBranch = ProductBranch::where('branch_id', $branchId)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['product_branch'] = $loadProductsBranch;  //product_branch

                // Products ProductsCategories collection
                $productIds = ProductBranch::where('branch_id', $branchId)->select("product_id")->get();
                $loadProductsCategirues = ProductCategory::whereIn('product_id', $productIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['product_category'] = $loadProductsCategirues;

                // Products Attributes Data collection
                //$productIds = ProductBranch::where('branch_id', $branchId)->select("product_id")->get();
                $loadProductsAttribute = ProductAttribute::whereIn('product_id', $productIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['product_attribute'] = $loadProductsAttribute;

                // Products Modifiers Data collection
                //$productIds = ProductBranch::where('branch_id', $branchId)->select("product_id")->get();
                $loadProductsModifiers = ProductModifier::whereIn('product_id', $productIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['product_modifier'] = $loadProductsModifiers;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'product_attribute,product_modifier,product_cateogry,product_branch');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    public function appPrinterPriceTypeDataTable(Request $request, $locale)
    {
        Helper::log('AppDataTable Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);

            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');

            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;

            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::withTrashed()->where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;
                if (empty($datetime)) {
                    $response['postdatetime'] = 0;
                } else {
                    $response['postdatetime'] = $datetime;
                }

                // Products Modifiers Data collection
                $loadPriceType = PriceType::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['price_type'] = $loadPriceType;

                //printers Data collection
                $loadPrinters = Printer::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['printer'] = $loadPrinters;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'price_type,printer');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

	public function appCountryStateCityDataTable(Request $request, $locale)
    {
        Helper::log('AppDataTable Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);

            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');

            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;

            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::withTrashed()->where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;
                if (empty($datetime)) {
                    $response['postdatetime'] = 0;
                } else {
                    $response['postdatetime'] = $datetime;
                }

                // Country Data collection
                $loadCountry = Countries::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['country'] = $loadCountry;

                //States Data collection
                $loadStates = States::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['state'] = $loadStates;

                //City Data collection
                $loadCity = Cities::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['city'] = $loadCity;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'price_type,printer');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    public function appCustomerTerminalPaymentDataTable(Request $request, $locale)
    {
        Helper::log('AppDataTable Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);
            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');

            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;

            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {


                $terminalData = Terminal::withTrashed()->where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;
                if (empty($datetime)) {
                    $response['postdatetime'] = 0;
                } else {
                    $response['postdatetime'] = $datetime;
                }

                //customer Data collection
                $loadCustomers = Customer::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['customer'] = $loadCustomers;

                // Terminal Data collection
                $loadTerminal = Terminal::withTrashed()->where('terminal_id', $terminalId)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->first();
                $response['terminal'] = $loadTerminal;

                // Table Data collection
                $loadTable = Table::withTrashed()->where('branch_id', $branchId)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['table'] = $loadTable;

                //paymentoptions Data collection
                $loadPaymentoptions = Payment::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['payment'] = $loadPaymentoptions;

                // Customer Data collection
                $loadCustomer = Customer::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['customer'] = $loadCustomer;

                // Customer Address Data collection
                $loadCustomerAddress = CustomerAddress::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['customer_address'] = $loadCustomerAddress;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'customer,customer_address,table,payment');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    public function appOrderDataTable(Request $request, $locale)
    {
        Helper::log('AppDataTable Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);

            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');

            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;

            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;
                if (empty($datetime)) {
                    $response['postdatetime'] = 0;
                } else {
                    $response['postdatetime'] = $datetime;
                }

                //Voucher Data collection
                $loadVouchers = Voucher::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['voucher'] = $loadVouchers;

                //Order Data collection
                $loadOrders = Order::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['order'] = $loadOrders;

                //Order Details Data collection
                $loadOrderDetails = OrderDetail::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['order_detail'] = $loadOrderDetails;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'order');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    public function appShiftDataTable(Request $request, $locale)
    {
        Helper::log('AppDataTable Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);

            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');

            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;

            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::withTrashed()->where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;
                if (empty($datetime)) {
                    $response['postdatetime'] = 0;
                } else {
                    $response['postdatetime'] = $datetime;
                }

                //Shift Data collection
                $loadShift = Shift::where('branch_id', $branchId)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
                $response['shift'] = $loadShift;

                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'shift');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    public function productImage(Request $request, $locale)
    {
        Helper::log('AppDataTable Synch : Start');
        App::setLocale($locale);
        try {
            //GET SET GO...
            $timeStart = microtime(true);

            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');

            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;

            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::withTrashed()->where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;
                if (empty($datetime)) {
                    $response['postdatetime'] = 0;
                } else {
                    $response['postdatetime'] = $datetime;
                }
                $offset = $request->offset;
                $limit = 10;

                // Products ProductsImage collection
                $productIds = ProductBranch::where('branch_id', $branchId)->select('product_id')->get()->toArray();
                $setmealIds = SetMealBranch::where('branch_id', $branchId)->select('setmeal_id')->get()->toArray();

                $loadProductsImage = Assets::whereIn('asset_type_id', array_merge($productIds,$setmealIds))->whereIn('asset_type', [1,2,3])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->limit($limit)->offset($offset)->get()->toArray();

                $pusImage = [];
                if (!empty($loadProductsImage)) {
                    foreach ($loadProductsImage as $proImage) {
                        $proImage['base64'] = '';
                        if ($proImage['asset_path'] != "") {
                            $file = asset($proImage['asset_path']);
                            $proImage['base64'] = $this->getImageDataFromUrl($file);
                            //$proImage['base64'] = $file;
                        }
                        $pusImage[] = $proImage;
                    }
                    $response['next_offset'] = $offset + $limit;
                } else {
                    $response['next_offset'] = 0;
                }

                $response['product_image'] = $pusImage;
                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('AppData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'asset');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);
            }

        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method  : TableData
     * @params  : datetime, table
     */

    public function actionTableData(Request $request, $locale)
    {
        Helper::log('Table Synch : Start');
        App::setLocale($locale);
        try {

            $serverdatetime = $request->serverdatetime;
            $table = $request->table;
            $terminalId = $request->terminal_id;

            if (empty($table)) {
                Helper::log('Table Synch : Table required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.table_name_required')]);
            } elseif (empty($serverdatetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } elseif (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {

                $terminalData = Terminal::where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;
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

                $response['timezone'] = $timeZone;
                $response['serverdatetime'] = $serverdatetime;
                $response['branch_id'] = $branchId;
                $response['terminal_id'] = $branchId;

                switch ($table) {
                    case "branch":
                        return $this->loadBranch($response);
                        break;
                    case "branch_tax":
                        return $this->loadBranchTax($response);
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
                    case "category_attribute":
                        return $this->loadCategoryAttributes($response);
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
                    case "customer":
                        return $this->loadCustomer($response);
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
            $loadBranch = Branch::withTrashed()->where('branch_id', $response['branch_id'])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->first();
            if (!empty($loadBranch)) {
                $loadBranch['branch_image_base64'] = '';
                if ($loadBranch['branch_banner'] != '') {
                    $file = asset($loadBranch['branch_banner']);
                    $loadBranch['branch_image_base64'] = $this->getImageDataFromUrl($file);
                }
                $pushBranch[] = $loadBranch;
                /*foreach ($loadBranch as $branchInfo) {
                    $branchInfo['branch_image_base64'] = '';
                    if ($branchInfo['branch_banner'] != '') {
                        $file = asset($branchInfo['branch_banner']);
                        $branchInfo['branch_image_base64'] = $this->getImageDataFromUrl($file);
                    }
                    $pushBranch[] = $branchInfo;
                }*/
            }
            $response['branch'] = Helper::replaceNullWithEmptyString($pushBranch);
            $message = trans('api.retrive_branch_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'branch');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table branch Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Branch Tax
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadBranchTax($response)
    {
        try {
            // Branch Tax Data Collection

            $loadBranchTax = BranchTax::where('branch_id', $response['branch_id'])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->first();

            $response['branch_tax'] = $loadBranchTax;
            $message = trans('api.retrive_branch_tax_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'branch_tax');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table branch tax Query Exception : exception');
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

            $loadPrinter = Printer::withTrashed()->where('branch_id', $response['branch_id'])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['printer'] = Helper::replaceNullWithEmptyString($loadPrinter);
            $message = trans('api.retrive_printer_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'printer');
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

            $categoryIds = CategoryBranch::where('branch_id', $response['branch_id'])->select('category_id')->get();
            $loadCategory = Category::withTrashed()->whereIn('category_id', $categoryIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['category'] = Helper::replaceNullWithEmptyString($loadCategory);
            $message = trans('api.retrive_category_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'category');
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
            $productIds = ProductBranch::where('branch_id', $response['branch_id'])->select('product_id')->get();
            $loadProductsCategories = ProductCategory::whereIn('product_id', $productIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['product_category'] = Helper::replaceNullWithEmptyString($loadProductsCategories);
            $message = trans('api.retrive_product_category_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'product_branch');
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
            $productIds = ProductBranch::where('branch_id', $response['branch_id'])->select('product_id')->get();
            $loadProduct = Product::withTrashed()->whereIn('product_ic', $productIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['product'] = Helper::replaceNullWithEmptyString($loadProduct);
            $message = trans('api.retrive_product_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'product');
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

            $loadProductInventory = ProductStoreInventory::where('branch_id', $response['branch_id'])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['product_store_inventory'] = Helper::replaceNullWithEmptyString($loadProductInventory);
            $message = trans('api.retrive_product_store_inventory_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'product_store-inventory');
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
            $productIds = ProductBranch::where('branch_id', $response['branch_id'])->select('product_id')->get();
            $loadProductAttribute = ProductAttribute::whereIn('product_ic', $productIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['product_attribute'] = Helper::replaceNullWithEmptyString($loadProductAttribute);
            $message = trans('api.retrive_product_attribute_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'product_attribute');
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
            $productIds = ProductBranch::where('branch_id', $response['branch_id'])->select('product_id')->get();
            $loadProductModifier = ProductModifier::whereIn('product_ic', $productIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['product_modifier'] = Helper::replaceNullWithEmptyString($loadProductModifier);
            $message = trans('api.retrive_product_modifier_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'product_modifier');
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

            $loadKitchen = Kitchen::withTrashed()->where('branch_id', $response['branch_id'])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['kitchen_department'] = Helper::replaceNullWithEmptyString($loadKitchen);
            $message = trans('api.retrive_kitchen_department_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'kitchen');
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

            $loadPayment = Payment::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['payment'] = Helper::replaceNullWithEmptyString($loadPayment);
            $message = trans('api.retrive_payment_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'payment');
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

            $loadPriceType = PriceType::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['price_type'] = Helper::replaceNullWithEmptyString($loadPriceType);
            $message = trans('api.retrive_price_type_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'price_type');
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
            $loadAttributes = Attributes::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['attributes'] = Helper::replaceNullWithEmptyString($loadAttributes);
            $message = trans('api.retrive_attributes_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'attribute');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table attributes Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Category Attributes
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadCategoryAttributes($response)
    {
        try {
            $loadCategoryAttributes = CategoryAttribute::where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['category_attribute'] = $loadCategoryAttributes;
            $message = trans('api.retrive_category_attributes_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'category_attribute');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table category attributes Query Exception : exception');
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
            $loadModifier = Modifier::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['modifier'] = Helper::replaceNullWithEmptyString($loadModifier);
            $message = trans('api.retrive_modifier_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'modifier');
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
            $loadVoucher = Voucher::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['voucher'] = Helper::replaceNullWithEmptyString($loadVoucher);
            $message = trans('api.retrive_voucher_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'voucher');
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
            $loadShift = Shift::where('branch_id', $response['branch_id'])->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['shift'] = Helper::replaceNullWithEmptyString($loadShift);
            $message = trans('api.retrive_shift_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'shift');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table Shift Query Exception : exception');
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
            $productIds = ProductBranch::where('branch_id', $response['branch_id'])->select('product_id')->get();
            $loadProductimage = Assets::whereIn('asset_type_id', $productIds)->where('asset_type', 1)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
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
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'asset');
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
            $loadRole = Roles::whereNotIn('role_id',Roles::$notIn)->where(DB::raw('COALESCE(role_updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['role'] = Helper::replaceNullWithEmptyString($loadRole);
            $message = trans('api.retrive_role_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'role');
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
            $userIds = UserBranch::where('branch_id', $response['branch_id'])->select('user_id')->get();
            $loadUser = User::withTrashed()->whereIn('id', $userIds)->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['users'] = Helper::replaceNullWithEmptyString($loadUser);
            $message = trans('api.retrive_user_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'users');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table User Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Customer
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadCustomer($response)
    {
        try {
            $loadCustomer = Customer::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['customer'] = $loadCustomer;
            $message = trans('api.retrive_customer_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'customer');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table Customer Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }

    /*
     * @method   : Customer Address
     * @params   : datetime
     * @respose  : Json updated data branch table
     */

    protected function loadCustomerAddress($response)
    {
        try {
            $loadCustomerAddress = CustomerAddress::withTrashed()->where(DB::raw('COALESCE(updated_at,0)'), '>=', $response['postdatetime'])->get()->toArray();
            $response['customer_address'] = $loadCustomerAddress;
            $message = trans('api.retrive_customer_address_updated_data');
            Helper::saveTerminalLog($response['terminal_id'], $response['branch_id'], 'Auto Sync', 'SynchronizeAppdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'customer_address');
            return response()->json(['status' => 200, 'show' => false, 'message' => $message, 'data' => $response]);

        } catch (\Exception $exception) {
            Helper::log('Table Customer Address Query Exception : exception');
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

    /*public static function getImageDataFromUrl_new($url,$name)
    {
        if (!file_exists($url)) {
            mkdir($uploadsPath1.'/thumb/', 0777, true);
        }

        $fileSizeKB = 0;
        $size = @filesize($uploadsPath1.'/'.$name);
        if(!empty($size)){
            $fileSizeKB = round($size / 1024);
        }

        if($fileSizeKB > 500){

            Image::frame($url, 0, 100, 100)
                ->save(Yii::getAlias($uploadsPath1.'/thumb/'.$name), ['jpeg_quality' => 50]);
            $thumbpath = $uploadsPath . 'products/thumb/' . $name;
            $urlParts = pathinfo($thumbpath);
            $imagepath = $thumbpath;
        }
        else{
            $urlParts = pathinfo($url);
            $imagepath = $url;
        }

        if ($fileSizeKB > 0) {

            $extension = $urlParts['extension'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imagepath);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($response);
            return $base64;
        }
        else{

            $base64 = 'data:image/jpg;base64';
            return $base64;
        }
    }*/

    public function synchWebOrderData(Request $request, $locale)
    {
        Helper::log('WebOrderDataTable Synch : Start');
        App::setLocale($locale);
        try {

            //GET SET GO...
            $timeStart = microtime(true);
            //$timezonetype = "COMPANY CONFIGURATION";
            $response['timezone'] = Helper::getSettingValue('timezone');//config('app.timezone');
            $response['serverdatetime'] = date('Y-m-d h:i:s');
            //$response['timezonetype'] = $timezonetype;
            $datetime = $request->datetime;
            $terminalId = $request->terminal_id;
            if (empty($datetime)) {
                $response['postdatetime'] = 0;
            } else {
                $response['postdatetime'] = $datetime;
            }


            /*if (empty($datetime)) {
                Helper::log('Table Synch : serverDatetime required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.serverdatetime_required')]);
            } else*/
            if (empty($terminalId)) {
                Helper::log('Table Synch : Terminal required');
                return response()->json(['status' => 422, 'show' => true, "message" => trans('api.terminal_id_required')]);
            } else {
                $terminalData = Terminal::where('terminal_id', $terminalId)->first();
                $branchId = $terminalData->branch_id;

                $cartData = Cart::where(['branch_id'=>$branchId,'source'=>1,'cart_payment_status' => 1])->where(DB::raw('COALESCE(created_at,0)'), '>=', $response['postdatetime'])->select('cart.*')->get();
                if(!empty($cartData)){
                    foreach ($cartData as $key => $value)
                    {
                        $cart_id = $value->cart_id;

                        /* Cart Detail */
                        $cartDetail = CartDetail::where('cart_id',$cart_id)->get();
                        foreach ($cartDetail as $ckey => $cvalue) {

                            /* Cart Attribute */
                            $cartSubDetail = CartSubDetail::where(['cart_id' => $cart_id, 'cart_detail_id' => $cvalue->cart_detail_id])->get();
                            $cartDetail[$ckey]['cart_sub_detail'] = $cartSubDetail;
                        }
                        $cartData[$key]['cart_detail'] = $cartDetail;
                    }
                }
                $response['cart'] = $cartData;
                // total time taking api response
                $timeEnd = microtime(true);
                $response['timetaking'] = $timeEnd - $timeStart;

                Helper::log('WebOrderData synch : Data Synchronize');
                Helper::saveTerminalLog($terminalId, $branchId, 'Web Order Sync', 'Web Order Synchronize Appdata Synchronize Successfully done', date('Y-m-d'), date('H:i:s'), 'cart');
                $message = trans('api.data_synchronize');
                return response()->json(['status' => 200, 'show' => true, 'message' => $message, 'data' => $response]);

            }
        } catch (\Exception $exception) {
            Helper::log('SynchronizeAppdata Query Exception : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'show' => true, 'message' => trans('api.ooops')]);
        }
    }
}
