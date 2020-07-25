<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuthMutator
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        $credentials = Arr::only($args, ['email', 'password']);

        if (Auth::once($credentials)) {

            $user = auth()->user();
            // return $user;
            // return $user->hasRole('developer');
            // return $user->givePermissionsTo('edit-tasks');
            return $user->can('edit-tasks');
        }

        return null;
    }
}
