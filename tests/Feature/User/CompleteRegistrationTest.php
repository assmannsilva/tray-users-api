<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class CompleteRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
{
    parent::setUp();
    
    // Adiciona o provider manualmente
    $this->faker->addProvider(new \Faker\Provider\pt_BR\Person($this->faker));
}

    public function test_complete_registration_sucessuful()
    {
        Queue::fake();
        $submit_data = [
            "cpf" => "747.958.470-96",
            "name" => "Caue ".$this->faker->lastName(),   
            "birthday" => $this->faker->dateTimeBetween("-50 years","-18 years")->format("Y-m-d")
        ];

        $user = User::factory()->create([
            "google_token" => $this->faker->uuid(),
            "name" => null,
            "birthday" => null,
            "cpf" => null
        ]);

        $this->post(
            "api/users/{$user->id}/complete-registration",
            $submit_data
        )->assertStatus(200);

        $user->refresh();

        $this->assertEquals($submit_data["name"],$user->name);
        $this->assertEquals($submit_data["birthday"],$user->birthday);
        $this->assertEquals(preg_replace('/[^0-9]/', '', $submit_data["cpf"]),$user->cpf);
        
    }

    public function test_complete_registration_with_invalid_cpf()
    {
        Queue::fake();
        $submit_data = [
            "cpf" => "747.20.21-54",
            "name" => "Caue ".$this->faker->lastName(),   
            "birthday" => $this->faker->dateTimeBetween("-50 years","-18 years")->format("Y-m-d")
        ];

        $user = User::factory()->create([
            "google_token" => $this->faker->uuid(),
            "name" => null,
            "birthday" => null,
            "cpf" => null
        ]);

        $this->post(
            "api/users/{$user->id}/complete-registration",
            $submit_data
        )->assertStatus(302)->withSessionErrors([
            "cpf" => "O CPF informado não é válido."
        ]);

        $user->refresh();

        $this->assertNotEquals($submit_data["name"],$user->name);
        $this->assertNotEquals($submit_data["birthday"],$user->birthday);
        $this->assertNotEquals(preg_replace('/[^0-9]/', '', $submit_data["cpf"]),$user->cpf);
        
    }

    public function test_complete_registration_throws_an_exception()
    {
        Queue::fake();
        $submit_data = [
            "cpf" => "747.958.470-96",
            "name" => "Caue ".$this->faker->lastName(),   
            "birthday" => $this->faker->dateTimeBetween("-50 years","-18 years")->format("Y-m-d")
        ];

        $user = User::factory()->create([
            "google_token" => $this->faker->uuid(),
            "name" => null,
            "birthday" => null,
            "cpf" => null
        ]);

        $this->instance(
            UserService::class,
            Mockery::mock(UserService::class, function ($mock) {
                $mock->shouldReceive('completeRegistration')
                    ->andThrow(new RuntimeException("Erro ao completar o registro"));
            })
        );

        $this->post(
            "api/users/{$user->id}/complete-registration",
            $submit_data
        )->assertStatus(500)->assertJson(["error" => "internal error"]);

        $user->refresh();

        $this->assertNotEquals($submit_data["name"],$user->name);
        $this->assertNotEquals($submit_data["birthday"],$user->birthday);
        $this->assertNotEquals(preg_replace('/[^0-9]/', '', $submit_data["cpf"]),$user->cpf);
        
    }
}
