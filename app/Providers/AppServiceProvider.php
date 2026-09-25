<?php

namespace App\Providers;

use App\Models\CalendarEvent;
use App\Models\Category;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use App\Observers\TaskObserver;
use App\Policies\CalendarEventPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\NotePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\SettingPolicy;
use App\Policies\TagPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use App\Repositories\CategoryRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\NoteRepositoryInterface;
use App\Repositories\Contracts\ProductivityLogRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\NoteRepository;
use App\Repositories\ProductivityLogRepository;
use App\Repositories\TagRepository;
use App\Repositories\TaskRepository;
use App\Services\AI\AIHistoryService;
use App\Services\AI\AIProviderFactory;
use App\Services\AI\AIService;
use App\Services\AI\PromptBuilder;
use App\Services\Setting\SettingService;
use App\View\Composers\NotificationComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerRepositoryBindings();
        $this->registerAIServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);

        $this->registerPolicies();

        View::composer(['layouts.app', 'components.sidebar'], NotificationComposer::class);
    }

    protected function registerRepositoryBindings(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(NoteRepositoryInterface::class, NoteRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(ProductivityLogRepositoryInterface::class, ProductivityLogRepository::class);
    }

    protected function registerAIServices(): void
    {
        $this->app->singleton(SettingService::class);
        $this->app->singleton(AIProviderFactory::class);
        $this->app->singleton(AIHistoryService::class);
        $this->app->singleton(PromptBuilder::class);
        $this->app->singleton(AIService::class);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Note::class, NotePolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);
        Gate::policy(CalendarEvent::class, CalendarEventPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Notification::class, NotificationPolicy::class);
    }
}
