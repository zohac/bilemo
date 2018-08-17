<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationList;

class ResourceValidationException extends \Exception
{
    /**
     * @var ConstraintViolationList
     */
    private $violations;

    /**
     * The default status code.
     * 422: Unprocessable entity.
     *
     * @var int
     */
    private $statusCode = 422;

    /**
     * Constructor.
     *
     * @param ConstraintViolationList $violations
     */
    public function __construct(ConstraintViolationList $violations)
    {
        $this->violations = $violations;
    }

    /**
     * Get Messages.
     *
     * @return array
     */
    public function getMessages(): array
    {
        $messages = [];
        foreach ($this->violations as $violation) {
            $messages[] = sprintf('Field %s: %s', $violation->getPropertyPath(), $violation->getMessage());
        }

        return $messages;
    }

    /**
     * Get the value of statusCode.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
