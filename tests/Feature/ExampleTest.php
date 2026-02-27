<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_hardcoded_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(session('is_logged_in'));
        $this->get('/dashboard')->assertSee('Logout');
    }

    public function test_user_can_logout(): void
    {
        $this->withSession(['is_logged_in' => true])
            ->post('/logout')
            ->assertRedirect('/login');

        $this->get('/dashboard')->assertRedirect('/login');
    }
}
