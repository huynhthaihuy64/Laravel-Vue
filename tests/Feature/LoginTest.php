<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserLogin()
    {
        $response = $this->post('/api/admin/users/login/store', [
            'email' => 'huynhthaihuy64@gmail.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200);
    }
}
