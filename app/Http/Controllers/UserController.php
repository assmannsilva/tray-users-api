<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCompleteRegistrationRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Log;
class UserController {

    /**
     * Completa o cadastro do usuário com os novos dados
     * @param UserCompleteRegistrationRequest $request
     * @param User $user
     * @param UserService $user_service
     * @param UserRepository $user_repository
     * @return JsonResponse
     */
    public function completeRegistration(
        UserCompleteRegistrationRequest $request,
        User $user,
        UserService $user_service,
        UserRepository $user_repository,
    ) : JsonResponse {
        /* Acredito que esse nome para o método seja mais adequado, existe a discussão de que um controller 
          deve ter apenas create,update, etc. como métodos, no entanto nesse contexto não faz sentido chamar
          de create se o usuário já foi criado no callback do google  
        */
        try { 
            $updated_user = $user_service->completeRegistration(
                $user,
                $user_repository,
                $request->input("name"),
                $request->input("birthday"),
                $request->input("cpf"),
            );
            return \response()->json($updated_user,200);
        }
        catch(Throwable $th) {
            Log::error($th->getMessage(),["trace" => $th->getTrace()]);
            return \response()->json(["error" => "internal error"],500);
        }
    }

    /**
     * Buca usuários cadastrados no sistema
     * @param Request $request
     * @param UserService $user_service
     * @param UserRepository $user_repository
     * 
     */
    public function search(
        Request $request,
        UserService $user_service,
        UserRepository $user_repository
    ): Paginator|JsonResponse {
        try { 
            return $user_service->search(
                $request->query("name"),
                $request->query("cpf"),
                $request->query("page"),
                $user_repository
            );
        }
        catch(Throwable $th) {
            Log::error($th->getMessage(),["trace" => $th->getTrace()]);
            return \response()->json(["error" => "internal error"],500);
        }
        
    }
}