<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\Bridge\RefreshTokenRepository;
// use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use App\CustomGrants\VerifyEmailGrant;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        app(AuthorizationServer::class)->enableGrantType(
            $this->makeVerifyEmailGrant(), Passport::tokensExpireIn()
        );


        //
        Passport::routes();

        Passport::personalAccessClientId(
            config('passport.personal_access_client.id')
        );
    
        Passport::personalAccessClientSecret(
            config('passport.personal_access_client.secret')
        );

        // Passport::tokensExpireIn(Carbon::now()->addMinutes(10));
        // Passport::refreshTokensExpireIn(Carbon::now()->addDays(10));
    }

    protected function makeVerifyEmailGrant()
    {
        $grant = new VerifyEmailGrant(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
