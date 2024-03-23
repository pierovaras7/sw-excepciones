<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        //
        $userId = $event->user->id;

        // Aquí puedes insertar los datos del inicio de sesión en tu tabla
        LoginHistory::create([
            'user_id' => $userId,
            'login_at' => now(),
        ]);
    }
}
