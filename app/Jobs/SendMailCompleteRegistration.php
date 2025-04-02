<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\GoogleAuthService;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMailCompleteRegistration implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private User $user) { }

    /**
     * Execute the job.
     */
    public function handle(
        GoogleAuthService $google_auth_service,
        UserService $user_service
    ): void
    {
        $user_service->sendMailCompleteRegistration(
            $google_auth_service,
            $this->user
        );
    }

    public function failed()
    {
        //Aqui enviaria para um canal de erros imprevistos
    }
}
