<?php

namespace App\Traits;

use App\Notifications\VerifyEmail;

trait VerifyEmailTrait {
    /*
    * this over-writes laravel's default sendEmailVerificationNotification
    *
    * @return void
    */

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }
}