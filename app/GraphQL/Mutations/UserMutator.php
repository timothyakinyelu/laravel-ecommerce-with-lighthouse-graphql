<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use App\Notifications\NewUserResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

use App\User;
use App\Role;

class UserMutator
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function createUser($_, array $args)
    {
        // dd(csrf_token());
        $validator = Validator::make($args, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|regex:/^.+@.+$/i',
            'dob' => 'date|nullable',
            'gender' => 'string|nullable',
        ]);
            
        if($validator->fails()) {
            return $user = $validator->errors();
        }

        $pw = User::generatePassword();
        $vendor_role = Role::where('role_key','vendor')->first();
        $user = new User;

        $user->first_name = $args['first_name'];
        $user->last_name = $args['last_name'];
        $user->email = $args['email'];
        $user->dob = $args['dob'];
        $user->gender = $args['gender'];
        $user->is_active = $args['is_active'];
        $user->password = $pw;
        $user->roles()->attach($vendor_role);

        $user->save();

        // $email = $user->email;
        // $this->sendUserEmail($user, $email);
        
        return $user;
    }

    // public function sendUserEmail($user, $email) 
    // {
    //     //New user password reset code
    //     $token = $this->createToken($email);
    //     $user->notify(new NewUserResetPasswordMail($token));
    // }

    // public function createToken($email) 
    // {
    //     $oldToken = DB::table('password_resets')->where('email', $email)->first();
    //     if ($oldToken) {
    //         return $oldToken->token;
    //     }

    //     $token = Str::random(60);
    //     $this->saveToken($token, $email);
    //     return $token;
    // }

    // public function saveToken($token, $email) 
    // {
    //     DB::table('password_resets')->insert([
    //         'email' => $email,
    //         'token' => $token,
    //         'created_at' => Carbon::now()
    //     ]);
    // }
}
