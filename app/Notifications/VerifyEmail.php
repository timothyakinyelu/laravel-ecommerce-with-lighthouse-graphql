<?php

namespace App\Notifications;

use Carbon\Carbon;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{
    /*
    * Get the front end url that will be included in the notification
    * email sent to newly registered user
    * @param mixed $notifiable
    **
    * @return string 
    */

    protected function verificationUrl($notifiable)
    {
        $payload = base64_encode(json_encode([
            'id' => $notifiable->getKey(),
            'hashed_email' => encrypt($notifiable->getEmailForVerification()),
            'expires_in' => encrypt(Carbon::now()->addMinutes(10)->toIso8601String())
        ]));

        return app(config('graph-passport-auth.verify_email.base_url')).'?token='.$payload;
        // return $payload;
    }
}