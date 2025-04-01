<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCompleteRegistrationRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Log;

class UserController {

    /**
     * Completa o cadastro do usuário com os novos dados
     * @param UserCompleteRegistrationRequest $request
     */
    public function completeRegistration(
        UserCompleteRegistrationRequest $request,
        User $user,
        UserService $user_service,
        UserRepository $user_repository,
    ) {
        /* Acredito que esse nome para o método seja mais adequado, existe a discussão de que um controller 
          deve ter apenas create,update, etc. como métodos, no entanto nesse contexto não faz sentido chamar
          de create se o usuário já foi criado no callback do google  
        */
        try { 
            $updated_user = $user_service->completeRegistration(
                $user,
                $user_repository,
                $request->input("name"),
                $request->input("birth_date"),
                $request->input("cpf"),
            );
            return \response()->json($updated_user,200);
        }
        catch(Throwable $th) {
            Log::error($th->getMessage(),["trace" => $th->getTrace()]);
            return \response()->json(["error" => "internal error"],500);
        }
    } 
}