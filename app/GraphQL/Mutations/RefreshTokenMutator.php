<?php

namespace App\GraphQL\Mutations;

class RefreshTokenMutator extends MakeTokenResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args, GraphQLContext $context = null)
    {
        $response = $this->attemptRefresh($context);
        return $response;
    }
}
