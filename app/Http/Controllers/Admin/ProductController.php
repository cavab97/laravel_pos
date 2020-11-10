<?php

namespace App\Http\Controllers\Admin;

use App\Models\Assets;
use App\Models\Attributes;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\CategoryBranch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Modifier;
use App\Models\Permissions;
use App\Models\PriceType;
use App\Models\Printer;
use App\Models\ProductBranch;
use App\Models\RolePermission;
use App\Models\Roles;
use App\Models\Branch;
use App\Models\UserBranch;
use App\Models\UserPermission;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductAttribute;
use App\Models\ProductModifier;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_product');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $categoryList = Category::getListForSelectBox1();

        return view('backend.product.index',compact('categoryList'));
    }

    /**
     * Pagination for backend products
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(Request $request)
    {
        $search = $request['sSearch'];
        $start = $request['iDisplayStart'];
        $page_length = $request['iDisplayLength'];

        $iSortCol = $request['iSortCol_0'];
        $col = 'mDataProp_' . $iSortCol;
        $order_by_field = $request->$col;
        if ($order_by_field == 'id') {
            $order_by_field = 'updated_at';
        }
        $order_by = $request['sSortDir_0'];

        $defaultCondition = 'product.uuid != ""';
        if (!empty($search)) {
            $search = Helper::string_sanitize($search);            
			$defaultCondition .= " AND ( `product`.price LIKE '%$search%' OR `product`.sku LIKE '%$search%' OR `product`.name LIKE '%$search%' ) ";
        }

        $name = $request->input('name', null);
        if ($name != null) {
            $name = Helper::string_sanitize($name);
            $defaultCondition .= " AND (`product`.name LIKE '%$name%' OR `product`.name_2 LIKE '%$name%') ";
        }

        $category_id = $request->input('category_id',null);
        if($category_id != null){
            $productIds = ProductCategory::where('category_id',$category_id)->select('product_id')->get();
            $productIdArray = [];
            if(!empty($productIds)){
                foreach ($productIds as $value){
                    array_push($productIdArray, $value->product_id);
                }
                $implodeIds = implode(',',$productIdArray);
                if (!empty($implodeIds)) {
                    $defaultCondition .= " AND `product`.product_id in ($implodeIds)";
                } else {
                    $defaultCondition .= " AND `product`.product_id in ('$implodeIds')";
                }
            }
        }

        $price = $request->input('price', null);
        $price_opt = $request->input('price_opt', '=');
        if ($price != null) {
            $defaultCondition .= " AND `product`.price $price_opt " . $price;
        }

        $old_price = $request->input('old_price', null);
        $old_price_opt = $request->input('old_price_opt', '=');
        if ($old_price != null) {
            $defaultCondition .= " AND `product`.old_price $old_price_opt " . $old_price;
        }

        $sku = $request->input('sku', null);
        if ($sku != null) {
            $defaultCondition .= " AND `product`.sku LIKE '%$sku%' ";
        }

        $status = $request->input('status', null);
        if ($status != null) {
            $defaultCondition .= " AND `product`.status =" . $status;
        }

        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $from = isset($from_date) ? (date('Y-m-d', strtotime($from_date))) : null;
        $to = isset($to_date) ? (date('Y-m-d', strtotime($to_date))) : null;
        if (empty($from) && !empty($to)) {
            $defaultCondition .= " AND DATE_FORMAT(`product`.updated_at, '%Y-%m-%d') <= '" . $to . "'";
        }
        if (!empty($from) && empty($to)) {
            $defaultCondition .= " AND DATE_FORMAT(`product`.updated_at, '%Y-%m-%d') >= '" . $from . "'";
        }
        if (!empty($from) && !empty($to)) {
            $defaultCondition .= " AND DATE_FORMAT(`product`.updated_at, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
        }


        $userData = Auth::user();
        if ($userData->role == 1) {
            $productCount = Product::leftjoin('price_type', 'price_type.pt_id', 'product.price_type_id')
                ->whereRaw($defaultCondition)
                ->count();
            $productList = Product::leftjoin('price_type', 'price_type.pt_id', 'product.price_type_id')
                ->select('product.*', 'price_type.name AS price_type_name')
                ->whereRaw($defaultCondition)
                ->orderBy('product_id', 'DESC')
                ->limit($page_length)
                ->offset($start)
                ->orderBy($order_by_field, $order_by)
                ->get()->toArray();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $productCount = Product::leftjoin('price_type', 'price_type.pt_id', 'product.price_type_id')
                ->leftjoin('product_branch', 'product_branch.product_id', '=', 'product.product_id')
                ->whereRaw($defaultCondition)
                ->whereIn('product_branch.branch_id', $branchIds)
                ->count();
            $productList = Product::leftjoin('price_type', 'price_type.pt_id', 'product.price_type_id')
                ->leftjoin('product_branch', 'product_branch.product_id', '=', 'product.product_id')
                ->select('product.*', 'price_type.name AS price_type_name')
                ->whereIn('product_branch.branch_id', $branchIds)
                ->whereRaw($defaultCondition)
                ->groupBy('product_id')
                ->limit($page_length)
                ->offset($start)
                ->orderBy($order_by_field, $order_by)
                ->get()->toArray();
        }

        if(!empty($productList)){
            foreach ($productList as $key => $value){
                $category = ProductCategory::where('product_category.product_id', $value['product_id'])->where('status',1)->get();
                if(!empty($category)){
                    $i = 0;
                    $cat_name = '';
                    foreach ($category as $catKey => $catValue){
                        $catData = Category::withTrashed()->where('category_id',$catValue->category_id)->select('name')->first();
						
                        $name = $catData->name;
                        $cat_name .= $name;
                        if (count($category) != ($i + 1)) {
                            $cat_name .= ',';
                        }
                        $i++;
						
                    }
                    $productList[$key]['category_name'] = $cat_name;
                }
            }
        }

        return response()->json([
            "aaData" => $productList,
            "iTotalDisplayRecords" => $productCount,
            "iTotalRecords" => $productCount,
            "sColumns" => $request->sColumns,
            "sEcho" => $request->sEcho,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_product');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $priceTypeList = PriceType::where('status', 1)->get();

        /*Product Category List*/
        $categoryProductList = Category::getListForSelectBox1('', '', 0);
        $categoryProductListHasRac = Category::getListForSelectBox1('', '', 1);
        /*Attribute List*/
        $attributeList = Attributes::where('status', 1)->get();

        /*Modifier List*/
        $modifierList = Modifier::where('status', 1)->get();

        /* Category Attribute List */
        $categoryAttributeList = CategoryAttribute::join('attributes','attributes.ca_id','category_attribute.ca_id')
            ->where('category_attribute.status', 1)
            ->select('category_attribute.*')
            ->groupBy('ca_id')
            ->get();

        /* Branch List */
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
            if(!empty($branchList)){
                foreach ($branchList as $key => $value){
                    $printerList = Printer::where(['branch_id'=>$value['branch_id'], 'status'=>1])->get();
                    $branchList[$key]['printer'] = $printerList;
                }
            }
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->where('status',1)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
            if(!empty($branchList)){
                foreach ($branchList as $key => $value){
                    $printerList = Printer::where(['branch_id'=>$value['branch_id'], 'status'=>1])->get();
                    $branchList[$key]['printer'] = $printerList;
                }
            }
        }

        return view('backend.product.create', compact('categoryProductList', 'priceTypeList', 'attributeList', 'modifierList', 'branchList', 'categoryAttributeList','categoryProductListHasRac'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        Helper::log('branch create : start');
        try {
            $name = trim($request->name);
            $name2 = trim($request->name2);
            $sku = $request->sku;
            $price = $request->price;
            $old_price = $request->old_price;
            $price_type_id = $request->price_type_id;
            $price_type_value = $request->price_type_value;
            $description = $request->description;
            $status = $request->status;
            if ($request->has('has_inventory')) {
                $has_inventory = 1;
            } else {
                $has_inventory = 0;
            }
            if ($request->has('has_rac_managemant')) {
                $has_rac_managemant = 1;
            } else {
                $has_rac_managemant = 0;
            }
            if ($request->has('has_setmeal')) {
                $has_setmeal = 1;
            } else {
                $has_setmeal = 0;
            }

            if ($has_rac_managemant == 1) {
                $category_id = $request->has_category_id;
            } else {
                $category_id = $request->category_id;
            }
            $checkName = Product::where('name', $name)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/product.name_exists')]);
            } else {
                $checkSkuExists = Product::where('sku', $sku)->count();
                if ($checkSkuExists > 0) {
                    Helper::log('Product sku : is exists');
                    return response()->json(['status' => 409, 'message' => trans('backend/product.product_sku_exists')]);
                } else {
                    $insertData = [
                        'uuid' => Helper::getUuid(),
                        'name' => $name,
                        'name_2' => $name2,
                        'slug' => Helper::slugify(trim($name)),
                        'description' => $description,
                        'sku' => $sku,
                        'price_type_id' => $price_type_id,
                        'price_type_value' => $price_type_value,
                        'price' => $price,
                        'old_price' => $old_price,
                        'has_inventory' => $has_inventory,
                        'has_rac_managemant' => $has_rac_managemant,
                        'has_setmeal' => $has_setmeal,
                        'status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id,
                    ];

                    $productData = Product::create($insertData);
                    $productId = $productData->product_id;

                    /*insert Image Data*/
                    if ($request->hasFile('product_images')) {
                        foreach ($request->file('product_images') as $key => $file) {
                            $folder = $this->createDirectory('products');
                            $extension = $file->getClientOriginalExtension();
                            $fileName = time() . '_' . $key . '.' . $extension;
                            $file->move($folder, $fileName);
                            chmod($folder . $fileName, 0777);
                            $image = 'uploads/products/' . $fileName;
                            $imageData = [
                                'uuid' => Helper::getUuid(),
                                'asset_type' => 1,
                                'asset_type_id' => $productId,
                                'asset_path' => $image,
                                'updated_at' => config('constants.date_time'),
                                'updated_by' => Auth::user()->id
                            ];
                            Assets::create($imageData);
                        }
                    }

                    /*insert Category Data*/
                    if ($category_id) {
                        foreach ($category_id as $key => $value) {
                            if ($value) {
                                $insertCatData = [
                                    'product_id' => $productId,
                                    'category_id' => $value,
                                    'branch_id' => 0,
                                    'display_order' => 0,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id,
                                ];
                                ProductCategory::create($insertCatData);
                            }
                        }
                    }

                    /*insert attribute Data*/
                    if ($request->cat_attribute_id) {
                        foreach ($request->cat_attribute_id as $catattkey => $catattvalue) {

                            foreach ($request->attribute_id_[$catattvalue] as $attkey => $attvalue) {
                                $re = 'att_price_'.$attvalue;
                                foreach ($request->$re[$catattvalue] as $inKey => $invalue) {
                                    $insertAttData = [
                                        'uuid' => Helper::getUuid(),
                                        'ca_id' => $catattvalue,
                                        'attribute_id' => $attvalue,
                                        'product_id' => $productId,
                                        'price' => $invalue,//$request->att_price_[$catattvalue][$attkey],
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Auth::user()->id,
                                    ];
                                    ProductAttribute::create($insertAttData);
                                }
                            }
                        }
                    }

                    /*if ($request->attribute_id) {
                        foreach ($request->attribute_id as $attkey => $attvalue) {
                            $insertAttData = [
                                'uuid' => Helper::getUuid(),
                                'attribute_id' => $attvalue,
                                'product_id' => $productId,
                                'price' => $request->att_price[$attkey],
                                'status' => $request->is_enabled[$attkey],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::user()->id,
                            ];
                            ProductAttribute::create($insertAttData);
                        }
                    }*/

                    /*insert modifier Data*/
                    if ($request->modifier_id) {
                        foreach ($request->modifier_id as $modkey => $modvalue) {
                            $insertModData = [
                                'uuid' => Helper::getUuid(),
                                'modifier_id' => $modvalue,
                                'product_id' => $productId,
                                'price' => $request->mod_price[$modkey],
                                'status' => $request->is_enabled_mod[$modkey],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::user()->id,
                            ];
                            ProductModifier::create($insertModData);
                        }
                    }

                    /* Assign Product Branch */
                    $branch_id = $request->branch_id;
                    if (isset($branch_id) && !empty($branch_id)) {
                        foreach ($branch_id as $key => $value) {
                            $insertProBranch = [
                                'uuid' => Helper::getUuid(),
                                'product_id' => $productId,
                                'branch_id' => $value,
                                'warningStockLevel' => $request->warning_stock_level[$value],
                                'display_order' => $request->display_order[$value],
                                'printer_id' => $request->printer_id[$value],
                                'status' => $request->is_enabled_status[$value],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::user()->id,
                            ];
                            ProductBranch::create($insertProBranch);
                        }
                    }

                    Helper::saveLogAction('1', 'Product', 'Store', 'Add new product ' . $productId, Auth::user()->id);
                    DB::commit();
                    Helper::log('Product create : finish');
                    return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Product create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Product', 'Store', 'Add new product Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
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

        $checkPermission = Permissions::checkActionPermission('view_product');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $productData = Product::where('uuid', $uuid)
            ->select('product.*',
                DB::raw('(SELECT name FROM users WHERE id = product.updated_by) AS updated_name'))
            ->first();
        if (!empty($productData)) {
            $productId = $productData->product_id;

            $productData->product_name = $productData->name;
            $productData->product_name2 = $productData->name_2;
            $productData->product_description = $productData->description;

            $priceType = PriceType::where('pt_id', $productData->price_type_id)->select('name')->first();
            $productData->price_type_name = $priceType->name;

            $productCategoryData = ProductCategory::leftjoin('category', 'category.category_id', 'product_category.category_id')                
				->where('product_category.product_id', $productId)
				->where('product_category.status', 1)
                ->select('product_category.*', 'category.name AS category_name')				
                ->get();

            foreach ($productCategoryData as $key => $value) {

                $categoryBreadcrubs = Category::getCategoryTreeIDsLibrary($value->category_id);
                $html = "";
                for ($i = 0; $i < count($categoryBreadcrubs); $i++) {
                    if ($categoryBreadcrubs[$i]['category_id'] != $value->category_id) {
                        $html .= $categoryBreadcrubs[$i]['name'] . " > ";
                    } else {
                        $html .= $categoryBreadcrubs[$i]['name'];
                    }
                }
                $productCategoryData[$key]['category_name'] = $html;

            }
            $productData->category = $productCategoryData;

            $productData->attribute = ProductAttribute::leftjoin('attributes', 'attributes.attribute_id', 'product_attribute.attribute_id')
                ->where('product_id', $productId)
                ->select('attributes.attribute_id', 'attributes.name', 'product_attribute.status', 'product_attribute.price')
                ->get();

            $productData->modifier = ProductModifier::leftjoin('modifier', 'modifier.modifier_id', 'product_modifier.modifier_id')
                ->where('product_id', $productId)
                ->select('modifier.modifier_id', 'modifier.name', 'product_modifier.status', 'product_modifier.price')
                ->get();

            $productData->branch = ProductBranch::leftjoin('branch', 'branch.branch_id', 'product_branch.branch_id')
                ->where('product_id', $productId)
                ->select('branch.branch_id', 'branch.name', 'product_branch.warningStockLevel', 'product_branch.status', 'product_branch.display_order')
                ->orderBy('product_branch.display_order', 'ASC')
                ->get();

            $productData->productImagesData = Assets::where('asset_type', 1)->where('asset_type_id', $productId)->where('status',1)->get();

            return view('backend.product.view', compact('productData'));
        } else {
            return redirect()->back()->with('error', trans('backend/common.oops'));
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
        $checkPermission = Permissions::checkActionPermission('edit_product');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $productData = Product::where('uuid', $uuid)->first();
        if (empty($productData)) {
            Helper::log('Product edit : No record found');
            return redirect()->back()->with('error', trans('backend/common.oops'));
        }
        $productId = $productData->product_id;
        $category = ProductCategory::where('product_category.product_id', $productId)->where('status', 1)->get();
        $i = 0;
        $categoryIds = '';
        foreach ($category as $key => $value) {
            $categoryIds .= $value->category_id;
            if (count($category) != ($i + 1)) {
                $categoryIds .= ',';
            }
            $i++;
        }
        $productData->category = $categoryIds;

        $categoryProductList = Category::getListForSelectBox1('', $categoryIds, 0);
        $categoryProductListHasRac = Category::getListForSelectBox1('', $categoryIds, 1);

        $productData->imagesData = Assets::where('asset_type', 1)->where('status',1)->where('asset_type_id', $productId)->get();

        $priceTypeList = PriceType::where('status', 1)->get();

        /*Attribute List*/
        $attributeList = Attributes::where('status', 1)->get();

        /* Category Attribute List */
        //$categoryAttributeList = DB::table('category_attribute')->where('status', 1)->get();
        $categoryAttributeList = CategoryAttribute::join('attributes','attributes.ca_id','category_attribute.ca_id')
            ->where('category_attribute.status', 1)
            ->select('category_attribute.*')
            ->groupBy('ca_id')
            ->get();

        $catAtt = ProductAttribute::leftjoin('category_attribute', 'category_attribute.ca_id', 'product_attribute.ca_id')
            ->where('product_id', $productId)
            ->where('product_attribute.status', '!=', 2)
            ->select('product_attribute.ca_id', 'category_attribute.name')
            ->groupBy('ca_id')
            ->get();
        if (!empty($catAtt)) {
            foreach ($catAtt as $key => $value) {
                $attribute = Attributes::where('ca_id',$value->ca_id)->get();
                if(!empty($attribute)){
                    foreach ($attribute as $akey => $avalue){
                        $productAttribute = ProductAttribute::where('product_id', $productId)
                            ->where('product_attribute.ca_id', $avalue->ca_id)
                            ->where('product_attribute.attribute_id', $avalue->attribute_id)
                            ->where('product_attribute.status', '!=', 2)
                            ->first();
                        if(!empty($productAttribute)){
                            $attribute[$akey]['price'] = $productAttribute->price;
                        } else {
                            $attribute[$akey]['price'] = '';
                        }
                    }
                }
                /*$productAttribute = ProductAttribute::leftjoin('attributes', 'attributes.attribute_id', 'product_attribute.attribute_id')
                    ->where('product_id', $productId)
                    ->where('product_attribute.ca_id', $value->ca_id)
                    ->where('product_attribute.status', '!=', 2)
                    ->select('product_attribute.*', 'attributes.name')
                    ->get();dd($productAttribute->toArray());*/
                $catAtt[$key]['attribute'] = $attribute;
            }
        }
        $productData->attribute = $catAtt;
        /*$productData->attribute = ProductAttribute::leftjoin('attributes', 'attributes.attribute_id', 'product_attribute.attribute_id')
            ->where('product_id', $productId)
            ->select('product_attribute.*', 'attributes.name')
            ->get();*/

        /*Modifier List*/
        $modifierList = Modifier::where('status', 1)->get();

        $productData->modifier = ProductModifier::leftjoin('modifier', 'modifier.modifier_id', 'product_modifier.modifier_id')
            ->where('product_id', $productId)
            ->where('product_modifier.status', '!=', 2)
            ->select('product_modifier.*', 'modifier.name')
            ->get();

        /* Branch List */
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
            if(!empty($branchList)){
                foreach ($branchList as $key => $value){
                    $printerList = Printer::where(['branch_id'=>$value['branch_id'], 'status'=>1])->get();
                    $branchList[$key]['printer'] = $printerList;
                }
            }
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->where('status',1)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
            if(!empty($branchList)){
                foreach ($branchList as $key => $value){
                    $printerList = Printer::where(['branch_id'=>$value['branch_id'], 'status'=>1])->get();
                    $branchList[$key]['printer'] = $printerList;
                }
            }
        }

        $productData->branchData = ProductBranch::leftjoin('branch', 'branch.branch_id', 'product_branch.branch_id')
            ->where('product_branch.product_id', $productId)
            ->where('product_branch.status', '!=', 2)
            ->select('product_branch.*', 'branch.name')
            ->get();
        $i = 0;
        $branchIds = '';
        foreach ($productData->branchData as $key => $value) {
            $branchIds .= $value->branch_id;
            if (count($productData->branchData) != ($i + 1)) {
                $branchIds .= ',';
            }
            $i++;
        }
        $productData->branch = $branchIds;

        return view('backend.product.edit', compact('productData', 'categoryProductList', 'priceTypeList', 'attributeList', 'modifierList', 'branchList', 'categoryAttributeList', 'categoryProductListHasRac'));
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
        DB::beginTransaction();
        Helper::log('Product update : start');
        try {
            $name = trim($request->name);
            $name2 = trim($request->name2);
            $sku = $request->sku;
            $price = $request->price;
            $old_price = $request->old_price;
            $price_type_id = $request->price_type_id;
            $price_type_value = $request->price_type_value;
            $description = $request->description;

            $status = $request->status;
            $productId = $request->product_id;
            if ($request->has('has_inventory')) {
                $has_inventory = 1;
            } else {
                $has_inventory = 0;
            }
            if ($request->has('has_rac_managemant')) {
                $has_rac_managemant = 1;
            } else {
                $has_rac_managemant = 0;
            }

            if ($request->has('has_setmeal')) {
                $has_setmeal = 1;
            } else {
                $has_setmeal = 0;
            }

            if ($has_rac_managemant == 1) {
                $category_id = $request->has_category_id;
            } else {
                $category_id = $request->category_id;
            }

            $checkName = Product::where('name', $name)->where('uuid', '!=', $uuid)->count();

            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/product.name_exists')]);
            } else {
                $checkSkuExists = Product::where('sku', $sku)->where('uuid', '!=', $uuid)->count();
                if ($checkSkuExists > 0) {
                    Helper::log('Product sku : is exists');
                    return response()->json(['status' => 409, 'message' => trans('backend/product.product_sku_exists')]);
                } else {
                    $updateData = [
                        'name' => $name,
                        'name_2' => $name2,
                        'slug' => Helper::slugify(trim($name)),
                        'description' => $description,
                        'sku' => $sku,
                        'price_type_id' => $price_type_id,
                        'price_type_value' => $price_type_value,
                        'price' => $price,
                        'old_price' => $old_price,
                        'has_inventory' => $has_inventory,
                        'has_rac_managemant' => $has_rac_managemant,
                        'has_setmeal' => $has_setmeal,
                        'status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id,
                    ];

                    $productData = Product::where('uuid', $uuid)->update($updateData);

                    /*insert Image Data*/
                    if ($request->hasFile('product_images')) {
                        foreach ($request->file('product_images') as $key => $file) {
                            $folder = $this->createDirectory('products');
                            $extension = $file->getClientOriginalExtension();
                            $fileName = time() . '_' . $key . '.' . $extension;
                            $file->move($folder, $fileName);
                            chmod($folder . $fileName, 0777);
                            $image = 'uploads/products/' . $fileName;
                            $imageData = [
                                'uuid' => Helper::getUuid(),
                                'asset_type' => 1,
                                'asset_type_id' => $productId,
                                'asset_path' => $image,
                                'updated_at' => config('constants.date_time'),
                                'updated_by' => Auth::user()->id
                            ];
                            Assets::create($imageData);
                        }
                    }

                    /*insert Category Data*/
                    $productcategoryData = ProductCategory::where('product_id', $productId)->get()->toArray();
                    if (isset($productcategoryData) && !empty($productcategoryData)) {
                        $existCategoryArray = array();
                        foreach ($productcategoryData as $key => $val) {
                            array_push($existCategoryArray, $val['category_id']);
                        }
                        $is_exist = true;
                        if (isset($category_id) && !empty($category_id)) {
                            $newCategory = array_diff($category_id, $existCategoryArray);
                            $oldCategoryArray = array_diff($existCategoryArray, $category_id);
                            $updateCategoryArray = array_intersect($existCategoryArray, $category_id);
                            $removeCategoryArray = array_diff($oldCategoryArray, $category_id);
                            if (empty($oldCategoryArray) && empty($updateCategoryArray) && empty($newCategory)) {
                                $is_exist = true;
                            } else {
                                $is_exist = false;
                                /*New insert*/
                                if (isset($newCategory) && !empty($newCategory)) {
                                    foreach ($newCategory as $key => $value) {
                                        if ($value) {
                                            $insertCatData = [
                                                'product_id' => $productId,
                                                'category_id' => $value,
                                                'branch_id' => 0,
                                                'display_order' => 0,
                                                'status' => 1,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => Auth::user()->id,
                                            ];
                                            ProductCategory::create($insertCatData);
                                        }
                                    }
                                }
                                /*status update*/
                                if (isset($updateCategoryArray) && !empty($updateCategoryArray)) {
                                    foreach ($updateCategoryArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 1,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id,
                                        ];

                                        ProductCategory::where(['category_id' => $value, 'product_id' => $productId])->update($updateObj);
                                    }
                                }
                                if (isset($removeCategoryArray) && !empty($removeCategoryArray)) {
                                    foreach ($removeCategoryArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 2,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id,
                                        ];

                                        ProductCategory::where(['category_id' => $value, 'product_id' => $productId])->update($updateObj);
                                    }
                                }
                            }
                        }
                        if ($is_exist == true) {
                            if (isset($existCategoryArray) && !empty($existCategoryArray)) {
                                foreach ($existCategoryArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Auth::user()->id,
                                    ];
                                    ProductCategory::where(['category_id' => $value, 'product_id' => $productId])->update($updateObj);
                                }
                            }
                        }
                    } else {

                        if (isset($category_id) && !empty($category_id)) {
                            foreach ($category_id as $key => $value) {
                                $insertCatData = [
                                    'product_id' => $productId,
                                    'category_id' => $value,
                                    'branch_id' => 0,
                                    'display_order' => 0,
                                    'status' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id,
                                ];
                                ProductCategory::create($insertCatData);
                            }
                        }
                    }

                    /*insert attribute Data*/

                    $cat_attribute_id = $request->cat_attribute_id;
                    $productAttributeData = ProductAttribute::where('product_id', $productId)->get()->toArray();
                    if (isset($productAttributeData) && !empty($productAttributeData)) {
                        $existAttributeArray = array();
                        foreach ($productAttributeData as $key => $val) {
                            array_push($existAttributeArray, $val['ca_id']);
                        }

                        $is_exist = true;
                        if (isset($cat_attribute_id) && !empty($cat_attribute_id)) {
                            $newAttribute = array_diff($cat_attribute_id, $existAttributeArray);
                            $newAttribute = array_unique($newAttribute);
                            $oldAttributeArray = array_diff($existAttributeArray, $cat_attribute_id);
                            $updateAttributeArray = array_intersect($existAttributeArray, $cat_attribute_id);
                            $removeAttributeArray = array_diff($oldAttributeArray, $cat_attribute_id);
                            if (empty($oldAttributeArray) && empty($updateAttributeArray) && empty($newAttribute)) {
                                $is_exist = true;
                            } else {
                                $is_exist = false;
                                /*New insert*/
                                if (isset($newAttribute) && !empty($newAttribute)) {
                                    foreach ($newAttribute as $catattkey => $catattvalue) {

                                        foreach ($request->attribute_id_[$catattvalue] as $attkey => $attvalue) {
                                            $re = 'att_price_'.$attvalue;
                                            foreach ($request->$re[$catattvalue] as $inKey => $invalue) {

                                                $insertAttData = [
                                                    'uuid' => Helper::getUuid(),
                                                    'ca_id' => $catattvalue,
                                                    'attribute_id' => $attvalue,
                                                    'product_id' => $productId,
                                                    'price' => $invalue,//$request->att_price_[$catattvalue][$attkey],
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                    'updated_by' => Auth::user()->id,
                                                ];
                                                ProductAttribute::create($insertAttData);
                                            }
                                        }
                                    }
                                }
                                /*status update*/
                                if (isset($updateAttributeArray) && !empty($updateAttributeArray)) {
                                    foreach ($updateAttributeArray as $key => $value) {
                                        foreach ($request->attribute_id_[$value] as $attkey => $attvalue) {
                                            $re = 'att_price_'.$attvalue;
                                            foreach ($request->$re[$value] as $inKey => $invalue) {
                                                $checkprodAtt = ProductAttribute::where(['ca_id'=>$value,'attribute_id'=>$attvalue,'product_id'=>$productId])->count();
                                                if($checkprodAtt > 0) {
                                                    $updateObj = [
                                                        'price' => $invalue,
                                                        'status' => 1,
                                                        'updated_at' => date('Y-m-d H:i:s'),
                                                        'updated_by' => Auth::user()->id,
                                                    ];

                                                    ProductAttribute::where(['ca_id' => $value, 'attribute_id' => $attvalue, 'product_id' => $productId])->update($updateObj);
                                                } else {
                                                    $insertAttData = [
                                                        'uuid' => Helper::getUuid(),
                                                        'ca_id' => $value,
                                                        'attribute_id' => $attvalue,
                                                        'product_id' => $productId,
                                                        'price' => $invalue,
                                                        'updated_at' => date('Y-m-d H:i:s'),
                                                        'updated_by' => Auth::user()->id,
                                                    ];
                                                    ProductAttribute::create($insertAttData);
                                                }
                                            }
                                        }
                                    }
                                }
                                if (isset($removeAttributeArray) && !empty($removeAttributeArray)) {
                                    foreach ($removeAttributeArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 2,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id,
                                        ];

                                        ProductAttribute::where(['ca_id' => $value, 'product_id' => $productId])->update($updateObj);
                                    }
                                }
                            }
                        }
                        if ($is_exist == true) {
                            if (isset($existAttributeArray) && !empty($existAttributeArray)) {
                                foreach ($existAttributeArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Auth::user()->id,
                                    ];
                                    ProductAttribute::where(['ca_id' => $value, 'product_id' => $productId])->update($updateObj);
                                }
                            }
                        }
                    } else {
                        if (isset($cat_attribute_id) && !empty($cat_attribute_id)) {
                            foreach ($cat_attribute_id as $catattkey => $catattvalue) {

                                foreach ($request->attribute_id_[$catattvalue] as $attkey => $attvalue) {
                                    $re = 'att_price_'.$attvalue;
                                    foreach ($request->$re[$catattvalue] as $inKey => $invalue) {
                                        $insertAttData = [
                                            'uuid' => Helper::getUuid(),
                                            'ca_id' => $catattvalue,
                                            'attribute_id' => $attvalue,
                                            'product_id' => $productId,
                                            'price' => $invalue,//$request->att_price_[$catattvalue][$attkey],
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id,
                                        ];
                                        ProductAttribute::create($insertAttData);
                                    }
                                }
                            }
                        }
                    }

                    /*insert modifier Data*/
                    $modifier_id = $request->modifier_id;
                    $productModifierData = ProductModifier::where('product_id', $productId)->get()->toArray();
                    if (isset($productModifierData) && !empty($productModifierData)) {
                        $existModifierArray = array();
                        foreach ($productModifierData as $key => $val) {
                            array_push($existModifierArray, $val['modifier_id']);
                        }
                        $is_exist = true;
                        if (isset($modifier_id) && !empty($modifier_id)) {
                            $newModifier = array_diff($modifier_id, $existModifierArray);
                            $oldModifierArray = array_diff($existModifierArray, $modifier_id);
                            $updateModifierArray = array_intersect($modifier_id, $existModifierArray);
                            $removeModifierArray = array_diff($oldModifierArray, $modifier_id);
                            if (empty($oldModifierArray) && empty($updateModifierArray) && empty($newModifier)) {
                                $is_exist = true;
                            } else {
                                $is_exist = false;
                                /*New insert*/
                                if (isset($newModifier) && !empty($newModifier)) {
                                    foreach ($newModifier as $key => $value) {
                                        if ($value) {
                                            $insertProModifier = [
                                                'uuid' => Helper::getUuid(),
                                                'product_id' => $productId,
                                                'modifier_id' => $value,
                                                'price' => $request->mod_price[$key],
                                                'status' => $request->is_enabled_mod[$key],
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => Auth::user()->id,
                                            ];
                                            ProductModifier::create($insertProModifier);
                                        }
                                    }
                                }
                                /*status update*/
                                if (isset($updateModifierArray) && !empty($updateModifierArray)) {
                                    foreach ($updateModifierArray as $key => $value) {
                                        $updateObj = [
                                            'price' => $request->mod_price[$key],
                                            'status' => $request->is_enabled_mod[$key],
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id,
                                        ];

                                        ProductModifier::where(['modifier_id' => $value, 'product_id' => $productId])->update($updateObj);
                                    }
                                }
                                if (isset($removeModifierArray) && !empty($removeModifierArray)) {
                                    foreach ($removeModifierArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 2,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id,
                                        ];

                                        ProductModifier::where(['modifier_id' => $value, 'product_id' => $productId])->update($updateObj);
                                    }
                                }
                            }
                        }
                        if ($is_exist == true) {
                            if (isset($existModifierArray) && !empty($existModifierArray)) {
                                foreach ($existModifierArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Auth::user()->id,
                                    ];
                                    ProductModifier::where(['modifier_id' => $value, 'product_id' => $productId])->update($updateObj);
                                }
                            }
                        }
                    } else {
                        if (isset($modifier_id) && !empty($modifier_id)) {
                            foreach ($modifier_id as $key => $value) {
                                $insertProModifier = [
                                    'uuid' => Helper::getUuid(),
                                    'product_id' => $productId,
                                    'modifier_id' => $value,
                                    'price' => $request->mod_price[$key],
                                    'status' => $request->is_enabled_mod[$key],
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id,
                                ];
                                ProductModifier::create($insertProModifier);
                            }
                        }
                    }


                    /* Assign Product Branch */
                    $branch_id = $request->branch_id;
                    $productBranchData = ProductBranch::where('product_id', $productId)->get()->toArray();
                    if (isset($productBranchData) && !empty($productBranchData)) {
                        $existBranchArray = array();
                        foreach ($productBranchData as $key => $val) {
                            array_push($existBranchArray, $val['branch_id']);
                        }
                        $is_exist = true;
                        if (isset($branch_id) && !empty($branch_id)) {
                            $newBranch = array_diff($branch_id, $existBranchArray);
                            $oldBranchArray = array_diff($existBranchArray, $branch_id);
                            $updateBranchArray = array_intersect($existBranchArray, $branch_id);
                            $removeBranchArray = array_diff($oldBranchArray, $branch_id);
                            if (empty($oldBranchArray) && empty($updateBranchArray) && empty($newBranch)) {
                                $is_exist = true;
                            } else {
                                $is_exist = false;
                                /*New insert*/
                                if (isset($newBranch) && !empty($newBranch)) {
                                    foreach ($newBranch as $key => $value) {
                                        if ($value) {
                                            $insertProBranch = [
                                                'uuid' => Helper::getUuid(),
                                                'product_id' => $productId,
                                                'branch_id' => $value,
                                                'warningStockLevel' => $request->warning_stock_level[$value],
                                                'display_order' => $request->display_order[$value],
                                                'printer_id' => $request->printer_id[$value],
                                                'status' => $request->is_enabled_status[$value],
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => Auth::user()->id,
                                            ];
                                            ProductBranch::create($insertProBranch);
                                        }
                                    }
                                }
                                /*status update*/
                                if (isset($updateBranchArray) && !empty($updateBranchArray)) {
                                    foreach ($updateBranchArray as $key => $value) {
                                        $updateObj = [
                                            'warningStockLevel' => $request->warning_stock_level[$value],
                                            'display_order' => $request->display_order[$value],
                                            'printer_id' => $request->printer_id[$value],
                                            'status' => $request->is_enabled_status[$value],
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id,
                                        ];

                                        ProductBranch::where(['branch_id' => $value, 'product_id' => $productId])->update($updateObj);
                                    }
                                }
                                if (isset($removeBranchArray) && !empty($removeBranchArray)) {
                                    foreach ($removeBranchArray as $key => $value) {
                                        $updateObj = [
                                            'status' => 2,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => Auth::user()->id,
                                        ];

                                        ProductBranch::where(['branch_id' => $value, 'product_id' => $productId])->update($updateObj);
                                    }
                                }
                            }
                        
							if ($is_exist == true) {
								if (isset($existBranchArray) && !empty($existBranchArray)) {
									foreach ($existBranchArray as $key => $value) {
										$updateObj = [
											'status' => 2,
											'updated_at' => date('Y-m-d H:i:s'),
											'updated_by' => Auth::user()->id,
										];
										ProductBranch::where(['branch_id' => $value, 'product_id' => $productId])->update($updateObj);
									}
								}
							}
						}
                    } else {
                        if (isset($branch_id) && !empty($branch_id)) {
                            foreach ($branch_id as $key => $value) {
                                $insertProBranch = [
                                    'uuid' => Helper::getUuid(),
                                    'product_id' => $productId,
                                    'branch_id' => $value,
                                    'warningStockLevel' => $request->warning_stock_level[$value],
                                    'display_order' => $request->display_order[$value],
                                    'printer_id' => $request->printer_id[$value],
                                    'status' => $request->is_enabled_status[$value],
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => Auth::user()->id,
                                ];
                                ProductBranch::create($insertProBranch);
                            }
                        }
                    }
                    Helper::saveLogAction('1', 'Product', 'Update', 'Update product ' . $productId, Auth::user()->id);
                    DB::commit();
                    Helper::log('Product update : finish');
                    return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
                }
            }


        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Product update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Product', 'Update Product exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_product');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $productData = Product::where('uuid', $uuid)->first();

        return view('backend.product.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Product delete : start');
        try {
            $productData = Product::where('uuid', $uuid)->first();
            if ($productData) {
                $productId = $productData->product_id;
                Product::where('uuid', $uuid)->update(['status'=>0,'deleted_by' => Auth::user()->id]);
                Product::where('uuid', $uuid)->delete();
                ProductCategory::where('product_id', $productId)->delete();
                ProductAttribute::where('product_id', $productId)->delete();
                ProductModifier::where('product_id', $productId)->delete();
                ProductBranch::where('product_id', $productId)->delete();
            }
            Helper::saveLogAction('1', 'Product', 'Destroy', 'Destroy Product' . $productId, Auth::user()->id);
            DB::commit();
            Helper::log('Product delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Product delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Product', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    /**
     * Remove the specified image from database using image id.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ImageDelete($id)
    {
        Languages::setBackLang();
        Helper::log('product image delete : start');
        try {
            $updateData = [
                'status' => 0,
                'updated_at' => config('constants.date_time'),
                'updated_by' => Auth::user()->id
            ];
            Assets::where('asset_id', $id)->update($updateData);
            DB::commit();
            Helper::log('product image delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('product image delete : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }

    public function getCategoryAttribute($ca_id)
    {
        Languages::setBackLang();
        Helper::log('product category attribute : start');
        try {

            $attributeList = Attributes::where('ca_id', $ca_id)->where('status', 1)
                ->get()->toArray();
            return response()->json(['status' => 200, 'data' => $attributeList]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('product category attribute : exception');
            Helper::log($exception);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }

    }

}
