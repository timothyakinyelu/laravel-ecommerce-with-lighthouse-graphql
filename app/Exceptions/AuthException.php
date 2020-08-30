<?php

namespace App\Exceptions;

use Exception;
use Closure;
use GraphQL\Error\Error;
use Nuwave\Lighthouse\Execution\ErrorHandler;

class AuthException implements ErrorHandler
{
    public static function handle(Error $error, Closure $next): array {
        $response = $next($error);
    
        if($response['message'] === 'Unauthenticated.') {
            $response['extensions']['code'] = 401;

            return $response;
        }

        return $next($error);
    }
}