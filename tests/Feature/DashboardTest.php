<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that SuperAdmin can see all short URLs for every company.
     */
    public function test_superadmin_can_see_all_short_urls(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);

        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user1 = User::factory()->create(['company_id' => $company1->id]);
        $user2 = User::factory()->create(['company_id' => $company2->id]);

        ShortUrl::factory()->create([
            'user_id' => $user1->id,
            'company_id' => $company1->id,
            'long_url' => 'https://company1.com',
        ]);

        ShortUrl::factory()->create([
            'user_id' => $user2->id,
            'company_id' => $company2->id,
            'long_url' => 'https://company2.com',
        ]);

        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('https://company1.com');
        $response->assertSee('https://company2.com');
    }
}
