@php($compact = $compact ?? false)
@if ($user->isActive())
    <x-confirm-form :action="route('admin.users.status', $user)" method="PATCH"
                    :message="'Deactivate “'.$user->name.'”? They will be signed out and unable to log in.'"
                    icon="person-x" :label="$compact ? 'Deactivate '.$user->name : 'Deactivate'"
                    :label-class="$compact ? 'visually-hidden' : 'ms-1'"
                    :button-class="$compact ? 'btn btn-sm btn-outline-danger' : 'btn btn-outline-danger'" />
@else
    <x-confirm-form :action="route('admin.users.status', $user)" method="PATCH"
                    :message="'Activate “'.$user->name.'”? They will be able to log in again.'"
                    icon="person-check" :label="$compact ? 'Activate '.$user->name : 'Activate'"
                    :label-class="$compact ? 'visually-hidden' : 'ms-1'"
                    :button-class="$compact ? 'btn btn-sm btn-outline-success' : 'btn btn-outline-success'" />
@endif
