<?php

namespace Tests\Feature\GoogleAuth;

use App\Services\GoogleAuthService;
use Google\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GenerateGoogleUrlTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientMock = Mockery::mock(Client::class);
        $this->googleAuthService = new GoogleAuthService($this->clientMock);
    }
    

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_generate_google_auth_url()
    {
        $this->clientMock->shouldReceive('setState')->once();
        $this->clientMock->shouldReceive('addScope')->once();
        $this->clientMock->shouldReceive('setPrompt')->once();
        $this->clientMock->shouldReceive('createAuthUrl')->once()->andReturn('https://fake-google-auth-url.com');

        $oauth2ServiceMock = Mockery::mock(\Google\Service\Oauth2::class);

        $this->clientMock->shouldReceive('getOAuth2Service')
            ->once()
            ->andReturn($oauth2ServiceMock);

        $oauth2ServiceMock->shouldReceive('generateCodeVerifier')
            ->once()
            ->andReturn('mocked_code_verifier');

        $authUrl = $this->googleAuthService->generateAuthUrl();

        $this->assertEquals('https://fake-google-auth-url.com', $authUrl);
    }

    public function testGenerateGoogleUrlWithError()
    {
        $this->instance(
            GoogleAuthService::class,
            Mockery::mock(GoogleAuthService::class, function (MockInterface $mock) {
                $mock->shouldReceive('generateAuthUrl')
                    ->andThrow(new \Exception('internal error'));   
            })
    
        );
        $response = $this->get('/api/auth/generate-token');

        $response->assertStatus(500);
        $response->assertJson(['error' => 'internal error']);
    }

    
}
