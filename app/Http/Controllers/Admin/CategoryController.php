<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\CategoryBranch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_category');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $categoryList = Category::all();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $categoryList = Category::leftjoin('category_branch', 'category_branch.category_id', '=', 'category.category_id')
                ->whereIn('category_branch.branch_id', $branchIds)
                ->select('category.*')
                ->groupBy('category_id')
                ->get();
        }
        return view('backend.category.index', compact('categoryList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_category');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $parentCategoryList = Category::where(['parent_id' => 0, 'status' => Category::ACTIVE])->get()->toArray();
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get()->toArray();
        }
        return view('backend.category.create', compact('parentCategoryList', 'branchList'));

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
        DB::beginTransaction();
        Helper::log('Category store : start');
        try {
            $catName = $request->name;
            $loginId = Auth::user()->id;
            $checkExists = Category::where('name', $catName)->count();
            if ($checkExists > 0) {
                Helper::log('Category store : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/category.name_exists')]);
            } else {
                $parent_id = 0;
                if ($request->parent_id) {
                    $parent_id = $request->parent_id;
                }
                $is_for_web = 0;
                if ($request->is_for_web != '') {
                    $is_for_web = 1;
                }
                $has_rac_managemant = 0;
                if ($request->has_rac_managemant != '') {
                    $has_rac_managemant = 1;
                }
                $is_setmeal = 0;
                if ($request->is_setmeal != '') {
                    $is_setmeal = 1;
                }
                $categoryFolder = $this->createDirectory('category');
                $category_icon = '';
                if ($file = $request->file('category_icon')) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move("$categoryFolder/", $fileName);
                    chmod($categoryFolder . '/' . $fileName, 0777);
                    $category_icon = 'uploads/category/' . $fileName;
                }
                $insertCategory = [
                    'uuid' => Helper::getUuid(),
                    'name' => trim($catName),
                    'slug' => Helper::slugify(trim($catName)),
                    'category_icon' => $category_icon,
                    'parent_id' => $parent_id,
                    'is_for_web' => $is_for_web,
                    'has_rac_managemant' => $has_rac_managemant,
                    'is_setmeal' => $is_setmeal,
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];
                $categoryData = Category::create($insertCategory);
                $categoryId = $categoryData->category_id;
                $branch_id = $request->branch_id;
                if (isset($branch_id) && !empty($branch_id)) {
                    foreach ($branch_id as $key => $value) {
                        $insertCatBranch = [
                            'uuid' => Helper::getUuid(),
                            'category_id' => $categoryId,
                            'branch_id' => $value,
                            'display_order' => $request->display_order[$value],
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $loginId
                        ];
                        CategoryBranch::create($insertCatBranch);
                    }
                }
            }
            Helper::saveLogAction('1', 'Category', 'Store', 'Add new category ' . $categoryId, Auth::user()->id);
            DB::commit();
            Helper::log('Category store : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Category store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Category', 'Add new category exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_save_information')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('edit_category');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $categoryData = Category::where('uuid', $id)->first();
        if (empty($categoryData)) {
            Helper::log('Category edit : No record found');
            return redirect()->back()->with('error', trans('backend/common.oops'));
        }
        $parentCategoryList = Category::where(['parent_id' => 0, 'status' => Category::ACTIVE])->get()->toArray();

        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get()->toArray();
        }

        $catId = $categoryData->category_id;
        /*get selected branch */
        $catBranchData = CategoryBranch::where('category_id', $catId)->select('branch_id', 'display_order', 'status')->get();
        $categoryData->branchData = $catBranchData;

        return view('backend.category.edit', compact('categoryData', 'parentCategoryList', 'branchList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Category update : start');
        try {
            $loginId = Auth::user()->id;
            $catName = $request->name;
            $catId = $request->category_id;
            $is_for_web = 0;
            if ($request->is_for_web) {
                $is_for_web = 1;
            }
            $has_rac_managemant = 0;
            if ($request->has_rac_managemant) {
                $has_rac_managemant = 1;
            }
            $is_setmeal = 0;
            if ($request->is_setmeal != '') {
                $is_setmeal = 1;
            }
            $parent_id = 0;
            if ($request->parent_id) {
                $parent_id = $request->parent_id;
            }
            $checkExists = Category::where('name', $catName)->where('category_id', '!=', $catId)->count();
            if ($checkExists > 0) {
                Helper::log('Category update : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/common.name_exists')]);
            } else {
                $updateData = [
                    'name' => trim($catName),
                    'slug' => Helper::slugify(trim($catName)),
                    'parent_id' => $parent_id,
                    'is_for_web' => $is_for_web,
                    'has_rac_managemant' => $has_rac_managemant,
                    'is_setmeal' => $is_setmeal,
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $loginId,
                ];
                $categoryFolder = $this->createDirectory('category');
                if ($file = $request->file('category_icon')) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move($categoryFolder, $fileName);
                    $myIcon = $categoryFolder . $fileName;
                    chmod($myIcon, 0777);
                    $updateData['category_icon'] = "uploads/category/$fileName";
                }
                Category::where('uuid', $id)->update($updateData);
                $branch_id = $request->branch_id;
                $catId = $request->category_id;
                $display_order = $request->display_order;

                $categoryBranchData = CategoryBranch::where('category_id', $catId)->get()->toArray();
                if (isset($categoryBranchData) && !empty($categoryBranchData)) {
                    $existBranchArray = array();
                    foreach ($categoryBranchData as $key => $val) {
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
                                    $insertCatBranch = [
                                        'uuid' => Helper::getUuid(),
                                        'category_id' => $catId,
                                        'branch_id' => $value,
                                        'display_order' => $request->display_order[$value],
                                        'status' => 1,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];
                                    $this->insertBranch($insertCatBranch);
                                }
                            }
                            /*status update*/
                            if (isset($updateBranchArray) && !empty($updateBranchArray)) {
                                foreach ($updateBranchArray as $key => $value) {
                                    $updateObj = [
                                        'display_order' => $request->display_order[$value],
                                        'status' => 1,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];

                                    CategoryBranch::where(['branch_id' => $value, 'category_id' => $catId])->update($updateObj);
                                }
                            }
                            if (isset($removeBranchArray) && !empty($removeBranchArray)) {
                                foreach ($removeBranchArray as $key => $value) {
                                    $updateObj = [
                                        'status' => 2,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $loginId
                                    ];

                                    CategoryBranch::where(['branch_id' => $value, 'category_id' => $catId])->update($updateObj);
                                }
                            }
                        }
                    }

                    if ($is_exist == true) {
                        if (isset($existBranchArray) && !empty($existBranchArray)) {
                            foreach ($existBranchArray as $key => $value) {
                                $updateObj = [
                                    'status' => 2,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => $loginId
                                ];
                                CategoryBranch::where(['branch_id' => $value, 'category_id' => $catId])->update($updateObj);
                            }
                        }
                    }
                } else {
                    if (isset($branch_id) && !empty($branch_id)) {
                        foreach ($branch_id as $key => $value) {
                            $insertCatBranch = [
                                'uuid' => Helper::getUuid(),
                                'category_id' => $catId,
                                'branch_id' => $value,
                                'display_order' => $request->display_order[$value],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $loginId
                            ];
                            $this->insertBranch($insertCatBranch);
                        }
                    }
                }
                Helper::saveLogAction('1', 'Category', 'Update', 'Update category ' . $catId, Auth::user()->id);
                DB::commit();
                Helper::log('Category update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch
        (\Exception $exception) {
            DB::rollBack();
            Helper::log('Category update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Category', 'Update category exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    public function insertBranch($object)
    {
        CategoryBranch::create($object);
    }

    public function updateBranch($object)
    {
        CategoryBranch::create($object);
    }

    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_category');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.category.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Languages::setBackLang();

        DB::beginTransaction();
        Helper::log('category delete : start');
        try {
            $categoryData = Category::where('uuid', $id)->first();
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Category::where('uuid', $id)->update($deleteData);
            DB::commit();
            Helper::saveLogAction('1', 'Category', 'Destroy', 'Destroy category' . $id, Auth::user()->id);
            Helper::log('category delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('category delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Category', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
