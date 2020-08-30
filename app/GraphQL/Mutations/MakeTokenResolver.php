<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;

class MakeTokenResolver
{
    const REFRESH_TOKEN = 'refreshToken';
    private $request;

    public function __construct(Application $app)
    {
        $this->request = $app->make('request');
    }

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
     * and attaches the refresh_token
     * to an httpOnly cookie
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
            time() + 60 * 60 * 24 * 10, // 10 days
            '/',
            $this->request->getHost(),
            false,
            true // HttpOnly
        );

        return [
            'access_token' => $decoded['access_token'],
            'expires_in' => $decoded['expires_in']
        ];
    }

    /**
     * attemmpts to get the refresh_token
    */
    public function attemptRefresh($context)
    {
        $jwt = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $this->request->header('authorization')));
        $decodedJWT = (new \Lcobucci\JWT\Parser())->parse($jwt);

        $_token = $decodedJWT->getClaim('jti');
        $expires_at = $decodedJWT->getClaim('exp');

        $access_token = DB::table('oauth_access_tokens')
        ->select('user_id')
        ->where('id', $_token)
        ->first();

        $userID = $access_token->user_id;

        $refreshToken = $context->request->cookie(self::REFRESH_TOKEN);

        // Check if refresh_token has expired
        $data = DB::table('oauth_refresh_tokens')
        ->select('id', 'expires_at')
        ->where('access_token_id', $_token)
        ->where('revoked', 0)
        ->first();

        $time_of_expiry = Carbon::parse($data->expires_at)->timestamp;

        if($time_of_expiry > Carbon::now()->timestamp) {
            $credentials = $this->build([
                'refresh_token' => $refreshToken
            ], 'refresh_token',);

            return $this->makeToken($credentials);
        }
        throw new AuthenticationException('Authentication failed');
    }
}
