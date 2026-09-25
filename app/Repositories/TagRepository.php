<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class TagRepository implements TagRepositoryInterface
{
    public function findForUser(int $userId, string $id): ?Tag
    {
        return Tag::query()->where('user_id', $userId)->find($id);
    }

    public function queryForUser(int $userId): Builder
    {
        return Tag::query()->where('user_id', $userId)->orderBy('name');
    }

    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): Tag
    {
        $tag->update($data);

        return $tag->refresh();
    }

    public function delete(Tag $tag): bool
    {
        return (bool) $tag->delete();
    }
}
