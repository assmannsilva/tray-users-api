<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidGoogleAuthException;
use App\Repositories\UserRepository;
use App\Services\GoogleAuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    const ERROR_MESSAGE = "Erro interno na API";

    /**
     * Gera a URL de autenticação do Google
     * @param GoogleAuthService $google_auth_service
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken(GoogleAuthService $google_auth_service) : JsonResponse
    {
        try {
            $google_auth_service->generateAuthUrl();
            return response()->json(['auth_url' => $google_auth_service->generateAuthUrl()]);
        }
        catch(Throwable $th) {
            Log::error($th->getMessage(),["trace" => $th->getTrace()]);
            return \response()->json([
                "error" => self::ERROR_MESSAGE
            ],500);
        }
    }

    /**
     * Callback da autenticação do Google
     * @param Request $request
     * @param GoogleAuthService $google_auth_service
     * @param UserService $user_service
     * @param UserRepository $user_repository
     * @return RedirectResponse|JsonResponse
     */
    public function googleCallback(
        Request $request,
        GoogleAuthService $google_auth_service,
        UserService $user_service,
        UserRepository $user_repository
    ): RedirectResponse|JsonResponse {
        try {
            if($request->exists("error")) return \redirect(\config("app.url"));

            $token = $google_auth_service->getNewToken(
                $request->get('code'),
                $request->get("state")
            );

            $user = $user_service->create($token,$user_repository);

            return \redirect(\config("app.url")."/users/{$user->id}/complete-registration");
        } 
        catch (InvalidGoogleAuthException $exception) {
            return \response()->json([
                "error" => $exception->getMessage()
            ],422); //TODO: rever código HTTP
        }
        catch(Throwable $th) {
            Log::error($th->getMessage(),["trace" => $th->getTrace()]);
            return \response()->json([
                "error" => self::ERROR_MESSAGE
            ],500);
        }
        
    }
}
