<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PublicUrlPrefixTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_prefixed_path_redirects_to_clean_url(): void
    {
        $this->get('/public/admin/login')
            ->assertRedirect('/admin/login');
    }

    public function test_generated_routes_respect_forced_root_url(): void
    {
        URL::forceRootUrl('https://ceac.stateus.org');
        URL::forceScheme('https');

        $this->assertSame('https://ceac.stateus.org/admin/login', route('admin.login'));
        $this->assertStringNotContainsString('/public/', route('admin.applications.index'));
    }
}