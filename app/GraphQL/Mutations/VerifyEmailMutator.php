<?php

namespace App\GraphQL\Mutations;

use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\ModelNotFoundException;
// use Nuwave\Lighthouse\Exceptions\ValidationException;
use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VerifyEmailMutator extends MakeTokenResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * 
     * * @throws \Exception
     * * @return array
     */
    public function resolve($_, array $args)
    {
        $decoded = json_decode(base64_decode($args['token']));
        $expired_at = decrypt($decoded->expires_in);
        $email = decrypt($decoded->hashed_email);

        if(Carbon::parse($expired_at) < now()) {
            throw new ValidationException([
                'status' => 'INVALID_TOKEN',
            ], 'Validation Error');
        }

        $model = app(config('auth.providers.users.model'));

        try {
            $user = $model->where('email', $email)->firstOrFail();
            $user->markEmailAsVerified();
            event(new Verified($user));

            Auth::onceUsingId($user->id);
            $credentials = $this->build([
                'username' => $user->email
            ],'verify-email-grant');

            $tokens = $this->makeToken($credentials);
            return [
                'tokens' => $tokens,
                'status' => 'SUCCESS'
            ];
        } catch (ModelNotFoundException $e) {
            throw new ValidationException([
                'status' => 'INVALID_TOKEN'
            ], 'Validation Error');
        }
    }
}
