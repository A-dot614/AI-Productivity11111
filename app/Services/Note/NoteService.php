<?php

namespace App\Services\Note;

use App\Models\Note;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\NoteRepositoryInterface;

class NoteService
{
    public function __construct(
        protected readonly NoteRepositoryInterface $notes,
    ) {}

    public function createForUser(User $user, array $data): Note
    {
        return $this->notes->create([...$data, 'user_id' => $user->id]);
    }

    public function updateNote(Note $note, array $data): Note
    {
        return $this->notes->update($note, $data);
    }

    public function togglePin(Note $note): Note
    {
        return $this->notes->update($note, ['is_pinned' => ! $note->is_pinned]);
    }

    public function attachToTask(Note $note, ?Task $task): Note
    {
        return $this->notes->update($note, ['task_id' => $task?->id]);
    }

    public function deleteNote(Note $note): void
    {
        $this->notes->delete($note);
    }

    public function restoreNote(Note $note): void
    {
        $this->notes->restore($note);
    }
}
