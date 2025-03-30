<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository {
    
    /**
     * Cria um novo usuário ou atualiza o token, caso o email já exista
     * @param string $email
     * @param string $token
     * @return User
     */
    public function updateOrCreate(string $email, string $token) : User {
        return User::updateOrCreate(
            ["email" => $email],
            [
                "email" => $email,
                "google_token" => $token
            ], 
        );
    }
}