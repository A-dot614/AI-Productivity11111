<?php

namespace App\Http\Controllers;

use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Models\Note;
use App\Repositories\Contracts\NoteRepositoryInterface;
use App\Services\Note\NoteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoteController extends Controller
{
    public function __construct(
        protected readonly NoteRepositoryInterface $notes,
        protected readonly NoteService $service,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Note::class);

        $query = $this->notes->queryForUser(auth()->id());

        if (filled($request->query('q'))) {
            $query->search(trim($request->query('q')));
        }

        $notes = $this->notes->paginate($query, 12);

        return view('notes.index', [
            'notes' => $notes,
            'search' => $request->query('q', ''),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Note::class);

        return view('notes.create', ['note' => new Note]);
    }

    public function store(StoreNoteRequest $request): RedirectResponse
    {
        $note = $this->service->createForUser($request->user(), $request->validated());

        return redirect()
            ->route('notes.show', $note)
            ->with('toast', ['type' => 'success', 'title' => 'Note created', 'message' => 'Your note has been saved.']);
    }

    public function show(Note $note): View
    {
        $this->authorize('view', $note);

        return view('notes.show', ['note' => $note]);
    }

    public function edit(Note $note): View
    {
        $this->authorize('update', $note);

        return view('notes.edit', ['note' => $note]);
    }

    public function update(UpdateNoteRequest $request, Note $note): RedirectResponse
    {
        $this->service->updateNote($note, $request->validated());

        return redirect()
            ->route('notes.show', $note)
            ->with('toast', ['type' => 'success', 'title' => 'Note updated', 'message' => 'Changes have been saved.']);
    }

    public function togglePin(Note $note): RedirectResponse
    {
        $this->authorize('update', $note);

        $this->service->togglePin($note);

        return back()->with('toast', ['type' => 'success', 'title' => 'Note '.($note->is_pinned ? 'pinned' : 'unpinned'), 'message' => 'Updated your note.']);
    }

    public function destroy(Note $note): RedirectResponse
    {
        $this->authorize('delete', $note);

        $this->service->deleteNote($note);

        return redirect()
            ->route('notes.index')
            ->with('toast', ['type' => 'success', 'title' => 'Note deleted', 'message' => 'The note has been moved to the trash.']);
    }
}
