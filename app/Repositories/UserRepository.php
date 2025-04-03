<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

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

    public function search(
        ?String $cpf_index,
        ?String $name_index
    ) : Paginator {
        return User::query()
        ->when($cpf_index, fn($query) => $query->where('cpf_index', $cpf_index))
        ->when($name_index, fn($query) => 
            $query->where(function ($q) use ($name_index) {
                $q->where('first_name_index', $name_index)
                  ->orWhereJsonContains('surname_tokens', $name_index);
            })
        )
        ->paginate(100);
    
    }
}