<?php

namespace App\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;

/**
 * Class ValidationException.
 */
class ValidationException extends Exception implements ClientAware, ProvidesExtensions
{
    /**
     * @var array
     */
    public array $errors;

    /**
     * ValidationException constructor.
     *
     * @param array $errors
     * @param string $message
     */
    public function __construct(array $errors, string $message = '')
    {
        parent::__construct($message);

        $this->errors = $errors;
    }

    /**
     * The category.
     *
     * @var string
     */
    protected string $category = 'validation';

    /**
     * Returns true when exception message is safe to be displayed to a client.
     *
     * @api
     *
     * @return bool
     */
    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getExtensions(): array
    {
        return ['errors' => $this->errors];
    }
}
