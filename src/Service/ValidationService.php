<?php

namespace App\Service;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationService
{
    /**
     * @param ConstraintViolationList $violations
     * @return View
     */
    public function violation(ConstraintViolationList $violations)
    {
        if (count($violations)) {
            return View::create($violations,  Response::HTTP_BAD_REQUEST);
        }
    }
}