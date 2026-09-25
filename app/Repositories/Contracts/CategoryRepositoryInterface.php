<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

interface CategoryRepositoryInterface
{
    public function findForUser(int $userId, string $id): ?Category;

    public function queryForUser(int $userId): Builder;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): bool;
}
