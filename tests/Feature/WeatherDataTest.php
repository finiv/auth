<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WeatherDataTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test success case
     *
     * @return void
     */
    public function testAuthenticatedUserCanAccessWeatherData(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/api/home');
        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'main']);
    }

    /**
     * Test fail auth case
     *
     * @return void
     */
    public function testUnauthenticatedUserCannotAccessWeatherData(): void
    {
        $response = $this->get('/api/home', ['Accept' => 'application/json']);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
