<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    protected function role(User $user, Workspace $workspace): ?string
    {
        return $workspace->members()
            ->where('user_id', $user->id)
            ->first()?->pivot->role;
    }

    public function view(User $user, Workspace $workspace): bool
    {
        return $this->role($user, $workspace) !== null;
    }

    public function update(User $user, Workspace $workspace): bool
    {
        return in_array($this->role($user, $workspace), ['owner', 'manager']);
    }
}
