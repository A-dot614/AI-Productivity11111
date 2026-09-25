<?php

use App\Http\Controllers\Admin\AiConfigController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AIAssistantController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductivityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/gemini', [GeminiController::class, 'index'])->name('gemini.index');
Route::get('/gemini/generate', function () {
    return redirect()->route('gemini.index');
});
Route::post('/gemini/generate', [GeminiController::class, 'generate'])->name('gemini.generate');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/tasks/bulk', [TaskController::class, 'bulk'])->name('tasks.bulk');
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::patch('/tasks/{task}/reschedule', [TaskController::class, 'reschedule'])->name('tasks.reschedule');
    Route::patch('/tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::post('/tasks/{task}/subtasks', [TaskController::class, 'storeSubtask'])->name('tasks.subtasks.store');
    Route::patch('/tasks/{task}/subtasks/{subtask}', [TaskController::class, 'toggleSubtask'])->name('tasks.subtasks.toggle');
    Route::delete('/tasks/{task}/subtasks/{subtask}', [TaskController::class, 'destroySubtask'])->name('tasks.subtasks.destroy');

    Route::patch('/notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.pin');
    Route::resource('notes', NoteController::class);

    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('tags', TagController::class)->except(['show', 'create', 'edit']);

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/data', [CalendarController::class, 'data'])->name('calendar.data');
    Route::patch('/events/{event}/move', [CalendarEventController::class, 'move'])->name('events.move');
    Route::resource('events', CalendarEventController::class)->only(['store', 'update', 'destroy']);

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/analytics', [ProductivityController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [ProductivityController::class, 'data'])->name('analytics.data');

    Route::get('/ai', [AIAssistantController::class, 'index'])->name('ai.index');
    Route::post('/ai/natural-language', [AIAssistantController::class, 'naturalLanguage'])->name('ai.natural-language');
    Route::post('/ai/create-task', [AIAssistantController::class, 'createTask'])->name('ai.create-task');
    Route::post('/ai/prioritize', [AIAssistantController::class, 'prioritize'])->name('ai.prioritize');
    Route::post('/ai/daily-plan', [AIAssistantController::class, 'dailyPlan'])->name('ai.daily-plan');
    Route::post('/ai/weekly-plan', [AIAssistantController::class, 'weeklyPlan'])->name('ai.weekly-plan');
    Route::post('/ai/suggestions', [AIAssistantController::class, 'suggestions'])->name('ai.suggestions');
    Route::post('/tasks/{task}/ai/breakdown', [AIAssistantController::class, 'breakdown'])->name('ai.tasks.breakdown');
    Route::post('/tasks/{task}/ai/rewrite', [AIAssistantController::class, 'rewrite'])->name('ai.tasks.rewrite');
    Route::post('/tasks/{task}/ai/estimate', [AIAssistantController::class, 'estimate'])->name('ai.tasks.estimate');
    Route::post('/notes/{note}/ai/summarize', [AIAssistantController::class, 'summarize'])->name('ai.notes.summarize');
    Route::post('/notes/{note}/ai/to-tasks', [AIAssistantController::class, 'notesToTasks'])->name('ai.notes.to-tasks');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class)->only(['index', 'show', 'destroy']);
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
    Route::get('/ai-config', [AiConfigController::class, 'edit'])->name('ai-config.edit');
    Route::put('/ai-config', [AiConfigController::class, 'update'])->name('ai-config.update');
    Route::post('/ai-config/test', [AiConfigController::class, 'test'])->name('ai-config.test');
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
