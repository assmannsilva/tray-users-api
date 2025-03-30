<?php
namespace App\Services;

use App\Exceptions\InvalidGoogleAuthTokenException;
use Google\Client;
use Google\Service\Oauth2;
use Google\Service\Oauth2\Userinfo;

class GoogleAuthService {

    
    public function __construct(
        private Client $client,
    ) { }

    /**
     * Gera a URL de autenticação do Google
     * @return string
     */
    public function generateAuthUrl() : string
    {
        $this->client->addScope(Oauth2::USERINFO_EMAIL);
        return $this->client->createAuthUrl();
    }

    /**
     * Retorna um novo token do Google
     * @param string $code
     * @throws InvalidGoogleAuthTokenException
     * @return string $access_token
     */
    public function getNewToken(String $code): string
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        if(!isset($token["access_token"])) throw new InvalidGoogleAuthTokenException;

        return $token["access_token"];
    }


    /**
     * Busca dados dados pessoais do usuário no Google
     * @param string $token
     * @return Userinfo
     */
    public function getUserInfo(String $token): Userinfo 
    {
        $this->client->setAccessToken($token);
        //Como eu preciso passar o client com o token para a construtor do serviço
        //Acaba não sendo interessante utilizar injeção de dependência
        $service = new \Google\Service\Oauth2($this->client);
        return $service->userinfo->get();
    }
}