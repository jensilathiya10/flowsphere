<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_list_workspaces(): void
    {
        $this->getJson('/api/workspaces')->assertUnauthorized();
    }

    public function test_creating_a_workspace_makes_the_user_its_owner(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/workspaces', [
            'name' => 'Acme Inc',
            'type' => 'startup',
        ]);

        $response->assertCreated()->assertJsonPath('name', 'Acme Inc');

        $workspace = Workspace::firstOrFail();
        $this->assertSame($user->id, $workspace->owner_id);
        $this->assertSame('owner', $workspace->members()->first()->pivot->role);
        $this->assertSame($workspace->id, $user->fresh()->current_workspace_id);
    }

    public function test_workspace_type_must_be_valid(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/workspaces', ['name' => 'Acme', 'type' => 'galaxy'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('type');
    }

    public function test_users_only_see_workspaces_they_belong_to(): void
    {
        $user = User::factory()->create();
        $mine = $this->workspaceFor($user, 'member');
        $this->workspaceFor(User::factory()->create(), 'owner');

        Sanctum::actingAs($user);

        $this->getJson('/api/workspaces')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $mine->id);
    }

    public function test_users_cannot_switch_to_a_workspace_they_do_not_belong_to(): void
    {
        $user = User::factory()->create();
        $other = $this->workspaceFor(User::factory()->create(), 'owner');

        Sanctum::actingAs($user);

        $this->patchJson("/api/workspaces/{$other->id}/switch")->assertForbidden();
        $this->assertNull($user->fresh()->current_workspace_id);
    }

    private function workspaceFor(User $user, string $role): Workspace
    {
        $workspace = Workspace::create([
            'name' => 'Workspace',
            'slug' => 'workspace-'.uniqid(),
            'type' => 'company',
            'owner_id' => $user->id,
        ]);

        $workspace->members()->attach($user->id, ['role' => $role, 'joined_at' => now()]);

        return $workspace;
    }
}
