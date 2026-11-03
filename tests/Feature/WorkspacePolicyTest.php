<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use App\Policies\WorkspacePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspacePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_owners_and_managers_can_update_a_workspace(): void
    {
        $policy = new WorkspacePolicy;
        $owner = User::factory()->create();
        $workspace = $this->workspaceOwnedBy($owner);

        $expected = ['owner' => true, 'manager' => true, 'member' => false, 'client' => false];

        foreach ($expected as $role => $canUpdate) {
            $user = $role === 'owner' ? $owner : User::factory()->create();
            $workspace->members()->attach($user->id, ['role' => $role]);

            $this->assertTrue($policy->view($user, $workspace), "{$role} should be able to view");
            $this->assertSame($canUpdate, $policy->update($user, $workspace), "{$role} update permission");
        }
    }

    public function test_non_members_cannot_view_or_update_a_workspace(): void
    {
        $policy = new WorkspacePolicy;
        $workspace = $this->workspaceOwnedBy(User::factory()->create());
        $stranger = User::factory()->create();

        $this->assertFalse($policy->view($stranger, $workspace));
        $this->assertFalse($policy->update($stranger, $workspace));
    }

    private function workspaceOwnedBy(User $owner): Workspace
    {
        return Workspace::create([
            'name' => 'Acme',
            'slug' => 'acme',
            'type' => 'company',
            'owner_id' => $owner->id,
        ]);
    }
}
