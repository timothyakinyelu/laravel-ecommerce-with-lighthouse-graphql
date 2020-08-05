<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Cookie;

class MakeTokenResolver
{
    const REFRESH_TOKEN = 'refreshToken';

    /**
     * @param  array<string, mixed>  $args
     * @param  string  $grantType
     * 
     * This function combines the necessary data required to generate
     * a password grant token
    */
    public function build(array $args = [], $grantType = 'password')
    {
        $data = collect($args);
        $credentials = $data->except('directive')->toArray();

        $credentials['grant_type'] = $grantType;
        $credentials['client_id'] = $data->get('client_id', config('graph-passport-auth.client_id'));
        $credentials['client_secret'] = $data->get('client_secret', config('graph-passport-auth.client_secret'));

        return $credentials;
    }

    /**
     * @param  array<string, mixed>  $args
     * @param  string  $grantType
     * @throws AuthenticationEception
     * 
     * Function creates a password grant token
     * for first-party users
    */
    public function makeToken(array $credentials)
    {
        $request = Request::create('oauth/token', 'POST', $credentials, [], [], [
            'HTTP_Accept' => 'application/json'
        ]);
        
        $response = app()->handle($request);
        $decoded = json_decode($response->getContent(), true);

        if($response->getStatusCode() != 200) {
            throw new AuthenticationException($decoded['message']);
        }

        // Create a refresh token cookie
        Cookie::queue(
            self::REFRESH_TOKEN,
            $decoded['refresh_token'],
            864000, // 10 days
            null,
            null,
            false,
            true // HttpOnly
        );

        return $decoded;
    }

    /**
     * attemmpts to get the refresh_token
    */
    public function attemptRefresh($context)
    {
        $refreshToken = $context->request->cookie(self::REFRESH_TOKEN);

        $credentials = $this->build([
            'refresh_token' => $refreshToken
        ], 'refresh_token',);

        return $this->makeToken($credentials);
    }
}
