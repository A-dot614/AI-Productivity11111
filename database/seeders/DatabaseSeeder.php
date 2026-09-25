<?php

namespace Database\Seeders;

use App\Models\AIHistory;
use App\Models\CalendarEvent;
use App\Models\Category;
use App\Models\Note;
use App\Models\ProductivityLog;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedGlobalCategories();
        $this->seedAdmin();
        $this->seedDemoUser();
    }

    protected function seedSettings(): void
    {
        $settings = [
            // AI group
            'ai_provider' => [config('ai.default_provider', 'openai'), Setting::GROUP_AI],
            'ai_model' => [null, Setting::GROUP_AI],
            'ai_base_url' => [null, Setting::GROUP_AI],
            'ai_max_tokens' => [1500, Setting::GROUP_AI],
            'ai_timeout' => [60, Setting::GROUP_AI],
            'ai_cache_enabled' => [(bool) config('ai.cache.enabled'), Setting::GROUP_AI],
            // Notification group
            'reminder_lead_minutes' => [30, Setting::GROUP_NOTIFICATION],
            'reminder_channel' => ['both', Setting::GROUP_NOTIFICATION],
            'default_task_view' => ['list', Setting::GROUP_NOTIFICATION],
            'daily_digest_enabled' => [false, Setting::GROUP_NOTIFICATION],
            'daily_digest_time' => ['07:00', Setting::GROUP_NOTIFICATION],
        ];

        foreach ($settings as $key => [$value, $group]) {
            Setting::set($key, $value, $group);
        }
    }

    protected function seedGlobalCategories(): void
    {
        $categories = [
            ['name' => 'Study', 'color' => '#6366f1', 'icon' => '📚'],
            ['name' => 'Work', 'color' => '#f59e0b', 'icon' => '💼'],
            ['name' => 'Personal', 'color' => '#10b981', 'icon' => '🏡'],
            ['name' => 'Health', 'color' => '#ef4444', 'icon' => '💪'],
            ['name' => 'Finance', 'color' => '#06b6d4', 'icon' => '💰'],
        ];

        foreach ($categories as $category) {
            Category::create([...$category, 'user_id' => null, 'is_global' => true]);
        }
    }

    protected function seedAdmin(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'timezone' => 'UTC',
            'theme' => 'light',
            'productivity_goal_minutes' => 360,
        ]);
    }

    protected function seedDemoUser(): void
    {
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'user',
            'timezone' => 'UTC',
            'theme' => 'light',
            'productivity_goal_minutes' => 240,
        ]);

        $study = Category::where('is_global', true)->where('name', 'Study')->first();
        $work = Category::where('is_global', true)->where('name', 'Work')->first();
        $personal = Category::where('is_global', true)->where('name', 'Personal')->first();

        $thesis = Tag::create(['user_id' => $user->id, 'name' => 'thesis', 'color' => '#6366f1']);
        $meeting = Tag::create(['user_id' => $user->id, 'name' => 'meeting', 'color' => '#f59e0b']);
        $idea = Tag::create(['user_id' => $user->id, 'name' => 'idea', 'color' => '#10b981']);
        $urgent = Tag::create(['user_id' => $user->id, 'name' => 'urgent', 'color' => '#ef4444']);

        $this->seedProductivityLogs($user);
        $this->seedTasks($user, $study, $work, $personal, [$thesis, $meeting, $idea, $urgent]);
        $this->seedNotes($user, $study);
        $this->seedEvents($user);
        $this->seedAIHistory($user);
    }

    protected function seedTasks(User $user, ?Category $study, ?Category $work, ?Category $personal, array $tags): void
    {
        [$thesis, $meeting, $idea, $urgent] = $tags;

        $task = Task::create([
            'user_id' => $user->id,
            'category_id' => $study?->id,
            'title' => 'Write database design chapter',
            'description' => "## Outline\n- ER diagram\n- Normalization rationale\n- Migration strategy",
            'status' => Task::STATUS_IN_PROGRESS,
            'priority' => Task::PRIORITY_HIGH,
            'due_date' => CarbonImmutable::today()->addHours(18),
            'estimated_minutes' => 120,
            'recurrence' => Task::RECURRENCE_NONE,
        ]);
        $task->tags()->attach([$thesis->id, $urgent->id]);

        foreach (['Design the ER diagram', 'Write normalization section', 'Review with supervisor'] as $title) {
            $task->subtasks()->create(['title' => $title, 'sort_order' => $task->subtasks()->max('sort_order') + 1]);
        }
        $task->subtasks()->first()->update(['is_completed' => true, 'completed_at' => now()->subHours(3)]);

        $task->calendarEvent()->create([
            'user_id' => $user->id,
            'title' => 'Timebox: Database chapter',
            'start_at' => CarbonImmutable::today()->addHours(9),
            'end_at' => CarbonImmutable::today()->addHours(11),
            'color' => '#6366f1',
        ]);

        $overdue = Task::create([
            'user_id' => $user->id,
            'category_id' => $work?->id,
            'title' => 'Submit expense report',
            'description' => null,
            'status' => Task::STATUS_PENDING,
            'priority' => Task::PRIORITY_MEDIUM,
            'due_date' => CarbonImmutable::yesterday()->subHours(2),
            'estimated_minutes' => 30,
            'recurrence' => Task::RECURRENCE_NONE,
        ]);
        $overdue->tags()->attach($meeting->id);

        Task::create([
            'user_id' => $user->id,
            'category_id' => $personal?->id,
            'title' => 'Grocery shopping',
            'description' => 'Milk, eggs, bread, coffee beans.',
            'status' => Task::STATUS_PENDING,
            'priority' => Task::PRIORITY_LOW,
            'due_date' => CarbonImmutable::today()->addHours(6),
            'estimated_minutes' => 45,
            'recurrence' => Task::RECURRENCE_WEEKLY,
        ]);

        Task::create([
            'user_id' => $user->id,
            'category_id' => $study?->id,
            'title' => 'Review machine learning lecture notes',
            'description' => null,
            'status' => Task::STATUS_PENDING,
            'priority' => Task::PRIORITY_MEDIUM,
            'due_date' => CarbonImmutable::today()->addDays(2)->addHours(12),
            'estimated_minutes' => 60,
            'recurrence' => Task::RECURRENCE_NONE,
        ]);
        $task->tags()->attach($idea->id);

        Task::create([
            'user_id' => $user->id,
            'category_id' => $work?->id,
            'title' => 'Prepare project defense slides',
            'description' => null,
            'status' => Task::STATUS_PENDING,
            'priority' => Task::PRIORITY_URGENT,
            'due_date' => CarbonImmutable::today()->addDays(5)->addHours(9),
            'estimated_minutes' => 180,
            'recurrence' => Task::RECURRENCE_NONE,
        ])->tags()->attach($thesis->id);

        // A few completed tasks for analytics
        $completedData = [
            ['Finish ERD draft', $study, Task::PRIORITY_HIGH, 6],
            ['Peer review session', $work, Task::PRIORITY_MEDIUM, 2],
            ['Pay electricity bill', $personal, Task::PRIORITY_HIGH, 3],
            ['Read chapter 4', $study, Task::PRIORITY_LOW, 5],
            ['Update CV', $personal, Task::PRIORITY_MEDIUM, 4],
            ['Research API providers', $study, Task::PRIORITY_MEDIUM, 1],
        ];

        foreach ($completedData as [$title, $category, $priority, $daysAgo]) {
            $completed = CarbonImmutable::today()->subDays($daysAgo)->setHour(15);
            $completedTask = Task::create([
                'user_id' => $user->id,
                'category_id' => $category?->id,
                'title' => $title,
                'status' => Task::STATUS_COMPLETED,
                'priority' => $priority,
                'due_date' => $completed->addHour(),
                'estimated_minutes' => fake()->numberBetween(30, 120),
                'actual_minutes' => fake()->numberBetween(25, 140),
                'completed_at' => $completed,
            ]);

            $log = ProductivityLog::query()
                ->where('user_id', $user->id)
                ->whereDate('log_date', $completed->toDateString())
                ->first();

            if (! $log) {
                $log = ProductivityLog::query()->create([
                    'user_id' => $user->id,
                    'log_date' => $completed->toDateString(),
                    'focus_minutes' => 0,
                    'completed_tasks' => 0,
                    'planned_tasks' => 0,
                ]);
            }

            $log->increment('completed_tasks');
        }
    }

    protected function seedNotes(User $user, ?Category $study): void
    {
        Note::create([
            'user_id' => $user->id,
            'title' => 'Supervisor feedback — defense prep',
            'content' => "# Feedback\n- Focus on the **impact** section first\n- Prepare 3 backup demo scenarios\n\n## Action items\n- [ ] Rehearse the 10 minute walkthrough\n- [ ] Update slide 12 with new chart",
            'is_pinned' => true,
        ]);

        Note::create([
            'user_id' => $user->id,
            'title' => 'Reading notes: task prioritization',
            'content' => '## Key ideas\n\n- Eisenhower matrix: urgent vs important\n- Time-blocking improves deep work\n\n> "What gets scheduled gets done."',
        ]);

        Note::create([
            'user_id' => $user->id,
            'title' => 'Weekly review template',
            'content' => "## Wins\n- \n\n## Struggles\n- \n\n## Next week focus\n- ",
        ]);
    }

    protected function seedEvents(User $user): void
    {
        $events = [
            ['title' => 'Dentist appointment', 'daysFrom' => 1, 'hour' => 10, 'color' => '#ef4444'],
            ['title' => 'Team stand-up', 'daysFrom' => 2, 'hour' => 9, 'color' => '#f59e0b'],
            ['title' => 'Library study block', 'daysFrom' => 3, 'hour' => 14, 'color' => '#6366f1'],
            ['title' => 'Gym session', 'daysFrom' => 4, 'hour' => 18, 'color' => '#10b981'],
        ];

        foreach ($events as $event) {
            $start = CarbonImmutable::today()->addDays($event['daysFrom'])->setHour($event['hour']);
            CalendarEvent::create([
                'user_id' => $user->id,
                'title' => $event['title'],
                'start_at' => $start,
                'end_at' => $start->addMinutes(60),
                'color' => $event['color'],
            ]);
        }
    }

    protected function seedProductivityLogs(User $user): void
    {
        foreach (range(0, 29) as $offset) {
            $date = CarbonImmutable::today()->subDays($offset);
            $planned = fake()->numberBetween(2, 8);
            $completed = fake()->numberBetween(0, $planned);

            ProductivityLog::create([
                'user_id' => $user->id,
                'log_date' => $date->toDateString(),
                'focus_minutes' => fake()->numberBetween(30, 300),
                'completed_tasks' => $completed,
                'planned_tasks' => $planned,
            ]);
        }
    }

    protected function seedAIHistory(User $user): void
    {
        $features = [
            'natural_language_task' => 'Plan a study session for tomorrow afternoon',
            'task_breakdown' => 'Break down "write final report" into steps',
            'daily_planning' => 'Plan my day around 3 priorities',
            'productivity_suggestions' => 'How can I be more productive this week?',
        ];

        foreach ($features as $feature => $prompt) {
            AIHistory::create([
                'user_id' => $user->id,
                'feature' => $feature,
                'prompt' => $prompt,
                'response' => 'Scheduled "study session" for tomorrow at 14:00 with high priority.',
                'status' => AIHistory::STATUS_SUCCESS,
                'provider' => 'openai',
                'model' => 'gpt-4o-mini',
                'duration_ms' => 1200,
            ]);
        }
    }
}
