<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categories,
    ) {}

    public function index(): View
    {
        $categories = $this->categories->queryForUser(auth()->id())->withCount('tasks')->get();

        return view('categories.index', ['categories' => $categories]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categories->create([
            'user_id' => $request->user()->id,
            'name' => $request->validated('name'),
            'color' => $request->validated('color') ?? '#6366f1',
            'icon' => $request->validated('icon'),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('toast', ['type' => 'success', 'title' => 'Category created', 'message' => 'Your category has been added.']);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categories->update($category, $request->validated());

        return redirect()
            ->route('categories.index')
            ->with('toast', ['type' => 'success', 'title' => 'Category updated', 'message' => 'Changes have been saved.']);
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $this->categories->delete($category);

        return redirect()
            ->route('categories.index')
            ->with('toast', ['type' => 'success', 'title' => 'Category deleted', 'message' => 'The category was removed.']);
    }
}
