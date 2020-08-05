<?php

namespace App\Exceptions;

/**
 * Validation exception created because Nuwave\Lighthouse\Exceptions\ValidationException
 * doesn't return an array
 */

class ValidationException extends \Nuwave\Lighthouse\Exceptions\ValidationException
{
    /*
    ** @var
    */
    public $errors;

    /**
     * ValidationException constructor
     * @param $validator
     * @param string $message
     */
    public function __construct($errors, string $message = '')
    {
        parent::__construct($message);

        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function extensionsContent(): array
    {
        return ['errors' => $this->errors];
    }
}
