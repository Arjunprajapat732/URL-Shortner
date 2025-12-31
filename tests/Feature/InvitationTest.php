<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that SuperAdmin can invite Admin in a new company.
     */
    public function test_superadmin_can_invite_admin_in_new_company(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);

        $response = $this->actingAs($superAdmin)->post('/invitations', [
            'name' => 'New Admin',
            'email' => 'admin@newcompany.com',
            'company_name' => 'New Company',
            'company_email' => 'newcompany@example.com',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('companies', [
            'name' => 'New Company',
            'email' => 'newcompany@example.com',
        ]);
        $this->assertDatabaseHas('invitations', [
            'email' => 'admin@newcompany.com',
            'role' => 'Admin',
        ]);
    }

    /**
     * Test that Admin can invite Admin in their own company.
     */
    public function test_admin_can_invite_admin_in_their_company(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->post('/invitations', [
            'name' => 'Another Admin',
            'email' => 'admin2@example.com',
            'role' => 'Admin',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('invitations', [
            'email' => 'admin2@example.com',
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);
    }

    /**
     * Test that Admin can invite Member in their own company.
     */
    public function test_admin_can_invite_member_in_their_company(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->post('/invitations', [
            'name' => 'New Member',
            'email' => 'member@example.com',
            'role' => 'Member',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('invitations', [
            'email' => 'member@example.com',
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
    }
}
