<?php
namespace App\Services;

use App\Helpers\SodiumCrypto;
use App\Jobs\SendMailCompleteRegistration;
use App\Mail\CompleteRegistration;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
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
            "birthday" => $birth_date,
            "cpf" => $cpf
        ]);

        /*
            Sim, dava pra enviar o email usando a função queue do Mail facade somente,
            mas eu gostaria de chamar a API do Google na fila
            e demonstrar o uso de Jobs também
        */
        SendMailCompleteRegistration::dispatch($user);
        return $user;
    }

    /**
     * Envia um email de cadastro concluído para o usuário cadastrado no Google
     * @param GoogleAuthService $google_auth_service
     * @param User $user
     * @return void
     */
    public function sendMailCompleteRegistration(
        GoogleAuthService $google_auth_service,
        User $user
    ) {
        $token = $user->google_token;
        $user_info = $google_auth_service->getUserInfo($token);
        Mail::to($user_info->email)->send(new CompleteRegistration);
    }

    /**
     * Busca usuários com base no nome ou CPF
     * @param string|null $search_name
     * @param string|null $cpf
     * @param string|null $page
     * @param UserRepository $user_repository
     * @return Paginator
     */
    public function search(
        ?String $search_name,
        ?String $cpf,
        ?String $page,
        UserRepository $user_repository
    ): Paginator {
        $cache_key = "user_query_results_name_{$search_name}_cpf_{$cpf}_page_{$page}";
        $cache_tag = $search_name || $cpf ? "filter_search" : "clean_search";

        $index_key_name = $search_name ? SodiumCrypto::getIndex(
            $search_name,
            SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.name_index")
        ) : null;

        $index_key_cpf = $cpf ? SodiumCrypto::getIndex(
            $cpf,
            SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.cpf_index")
        ) : null;

        return Cache::tags([$cache_tag])->remember(
            $cache_key,
            \now()->addMinutes(15), 
            function () use($index_key_name,$index_key_cpf,$user_repository) {
            return $user_repository->search(
                $index_key_cpf,
                $index_key_name,
            );
        });
    }


}