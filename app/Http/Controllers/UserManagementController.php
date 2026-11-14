<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Admin-only user management (plan §6 Module 7). Routes are in the role:admin group.
 * Users are never hard-deleted (they own products and audit history); they are deactivated instead.
 */
class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'in:'.implode(',', User::ROLES)],
            'status' => ['nullable', 'string', 'in:'.implode(',', User::STATUSES)],
        ]);

        $users = User::query()
            ->withCount('createdProducts')
            ->when($filters['q'] ?? null, function ($query, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $query->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like));
            })
            ->when($filters['role'] ?? null, fn ($q, $role) => $q->where('role', $role))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'filters'));
    }

    public function create(): View
    {
        $user = new User;

        return view('users.create', compact('user'));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
        $user->role = $data['role'];       // assigned explicitly by an authorized admin
        $user->status = $data['status'];
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.users.show', $user)->with('success', "User \"{$user->name}\" created.");
    }

    public function show(User $user): View
    {
        $user->loadCount(['createdProducts', 'stockMovements']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $user, $data) {
            $this->guardAdminChanges($request->user(), $user, $data['role'], $data['status']);

            $user->fill(['name' => $data['name'], 'email' => $data['email']]);
            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }
            $user->role = $data['role'];
            $user->status = $data['status'];
            $user->save();
        });

        return redirect()->route('admin.users.show', $user)->with('success', "User \"{$user->name}\" updated.");
    }

    /** Quick activate/deactivate toggle. */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        $newStatus = $user->isActive() ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;

        try {
            DB::transaction(function () use ($request, $user, $newStatus) {
                $this->guardAdminChanges($request->user(), $user, $user->role, $newStatus);
                $user->status = $newStatus;
                $user->save();
            });
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return back()->with('success', sprintf('User "%s" %s.', $user->name, $newStatus === User::STATUS_ACTIVE ? 'activated' : 'deactivated'));
    }

    /**
     * Safety rules (plan §3.3, Module 7):
     *  - an admin cannot demote or deactivate their own account (prevents self-lockout);
     *  - the last active administrator can never be demoted or deactivated.
     */
    private function guardAdminChanges(User $actor, User $target, string $newRole, string $newStatus): void
    {
        $losesAdminAccess = $target->isAdmin() && $target->isActive()
            && ($newRole !== User::ROLE_ADMIN || $newStatus !== User::STATUS_ACTIVE);

        if (! $losesAdminAccess) {
            return;
        }

        if ($actor->is($target)) {
            throw ValidationException::withMessages([
                'role' => 'You cannot remove your own administrator access or deactivate your own account.',
            ]);
        }

        // Lock active admin rows so two concurrent demotions cannot both pass the check.
        $activeAdmins = User::activeAdmins()->lockForUpdate()->pluck('id');

        if ($activeAdmins->reject(fn ($id) => (int) $id === (int) $target->id)->isEmpty()) {
            throw ValidationException::withMessages([
                'role' => 'At least one active administrator must remain.',
            ]);
        }
    }
}
