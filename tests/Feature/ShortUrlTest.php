<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Admin can create short URLs.
     */
    public function test_admin_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->post('/short-urls', [
            'long_url' => 'https://example.com',
        ]);

        $response->assertRedirect(route('short-urls.index'));
        $this->assertDatabaseHas('short_urls', [
            'long_url' => 'https://example.com',
            'user_id' => $admin->id,
            'company_id' => $company->id,
        ]);
    }

    /**
     * Test that Member can create short URLs.
     */
    public function test_member_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($member)->post('/short-urls', [
            'long_url' => 'https://example.com',
        ]);

        $response->assertRedirect(route('short-urls.index'));
        $this->assertDatabaseHas('short_urls', [
            'long_url' => 'https://example.com',
            'user_id' => $member->id,
            'company_id' => $company->id,
        ]);
    }

    /**
     * Test that SuperAdmin cannot create short URLs.
     */
    public function test_superadmin_cannot_create_short_urls(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);

        $response = $this->actingAs($superAdmin)->get('/short-urls/create');
        $response->assertStatus(403);

        $response = $this->actingAs($superAdmin)->post('/short-urls', [
            'long_url' => 'https://example.com',
        ]);
        $response->assertStatus(403);
    }

    /**
     * Test that Admin can only see short URLs created in their own company.
     */
    public function test_admin_can_only_see_short_urls_in_their_company(): void
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        
        $admin1 = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company1->id,
        ]);
        
        $admin2 = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company2->id,
        ]);

        $member1 = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company1->id,
        ]);

        // Create short URLs in company1
        ShortUrl::factory()->create([
            'user_id' => $member1->id,
            'company_id' => $company1->id,
            'long_url' => 'https://company1.com',
        ]);

        // Create short URLs in company2
        ShortUrl::factory()->create([
            'user_id' => $admin2->id,
            'company_id' => $company2->id,
            'long_url' => 'https://company2.com',
        ]);

        $response = $this->actingAs($admin1)->get('/short-urls');
        $response->assertStatus(200);
        $response->assertSee('https://company1.com');
        $response->assertDontSee('https://company2.com');
    }

    /**
     * Test that Member can only see short URLs created by themselves.
     */
    public function test_member_can_only_see_their_own_short_urls(): void
    {
        $company = Company::factory()->create();
        
        $member1 = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
        
        $member2 = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        // Create short URLs for member1
        ShortUrl::factory()->create([
            'user_id' => $member1->id,
            'company_id' => $company->id,
            'long_url' => 'https://member1.com',
        ]);

        // Create short URLs for member2
        ShortUrl::factory()->create([
            'user_id' => $member2->id,
            'company_id' => $company->id,
            'long_url' => 'https://member2.com',
        ]);

        $response = $this->actingAs($member1)->get('/short-urls');
        $response->assertStatus(200);
        $response->assertSee('https://member1.com');
        $response->assertDontSee('https://member2.com');
    }

    /**
     * Test that short URLs are publicly resolvable and redirect to the original URL.
     */
    public function test_short_urls_are_publicly_resolvable_and_redirect(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        $shortUrl = ShortUrl::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'long_url' => 'https://example.com',
            'hits' => 0, // Start with 0 hits
        ]);

        $initialHits = $shortUrl->hits;
        $response = $this->get('/s/' . $shortUrl->short_code);
        $response->assertRedirect('https://example.com');
        
        // Check that hits were incremented
        $shortUrl->refresh();
        $this->assertEquals($initialHits + 1, $shortUrl->hits);
    }
}
