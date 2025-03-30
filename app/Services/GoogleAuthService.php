<?php
namespace App\Services;

use Google\Client;
use Google\Service\Oauth2;
use Google\Service\Oauth2\Userinfo;

class GoogleAuthService {

    
    public function __construct(
        private Client $client,
    ) {
    }

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
     * Busca dados dados pessoais do usuário no Google
     * @param string $code
     * @param string $state
     * @throws \App\Exceptions\InvalidGoogleAuthStateException
     * @return Userinfo
     */
    public function getUserInfo(String $code): Userinfo {

        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($token);

        //Como eu preciso passar o client com o token para a construtor do serviço
        //Acaba não sendo interessante utilizar injeção de dependência
        $service = new \Google\Service\Oauth2($this->client);
        return $service->userinfo->get();
    }
}