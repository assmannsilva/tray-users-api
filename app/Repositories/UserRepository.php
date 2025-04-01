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

    /**
     * Cria um novo usuário ou atualiza o token, caso o email já exista
     * @param string $email
     * @param array $update_data
     * @return User
     */
    public function update(User $user, array $update_data) : User {
        $user->update($update_data);
        $user->refresh();
        return $user;
    }
}