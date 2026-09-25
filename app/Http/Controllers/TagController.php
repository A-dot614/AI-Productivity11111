<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    public function __construct(
        protected readonly TagRepositoryInterface $tags,
    ) {}

    public function index(): View
    {
        $tags = $this->tags->queryForUser(auth()->id())->withCount('tasks')->get();

        return view('tags.index', ['tags' => $tags]);
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        $this->tags->create([
            'user_id' => $request->user()->id,
            'name' => $request->validated('name'),
            'color' => $request->validated('color') ?? '#10b981',
        ]);

        return redirect()
            ->route('tags.index')
            ->with('toast', ['type' => 'success', 'title' => 'Tag created', 'message' => 'Your tag has been added.']);
    }

    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $this->tags->update($tag, $request->validated());

        return redirect()
            ->route('tags.index')
            ->with('toast', ['type' => 'success', 'title' => 'Tag updated', 'message' => 'Changes have been saved.']);
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $this->authorize('delete', $tag);

        $this->tags->delete($tag);

        return redirect()
            ->route('tags.index')
            ->with('toast', ['type' => 'success', 'title' => 'Tag deleted', 'message' => 'The tag was removed.']);
    }
}
