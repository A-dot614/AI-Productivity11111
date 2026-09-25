<?php

namespace App\Http\Controllers;

use App\Http\Requests\AI\NaturalLanguageTaskRequest;
use App\Models\Note;
use App\Models\Task;
use App\Services\AI\AIAssistantService;
use App\Services\AI\Exceptions\AIException;
use App\Services\Note\NoteService;
use App\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AIAssistantController extends Controller
{
    public function __construct(
        protected readonly AIAssistantService $assistant,
        protected readonly TaskService $tasks,
        protected readonly NoteService $notes,
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        $today = $user->tasks()->active()->whereDate('due_date', today())->orderBy('due_date')->get();
        $upcoming = $user->tasks()->active()->whereDate('due_date', '>=', today())->orderBy('due_date')->limit(12)->get();

        return view('ai.index', [
            'todayTasks' => $today,
            'upcomingTasks' => $upcoming,
            'recentHistory' => $user->aiHistories()->latest()->limit(8)->get(),
        ]);
    }

    public function naturalLanguage(NaturalLanguageTaskRequest $request): JsonResponse
    {
        try {
            $suggestion = $this->assistant->suggestTask($request->user(), $request->validated('request'));

            return response()->json(['success' => true, 'data' => $suggestion]);
        } catch (AIException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function createTask(NaturalLanguageTaskRequest $request): RedirectResponse
    {
        try {
            $task = $this->assistant->createTaskFromRequest($request->user(), $request->validated('request'));

            return redirect()
                ->route('tasks.show', $task)
                ->with('toast', ['type' => 'success', 'title' => 'Task created by AI', 'message' => '"'.$task->title.'" was parsed and created.']);
        } catch (AIException $e) {
            return back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function breakdown(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        try {
            $steps = $this->assistant->breakdownTask($task);

            $titles = array_column($steps, 'title');
            $created = $this->tasks->syncSubtasks($task, $titles);

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Task broken down',
                'message' => count($created).' subtasks were generated.',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function rewrite(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        try {
            $rewritten = $this->assistant->rewriteTask($task);

            $this->tasks->updateTask($task, $rewritten);

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Task rewritten',
                'message' => 'AI rewrote the task to be clearer and more actionable.',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function estimate(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        try {
            $estimate = $this->assistant->estimateDuration($task);

            $task->update(['estimated_minutes' => $estimate['estimated_minutes']]);

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Duration estimated',
                'message' => 'Estimated at '.$estimate['estimated_minutes'].' minutes (confidence: '.$estimate['confidence'].').',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function prioritize(Request $request): RedirectResponse
    {
        try {
            $suggestions = $this->assistant->prioritizeTasks($request->user());

            $request->session()->flash('ai_prioritization', collect($suggestions)->map(fn ($s) => [
                'title' => $s['task']->title,
                'priority' => $s['priority'],
                'reason' => $s['reason'],
            ]));

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Tasks prioritized',
                'message' => count($suggestions).' tasks were re-prioritized by AI.',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function dailyPlan(Request $request): RedirectResponse
    {
        try {
            $result = $this->assistant->dailyPlan($request->user());

            $request->session()->flash('ai_daily_plan', [
                'plan' => $result['plan'],
                'note' => $result['note'],
            ]);

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Daily plan generated',
                'message' => 'Your tasks have been scheduled for today.',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function weeklyPlan(Request $request): RedirectResponse
    {
        try {
            $result = $this->assistant->weeklyPlan($request->user());

            $request->session()->flash('ai_weekly_plan', $result);

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Weekly plan generated',
                'message' => 'Your week has been scheduled by AI.',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function suggestions(Request $request): RedirectResponse
    {
        try {
            $suggestions = $this->assistant->productivitySuggestions($request->user());

            $request->session()->flash('ai_suggestions', $suggestions);

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Recommendations ready',
                'message' => count($suggestions).' personalized suggestions generated.',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function summarize(Request $request, Note $note): RedirectResponse
    {
        $this->authorize('view', $note);

        try {
            $summary = $this->assistant->summarizeNote($note);

            $request->session()->flash('ai_summary', $summary);

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Note summarized',
                'message' => 'Your summary is ready to view.',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }

    public function notesToTasks(Request $request, Note $note): RedirectResponse
    {
        $this->authorize('view', $note);

        try {
            $created = $this->assistant->notesToTasks($request->user(), $note);

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Tasks created from note',
                'message' => count($created).' tasks were created from this note.',
            ]);
        } catch (AIException $e) {
            return back()->with('toast', ['type' => 'error', 'title' => 'AI unavailable', 'message' => $e->getMessage()]);
        }
    }
}
