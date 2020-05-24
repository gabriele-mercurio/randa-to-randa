<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationResponse extends JsonResponse {
    public function __construct(ConstraintViolationListInterface $errors, int $status = 400, array $headers = [], bool $json = false)
    {
        $errs = [];
        foreach($errors as $error) {
            $errs[$error->getPropertyPath()] = $error->getMessage();
        }

        parent::__construct($errs, $status, $headers, $json);
    }
}