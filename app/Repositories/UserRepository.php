<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository {
    
    /**
     * Cria um novo usuário a partir do token
     * @param string $token
     * @return User
     */
    public function create(string $token) : User {
        return User::create([
            "google_token" => $token
        ]);
    }
}