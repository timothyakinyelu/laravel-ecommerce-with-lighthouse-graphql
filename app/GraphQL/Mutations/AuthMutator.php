<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Cookie;
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


        // if(!$context->request->hasCookie('_token')) {
        //     $tokens = Auth::user()->createToken('access token')->accessToken;
        //     Cookie::queue('_token', $token, 1800, '/', $context->request->getHost(), false, true);
        // }
        return [
            'tokens' => $tokens,
            'user' => $user
        ];
    }
}
