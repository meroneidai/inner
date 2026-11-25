<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->withCount('dreams')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        $user->update(['is_active' => ! $user->is_active]);

        AuditLog::query()->create([
            'actor_id' => $request->user()->id,
            'action' => $user->is_active ? 'user_activated' : 'user_deactivated',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('status', __('تم تحديث حالة المستخدم.'));
    }
}
