<?php

namespace Tests\Feature\GoogleAuth;

use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Str; 

class GoogleCallbackTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    protected function setUp(): void
    {
        parent::setUp();
        $this->googleAuthService = Mockery::mock(GoogleAuthService::class);
        $this->app->instance(GoogleAuthService::class, $this->googleAuthService);    
    }
    

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_callback_user_created()
    {
        $this->googleAuthService
            ->shouldReceive('getNewToken')
            ->once()
            ->with('valid_code', 'valid_uuid')
            ->andReturn('test_token');
    
        $this->get("google-callback?code=valid_code&state=valid_uuid")
            ->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'google_token' => 'test_token',
        ]);
    }
}
