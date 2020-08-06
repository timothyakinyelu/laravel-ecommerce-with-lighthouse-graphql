<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Carbon\Carbon;

use App\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function register_guest_with_email_verification(): void
    {

        Notification::fake();
        Event::fake([Registered::class]);

        $this->createClient();

        $res = $this->graphQL(
            '
                mutation Register($register: RegisterInput!) {
                    register(register: $register) {
                        tokens {
                            access_token
                        }
                        status
                    }
                }
            ',
            [
                'register' => [
                    "first_name" => "Juniper",
                    "last_name" => "Lee",
                    "email" => "tolujamal1@gmail.com",
                    "password" => "secret",
                    "password_confirmation" => "secret"
                ]
            ]
        );

        $user = json_decode($res->getContent(), true);
        
        $this->assertArrayHasKey('register', $user['data']);
        $this->assertArrayHasKey('status', $user['data']['register']);
        $this->assertEquals('VERIFY_EMAIL', $user['data']['register']['status']);

        $user = User::first();
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

        Event::assertDispatched(Registered::class);
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function email_verified_with_token(): void
    {
        Notification::fake();
        Event::fake([\Illuminate\Auth\Events\Verified::class]);

        $this->createClient();

        $user = factory(User::class)->create([
            'first_name' => 'Juniper',
            'last_name' => 'Lee',
            'password' => Hash::make('secret')
        ]);

        $payload = base64_encode(json_encode([
            'id' => encrypt($user->id),
            'hashed_email' => encrypt($user->getEmailForVerification()),
            'expires_in' => encrypt(Carbon::now()->addMinutes(10)->toIso8601String())
        ]));

        $res = $this->graphQL('
            mutation VerifyEmail($data: VerifyEmailInput!) {
                verifyEmail(data: $data) {
                    tokens {
                        access_token
                        expires_in
                    }
                    status
                }
            }',
            [
                'data' => [
                    'token' => $payload
                ]
            ]
        );

        $response = json_decode($res->getContent(), true);

        $this->assertArrayHasKey('verifyEmail', $response['data']);
        $this->assertArrayHasKey('tokens', $response['data']['verifyEmail']);
        $this->assertArrayHasKey('access_token', $response['data']['verifyEmail']['tokens']);
        $this->assertEquals('SUCCESS', $response['data']['verifyEmail']['status']);

        $verifiedUser = User::find($user->id);
        $this->assertNotNull($verifiedUser->email_verified_at);
        Event::assertDispatched(\Illuminate\Auth\Events\Verified::class);
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function validate_verification_token(): void
    {
        Notification::fake();
        Event::fake([\Illuminate\Auth\Events\Verified::class]);

        $this->createClient();

        $user = factory(User::class)->create([
            'first_name' => 'Juniper',
            'last_name' => 'Lee',
            'password' => Hash::make('secret')
        ]);

        $payload = base64_encode(json_encode([
            'id' => encrypt($user->id),
            'hashed_email' => encrypt($user->getEmailForVerification()),
            'expires_in' => encrypt(Carbon::now()->subMinutes(10)->toIso8601String())
        ]));

        $res = $this->graphQL(
            '
                mutation VerifyEmail($data: VerifyEmailInput!) {
                    verifyEmail(data: $data) {
                        tokens {
                            access_token
                        }
                        status
                    }
                }
            ', 
            [
                'data' => [
                    'token' => $payload
                ]
            ]
        );

       $response = json_decode($res->getContent(), true);
       $this->assertArrayHasKey('errors', $response);

       $verifiedUser = User::find($user->id);
       $this->assertNull($verifiedUser->email_verified_at);
       Event::assertNotDispatched(\Illuminate\Auth\Events\Verified::class);
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function user_can_login(): void
    {
        $user = factory(User::class)->create(['email' => 'lee.juniper@xo.com', 'password' => Hash::make('secret')]);

        $this->createClient();

        $res = $this->graphQL(
            '
                mutation Login($login: LoginInput!) {
                    login(login: $login) {
                        tokens {
                            access_token
                            expires_in
                        }
                        user {
                            id
                            first_name
                            email
                        }
                    }
                }
            ', 
            [
                "login" => [
                    "email" => "lee.juniper@xo.com",
                    "password" => "secret"
                ]
            ]
        );

        $login = json_decode($res->getContent(), true);
        $this->assertArrayHasKey('login', $login['data']);
        $this->assertArrayHasKey('tokens', $login['data']['login']);
        $this->assertNotNull($login['data']['login']['tokens']['access_token']);
        $this->assertNotNull($login['data']['login']['tokens']['expires_in']);

        $this->assertArrayHasKey('user', $login['data']['login']);
        $this->assertNotNull($login['data']['login']['user']['first_name']);
        $this->assertNotNull($login['data']['login']['user']['email']);
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function login_auth_fail(): void
    {
        $user = factory(User::class)->create(['email' => 'lee.juniper@xo.com', 'password' => Hash::make('secret')]);

        $this->createClient();

        $res = $this->graphQL(
            '
                mutation Login($login: LoginInput!) {
                    login(login: $login) {
                        tokens {
                            access_token
                            expires_in
                        }
                        user {
                            id
                            first_name
                            email
                        }
                    }
                }
            ', 
            [
                "login" => [
                    "email" => "lee.juniper@xo.com",
                    "password" => "password"
                ]
            ]
        );

        $login = json_decode($res->getContent(), true);
        $this->assertarrayHasKey('errors', $login);
    }
}
