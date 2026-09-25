<?php

namespace App\Repositories\Contracts;

use App\Models\Note;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface NoteRepositoryInterface
{
    public function findForUser(int $userId, string $id): ?Note;

    public function queryForUser(int $userId): Builder;

    public function paginate(Builder $query, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Note;

    public function update(Note $note, array $data): Note;

    public function delete(Note $note): bool;

    public function restore(Note $note): bool;
}
