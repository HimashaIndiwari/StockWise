<div class="card h-100">
    <div class="card-header">
        <h2 class="card-title h6 mb-0"><i class="bi bi-person me-1" aria-hidden="true"></i> Profile information</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" novalidate>
            @csrf
            @method('PATCH')

            <x-form.input name="name" label="Name" :value="$user->name" required autocomplete="name" />
            <x-form.input name="email" type="email" label="Email address" :value="$user->email" required autocomplete="username" />

            {{-- Role is displayed for information only; it cannot be changed here. --}}
            <div class="mb-3">
                <span class="form-label d-block">Role</span>
                <x-role-badge :user="$user" />
                <div class="form-text">Roles can only be changed by an administrator.</div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2 me-1" aria-hidden="true"></i> Save profile
            </button>
        </form>
    </div>
</div>
