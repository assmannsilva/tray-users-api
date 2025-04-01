<?php
namespace App\Services;

use App\Exceptions\InvalidGoogleAuthException;
use Google\Client;
use Google\Service\Oauth2;
use Google\Service\Oauth2\Userinfo;
use Illuminate\Support\Str;

class GoogleAuthService {

    
    public function __construct(
        private Client $client,
    ) { }

    /**
     * Configura o código state para realizar uma checagem posterior no callback_uri
     * @return void
     */
    private function configureStateCode() 
    {
        $state_code = Str::uuid();
        session(["state_code" => $state_code]);
        $this->client->setState($state_code);
    }

    /**
     * Configura o code verifier, também é checado na callback_uri
     * Acaba sendo necessário se for usado o state code, senão a chamada do token é inválida
     * @return void
     */
    private function configureCodeVerifier() 
    {
        $google_code_verifier = $this->client->getOAuth2Service()->generateCodeVerifier();
        session(["google_code_verifier" => $google_code_verifier]);
    }

    /**
     * Gera a URL de autenticação do Google
     * @return string
     */
    public function generateAuthUrl() : string
    {
        $this->client->addScope(Oauth2::USERINFO_EMAIL);
        $this->client->setPrompt('consent');

        $this->configureStateCode();
        $this->configureCodeVerifier();
    
        return $this->client->createAuthUrl();
    }

    /**
     * Retorna um novo token do Google
     * @param string $code
     * @param string $state_code_from_google
     * @throws InvalidGoogleAuthException
     * @return string $access_token
     */
    public function getNewToken(String $code,String $state_code_from_google): string
    {
        if(!session("state_code") || session("state_code") != $state_code_from_google) {
            throw new InvalidGoogleAuthException;
        }

        $token = $this->client->fetchAccessTokenWithAuthCode($code,session("google_code_verifier"));
        if(!isset($token["access_token"])) throw new InvalidGoogleAuthException;

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
        //Como eu preciso passar o client com o token para o construtor do serviço
        //Acaba não sendo interessante utilizar injeção de dependência
        $service = new \Google\Service\Oauth2($this->client);
        return $service->userinfo->get();
    }
}