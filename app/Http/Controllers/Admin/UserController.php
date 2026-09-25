<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount(['tasks', 'notes']);

        if (filled($request->query('q'))) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->query('q').'%')
                    ->orWhere('email', 'like', '%'.$request->query('q').'%');
            });
        }

        if (in_array($request->query('role'), [User::ROLE_ADMIN, User::ROLE_USER], true)) {
            $query->where('role', $request->query('role'));
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
            'search' => $request->query('q', ''),
            'roleFilter' => $request->query('role', ''),
        ]);
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user->loadCount(['tasks', 'notes', 'calendarEvents', 'aiHistories']),
            'recentTasks' => $user->tasks()->latest()->limit(8)->get(),
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $user->update(['role' => $request->validated('role')]);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Role updated',
            'message' => $user->name.' is now a '.$user->role.'.',
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('toast', ['type' => 'success', 'title' => 'User deleted', 'message' => $user->name.' was removed.']);
    }
}
