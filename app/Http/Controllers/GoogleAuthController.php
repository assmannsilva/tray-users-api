<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{

    /**
     * Gera a URL de autenticação do Google
     * @param GoogleAuthService $googleAuthService
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken(GoogleAuthService $googleAuthService)
    {
        $googleAuthService->generateAuthUrl();
        return response()->json(['auth_url' => $googleAuthService->generateAuthUrl()]);
    }

    /**
     * Callback da autenticação do Google
     * @param Request $request
     * @param GoogleAuthService $googleAuthService
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleCallback(Request $request,GoogleAuthService $googleAuthService) 
    {   
        $user_info = $googleAuthService->getUserInfo($request->get('code'));
    }
}
