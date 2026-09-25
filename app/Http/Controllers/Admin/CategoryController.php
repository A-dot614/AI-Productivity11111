<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('tasks')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.categories', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/', 'max:9'],
            'icon' => ['nullable', 'string', 'max:32'],
        ]);

        Category::create([
            ...$data,
            'user_id' => null,
            'is_global' => true,
        ]);

        return back()->with('toast', ['type' => 'success', 'title' => 'Category created', 'message' => 'The global category is now available to all users.']);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/', 'max:9'],
            'icon' => ['nullable', 'string', 'max:32'],
        ]);

        $category->update($data);

        return back()->with('toast', ['type' => 'success', 'title' => 'Category updated', 'message' => 'The global category has been updated.']);
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('toast', ['type' => 'success', 'title' => 'Category deleted', 'message' => 'The global category was removed.']);
    }
}
