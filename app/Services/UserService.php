<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService {

    /**
     * Retorna um model de usuário baseado no email e no token especificado
     * @param string $email
     * @param string $token
     * @param UserRepository $user_repository
     * @return User
     */
    public function create(
        string $email,
        string $token,
        UserRepository $user_repository
    ): User {
        /**
         * Normalmente eu colocaria a criação direto no Service, 
         * mas como o teste especifica o uso do Repository pattern, 
         * decidi incluir essa camada aqui junto
         */
        return $user_repository->updateOrCreate($email,$token);
    }
}