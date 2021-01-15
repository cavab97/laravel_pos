<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\CategoryBranch;

class Category extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "category";
    protected $primaryKey = "category_id";
    protected $guarded = ['category_id'];
    protected $dates = ['deleted_at'];
    static $selectedId = '';
    static $selectedCatId = '';
    const ACTIVE = 1;
    const DEACTIVE = 0;

    public function childs()
    {
        return $this->hasMany(Category::class, 'parent_id', 'category_id');
    }

    static function getCategoryTreeIDsLibrary($catId)
    {
        $result = self::where('category_id', $catId)
            ->select('category_id', 'parent_id', 'name')
            ->first();

        $path = array();
        if (isset($result) && !empty($result)) {

            if ($result['parent_id'] != '' || $result['parent_id'] != 0) {
                $path[] = array('name' => $result['name'], 'category_id' => $result['category_id'], 'parent_id' => $result['parent_id']);
                $path = array_merge(self::getCategoryTreeIDsLibrary($result['parent_id']), $path);
            } else {
                $path[] = array('name' => $result['name'], 'category_id' => $result['category_id'], 'parent_id' => $result['parent_id']);
            }

        }
        return $path;
    }

    static function getListForSelectBox1($selectedId = null, $categoty_id = null, $has_rac = 0)
    {
        self::$selectedId = $selectedId;
        self::$selectedCatId = $categoty_id;

        return self::categoryRepeat1(0, 0, '', $has_rac);
    }

    static function categoryRepeat1($pId, $level, $parentName, $has_rac)
    {
        $userData = Auth::user();
        $categoryData = self::select('category_id', 'parent_id', 'name')
            ->where('has_rac_managemant', $has_rac);
        if ($userData->role != 1) {
            $branchIds = UserBranch::where('user_id', $userData->id)->where('status',1)->pluck("branch_id")->toArray();
            $categoryIds = CategoryBranch::whereIn('branch_id', $branchIds)->pluck('category_id');
            $categoryData = $categoryData->whereIn('category_id', $categoryIds);
        }
        $categoryData = $categoryData->get()->toArray();
        foreach ($categoryData as $key => $value) {
            $categoryId = $value['category_id'];
            $name = $value['name'];
            $categoryData[$key]['name'] = $name;
        }

        $categoryId = self::$selectedCatId;
        $option = '';
        foreach ($categoryData as $key => $value) {
            $catId = explode(',', $categoryId);
            $parentId = $value['category_id'];
            if (in_array($parentId, $catId) && !empty($catId)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }

            $parentName = '';
            $categoryBreadcrubs = self::getCategoryTreeIDsLibrary1($value['category_id'], $has_rac);
            for ($i = 0; $i < count($categoryBreadcrubs); $i++) {
                if ($categoryBreadcrubs[$i]['category_id'] != $value['category_id']) {
                    $parentName .= $categoryBreadcrubs[$i]['name'] . " > ";
                } else {
                    $parentName .= $categoryBreadcrubs[$i]['name'];
                }
            }

            $catName = $value['name'];
            if ($parentName) {
                $catName = $parentName;
            } else {
                $parentName = $catName;
            }
            $option .= '<option value="' . $parentId . '" ' . $selected . '>' . $catName . '</option>';
        }
        return $option;
    }

    static function getCategoryTreeIDsLibrary1($catId, $has_rac)
    {
        $result = self::where('category_id', $catId)
            ->where('has_rac_managemant', $has_rac)
            ->select('category_id', 'parent_id', 'name')
            ->first();

        $path = array();
        if (isset($result) && !empty($result)) {

            if ($result['parent_id'] != '' || $result['parent_id'] != 0) {
                $path[] = array('name' => $result['name'], 'category_id' => $result['category_id'], 'parent_id' => $result['parent_id']);
                $path = array_merge(self::getCategoryTreeIDsLibrary($result['parent_id']), $path);
            } else {
                $path[] = array('name' => $result['name'], 'category_id' => $result['category_id'], 'parent_id' => $result['parent_id']);
            }

        }
        return $path;
    }

    static function getCategoryByBranch($branchId)
    {
        $data = self::join('category_branch', 'category_branch.category_id', 'category.category_id')
            ->where('category.status', 1)
            ->where('category.is_for_web', 1)
            ->select('category.*')
            ->where('category_branch.branch_id', $branchId)
            ->get()->toArray();
        return $data;
    }
}
