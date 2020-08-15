<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\PassportServiceProvider;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

use App\User;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function loggedin_user_can_create_user(): void
    {

        $loggedInUser = $this->loginWithPermission('create-users');
        Passport::actingAs($loggedInUser);

        if($loggedInUser->can('create-users')) {
            $user = $this->create_user();
    
            $email = $user->json("data.createUser.email");
            $dob = $user->json("data.createUser.dob");
    
            $this->assertDatabaseHas('users', [
                'email' => $email,
                'dob' => $dob
            ]);
    
            $user->assertStatus(200);
        }

        $user = $this->loginWithPermission('create-products');

        $res = $user->can('create-users');
        $this->assertFalse($res);
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function loggedin_user_can_view_users(): void 
    {
        $loggedInUser = $this->loginWithPermission('view-users');
        Passport::actingAs($loggedInUser);

        if($loggedInUser->can('view-users')) {
            $res = $this->graphQL(
                '{
                    users {
                        id
                        first_name
                        last_name   
                    }
                }'
            );

            $id = $res->json("data.users.*.id");
            $first_name = $res->json("data.users.*.first_name");
            $last_name = $res->json("data.users.*.last_name");

            $res->assertJsonFragment([
                'id' => $id[0],
                'first_name' => $first_name[0],
                'last_name' => $last_name[0]
            ]);
        }
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function loggedin_user_cannot_view_users(): void
    {
        $loggedInUser = $this->loginWithPermission('view-products');
        $res = $loggedInUser->can('view-users');
        $this->assertFalse($res);
    }

    private function create_user()
    {
        $user = $this->graphQL(
            '
            mutation CreateUser($user: UserInput!) {
                createUser(user: $user) {
                    id
                    first_name
                    last_name
                    email
                    dob
                }
            }
            ', [
                "user" => [
                    'first_name' => 'Juniper',
                    'last_name' => 'Lee',
                    'email' => 'lee.juniper@xo.com',
                    'dob' => '1980-08-07',
                    'gender' => 'Female',
                    'is_active' => false
                ]
            ]
        );

        return $user;
    }
}
