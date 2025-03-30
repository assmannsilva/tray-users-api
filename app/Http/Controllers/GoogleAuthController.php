<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidGoogleAuthTokenException;
use App\Repositories\UserRepository;
use App\Services\GoogleAuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{

    /**
     * Gera a URL de autenticação do Google
     * @param GoogleAuthService $google_auth_service
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken(GoogleAuthService $google_auth_service)
    {
        try {
            $google_auth_service->generateAuthUrl();
            return response()->json(['auth_url' => $google_auth_service->generateAuthUrl()]);
        }
        catch(Throwable $th) {
            Log::error($th->getMessage(),["trace" => $th->getTrace()]);
            return \response()->json([
                "error" => "internal error"
            ],500);
        }
    }

    /**
     * Callback da autenticação do Google
     * @param Request $request
     * @param GoogleAuthService $google_auth_service
     * @param UserService $user_service
     * @param UserRepository $user_repository
     */
    public function googleCallback(
        Request $request,
        GoogleAuthService $google_auth_service,
        UserService $user_service,
        UserRepository $user_repository
    ) {
        try {   
            $token = $google_auth_service->getNewToken($request->get('code'));
            $user_info = $google_auth_service->getUserInfo($token);
            $user = $user_service->create(
                $user_info->email,
                $token,
                $user_repository
            );

            return \response()->json($user,201);
        } 
        catch (InvalidGoogleAuthTokenException $exception) {
            return \response()->json([
                "error" => $exception->getMessage()
            ],422); //TODO: rever código HTTP
        }
        catch(Throwable $th) {
            Log::error($th->getMessage(),["trace" => $th->getTrace()]);
            return \response()->json([
                "error" => "internal error"
            ],500);
        }
        
    }
}
