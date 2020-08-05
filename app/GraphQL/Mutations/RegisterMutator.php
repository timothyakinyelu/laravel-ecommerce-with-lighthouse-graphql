<?php

namespace App\GraphQL\Mutations;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;

class RegisterMutator extends MakeTokenResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        // Validate input values
        $validator = Validator::make($args, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users|regex:/^.+@.+$/i',
            'password' => 'required|string|min:6|confirmed',
            'dob' => 'date|nullable',
            'gender' => 'string|nullable',
        ]);

        if($validator->fails()) {
            return $user = $validator->errors();
        }

        /*
        * Get model provider from config
        */
        $model = app(config('auth.providers.users.model'));

        $input = collect($args)->except('password_confirmation')->toArray();
        $input['password'] = Hash::make($input['password']);

        $model->fill($input);
        $model->save();

        // if admin wants users to verify their email
        // before getting access to use the application
        if($model instanceof MustVerifyEmail) {
            $model->sendEmailVerificationNotification();

            event(new Registered($model));

            return [
                'tokens' => [],
                'status' => 'VERIFY_EMAIL'
            ];
        }

        //action proceeds if model does not implement MustVerifyEmail()
        // $credentials = $this->build([
        //     'username' => $args['email'],
        //     'password' => $args['password']
        // ]);

        // $user = $model::where('email', $args['email'])->first();
        // $response = $this->makeToken($credentials);

        // event(new Registered($user));

        // return [
        //     'tokens' => $response,
        //     'status' => 'SUCCESS'
        // ];
    }
}
