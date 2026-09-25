<?php

namespace App\Repositories;

use App\Models\Note;
use App\Repositories\Contracts\NoteRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class NoteRepository implements NoteRepositoryInterface
{
    public function findForUser(int $userId, string $id): ?Note
    {
        return Note::query()->where('user_id', $userId)->find($id);
    }

    public function queryForUser(int $userId): Builder
    {
        return Note::query()->where('user_id', $userId)->pinnedFirst();
    }

    public function paginate(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Note
    {
        return Note::create($data);
    }

    public function update(Note $note, array $data): Note
    {
        $note->update($data);

        return $note->refresh();
    }

    public function delete(Note $note): bool
    {
        return (bool) $note->delete();
    }

    public function restore(Note $note): bool
    {
        return $note->restore();
    }
}
