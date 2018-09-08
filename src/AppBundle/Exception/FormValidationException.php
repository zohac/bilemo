<?php

namespace AppBundle\Exception;

use Symfony\Component\Form\FormInterface;

class FormValidationException extends \Exception
{
    /**
     * @var FormInterface
     */
    private $form;

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
     * @param FormInterface $form
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * Get Messages.
     *
     * @return array
     */
    public function getMessages(): array
    {
        $messages = [];
        $messages[] = $this->getErrorsAsArray($this->form);

        return $messages;
    }

    /**
     * Get error of the form.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    public function getErrorsAsArray(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $key => $child) {
            if ($err = $this->getErrorsAsArray($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
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
