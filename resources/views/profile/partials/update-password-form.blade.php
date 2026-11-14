<div class="card h-100">
    <div class="card-header">
        <h2 class="card-title h6 mb-0"><i class="bi bi-key me-1" aria-hidden="true"></i> Change password</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('password.update') }}" novalidate>
            @csrf
            @method('PUT')

            <x-form.input name="current_password" type="password" label="Current password" bag="updatePassword" required autocomplete="current-password" id="update_password_current_password" />
            <x-form.input name="password" type="password" label="New password" bag="updatePassword" required autocomplete="new-password" id="update_password_password" help="At least 8 characters." />
            <x-form.input name="password_confirmation" type="password" label="Confirm new password" bag="updatePassword" required autocomplete="new-password" id="update_password_password_confirmation" />

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-shield-lock me-1" aria-hidden="true"></i> Change password
            </button>
        </form>
    </div>
</div>
