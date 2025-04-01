<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService {

    /**
     * Cria um novo usuário
     * @param string $email
     * @param UserRepository $user_repository
     * @return User
     */
    public function create(
        string $token,
        UserRepository $user_repository
    ): User {
        /**
         * Normalmente eu colocaria a criação direto no Service, 
         * mas como o teste especifica o uso do Repository pattern, 
         * decidi incluir essa camada aqui junto
         */
        return $user_repository->create($token);
    }

    /**
     * Completa o registro do usuário
     * @param User $user
     * @param UserRepository $user_repository
     * @param string $name
     * @param string $birth_date
     * @param string $cpf
     * @return User
     */
    public function completeRegistration(
        User $user,
        UserRepository $user_repository,
        string $name,
        string $birth_date,
        string $cpf
    ): User {
        $user = $user_repository->update($user,[
            "name" => $name,
            "birth_date" => $birth_date,
            "cpf" => $cpf
        ]);
        //SendEmail
        
        return $user;
    }


}