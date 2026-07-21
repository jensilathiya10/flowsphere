<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    protected function role(User $user, Project $project): ?string
    {
        return $project->workspace->members()
            ->where('user_id', $user->id)
            ->first()?->pivot->role;
    }

    public function view(User $user, Project $project): bool
    {
        return $this->role($user, $project) !== null;
    }

    public function update(User $user, Project $project): bool
    {
        return in_array($this->role($user, $project), ['owner', 'manager']);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->role($user, $project) === 'owner';
    }

    public function manageTeam(User $user, Project $project): bool
    {
        return in_array($this->role($user, $project), ['owner', 'manager']);
    }
}
