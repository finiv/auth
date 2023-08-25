<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test valid case
     *
     * @return void
     */
    public function testUserCanRegisterWithEmail(): void
    {
        $response = $this->postJson('/api/register/email', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /**
     * Test invalid case
     *
     * @return void
     */
    public function testUserRegistrationRequiresValidEmail(): void
    {
        $response = $this->postJson('/api/register/email', [
            'name' => $this->faker->name,
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
