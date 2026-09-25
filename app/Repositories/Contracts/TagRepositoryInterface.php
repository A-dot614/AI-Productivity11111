<?php

namespace App\Repositories\Contracts;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

interface TagRepositoryInterface
{
    public function findForUser(int $userId, string $id): ?Tag;

    public function queryForUser(int $userId): Builder;

    public function create(array $data): Tag;

    public function update(Tag $tag, array $data): Tag;

    public function delete(Tag $tag): bool;
}
