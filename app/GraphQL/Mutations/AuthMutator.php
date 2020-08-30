<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Illuminate\Support\Str;

use App\User;
use App\Role;

class AuthMutator extends MakeTokenResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        $input = Arr::only($args, ['email', 'password']);
        $user = User::where('email', $input['email'])->first();

        if(!Auth::once($input)) {
            throw new AuthenticationException('Login Failed');
        }

        $credentials = $this->build([
            'username' => $input['email'],
            'password' => $input['password']
        ]);

        $tokens = $this->makeToken($credentials);

        return [
            'tokens' => $tokens,
            'user' => Auth::user()
        ];
    }
}
