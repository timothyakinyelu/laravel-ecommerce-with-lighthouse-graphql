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

class AuthMutator
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args, GraphQLContext $context = null)
    {
        $credentials = Arr::only($args, ['email', 'password']);

        if(!Auth::once($credentials)) {
            throw new AuthenticationException('Login Failed');
        }

        if(!$context->request->hasCookie('_token')) {
            $token = Auth::user()->createToken('access token')->accessToken;
            Cookie::queue('_token', $token, 1800, '/', $context->request->getHost(), false, true);
        }
        return $token;

        // if (Auth::once($credentials)) {

        //     $user = auth()->user();
        //     // return $user;
        //     // return $user->hasRole('developer');
        //     // return $user->givePermissionsTo('edit-tasks');
        //     return $user->can('edit-tasks');
        // }

        // return null;
    }
}
