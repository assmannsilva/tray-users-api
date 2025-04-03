<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchUsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    public function test_search_users_by_name()
    {
        User::factory(300)->create();
        User::factory()->create(["name" => "John Doe"]);

        $this->withoutExceptionHandling();
        $response = $this->get('/api/users/search?name=John');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'John Doe',
        ]);
    }

    public function test_search_users_by_last_name()
    {
        User::factory(300)->create();
        User::factory()->create(["name" => "John Doe"]);

        $this->withoutExceptionHandling();
        $response = $this->get('/api/users/search?name=Doe');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'John Doe',
        ]);
    }

    public function test_search_users_by_cpf()
    {
        User::factory(300)->create();
        User::factory()->create(["name" => "John Doe","cpf" => "04560015091"]);

        $this->withoutExceptionHandling();
        $response = $this->get('/api/users/search?cpf=04560015091');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'John Doe',
        ]);
    }
}
