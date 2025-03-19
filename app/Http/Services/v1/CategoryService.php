<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function getAllCategories()
    {
        return CategoryResource::collection(Category::all());
    }

    public function getCategoryById($id)
    {
        return new CategoryResource(Category::findOrFail($id));
    }

    public function createCategory($data)
    {
        $slug = Str::slug($data['name']);
        $category  = Category::where('slug', $slug)->exists();
        if ($category) {
            return false;
        }
        $data['slug'] = $slug;
        return Category::create($data) ? true : false;
    }

    public function updateCategory($id, $data)
    {
        $category = Category::find($id);
        if (!$category) return false;

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $category->update($data);
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);
        return $category ? $category->delete() : false;
    }
}
