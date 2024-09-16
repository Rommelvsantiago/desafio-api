<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $category = Category::create([
            'title' => $request->title,
            'color' => $request->input('color', '#01365B'),
            'user_id' => $user->id
        ]);

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
        ]);

        $category = Category::where('id', $id)->where('user_id', $user->id)->first();

        if (!$category) {
            return response()->json(['message' => 'not_found'], 404);
        }

        $category->title = $request->title;
        $category->color = $request->input('color', $category->color);
        $category->save();

        return response()->json($category);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $category = Category::where('id', $id)->where('user_id', $user->id)->first();

        if (!$category) {
            return response()->json(['message' => 'not_found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'deleted']);
    }
}
