<?php

namespace App\Exceptions;

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
