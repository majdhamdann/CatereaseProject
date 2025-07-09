<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuManagementController extends Controller
{
public function indexForManager()
{
       $branch = Branch::where('manager_id', Auth::id())->first();

    if (!$branch) {
       return response()->json(['message' => 'لا يوجد فرع مرتبط بك كمدير.'], 404);
    }

    $categories = $branch->categories;
 
   return response()->json($categories);
}
public function allCategory()
{
       $category=Category::all();
        return response()->json($category);
}

}
