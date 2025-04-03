<?php

namespace Tests\Feature;

use App\Services\GoogleAuthService;
use Google\Client;
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Exceptions\InvalidGoogleAuthException;
use Illuminate\Support\Facades\Cache;

class GetGoogleTokenTest extends TestCase
{

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

    
    public function test_get_new_token_validates_state()
    {   
        $uuid = (string) Str::uuid();
        Cache::shouldReceive('get')->with($uuid.'_state',Mockery::any())->andReturn('state_code');
        Cache::shouldReceive('get')->with($uuid.'_code_verifier',Mockery::any())->andReturn('test_code_verifier');

        $this->clientMock->shouldReceive('fetchAccessTokenWithAuthCode')
            ->once()    
            ->with('valid_code', 'test_code_verifier')
            ->andReturn(['access_token' => 'test_token']);
    
        $token = $this->googleAuthService->getNewToken('valid_code', $uuid);
    
        $this->assertEquals('test_token', $token);
    }

    public function test_get_new_token_throws_exception_when_state_is_invalid()
    {
        $this->expectException(InvalidGoogleAuthException::class);

        $this->googleAuthService->getNewToken('valid_code', 'invalid_uuid');
    }

    public function test_get_new_token_throws_exception_when_state_is_wrong()
    {
        Cache::shouldReceive('get')->with("invalid_state_state",Mockery::any())->andReturn(\null);

        $this->expectException(InvalidGoogleAuthException::class);

        $this->googleAuthService->getNewToken('valid_code',"invalid_state");
    }
}   
