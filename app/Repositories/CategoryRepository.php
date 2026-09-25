<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\User;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function findForUser(int $userId, string $id): ?Category
    {
        return Category::query()->visibleTo(User::find($userId))->find($id);
    }

    public function queryForUser(int $userId): Builder
    {
        return Category::query()->visibleTo(User::find($userId))->orderBy('name');
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->refresh();
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
