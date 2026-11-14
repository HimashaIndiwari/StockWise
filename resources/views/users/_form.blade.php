@php($isSelf = $user->exists && $user->is(auth()->user()))
<div class="card" style="max-width: 720px">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <x-form.input name="name" label="Full name" :value="$user->name" required maxlength="255" autocomplete="off" />
            </div>
            <div class="col-md-6">
                <x-form.input name="email" type="email" label="Email address" :value="$user->email" required maxlength="255" autocomplete="off" />
            </div>
            <div class="col-md-6">
                <x-form.select name="role" label="Role" required :value="$user->role ?? 'staff'"
                               :options="['staff' => 'Staff', 'admin' => 'Admin']"
                               :help="$isSelf ? 'You cannot remove your own admin role.' : 'Admins can manage all records and users.'" />
            </div>
            <div class="col-md-6">
                <x-form.select name="status" label="Status" required :value="$user->status ?? 'active'"
                               :options="['active' => 'Active', 'inactive' => 'Inactive']"
                               :help="$isSelf ? 'You cannot deactivate your own account.' : 'Inactive users cannot log in.'" />
            </div>
        </div>

        <hr>
        <p class="small text-muted mb-3">
            {{ $user->exists ? 'Leave the password fields empty to keep the current password.' : 'Set an initial password and share it with the user securely.' }}
        </p>
        <div class="row">
            <div class="col-md-6">
                <x-form.input name="password" type="password" :label="$user->exists ? 'New password' : 'Password'" :required="! $user->exists" autocomplete="new-password" help="At least 8 characters." />
            </div>
            <div class="col-md-6">
                <x-form.input name="password_confirmation" type="password" label="Confirm password" :required="! $user->exists" autocomplete="new-password" />
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button type="submit" class="btn btn-success"><i class="bi bi-check2 me-1" aria-hidden="true"></i> {{ $user->exists ? 'Save changes' : 'Create user' }}</button>
        <a href="{{ $user->exists ? route('admin.users.show', $user) : route('admin.users.index') }}" class="btn btn-light border">Cancel</a>
    </div>
</div>
