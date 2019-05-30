<?php

namespace App\Service;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param ValidatorInterface $validator
     * @param $existingUser
     * @return View
     */
    public function validation($existingUser)
    {
        $errors = $this->validator->validate($existingUser);
        if (count($errors)) {
            return View::create($errors, Response::HTTP_BAD_REQUEST);
        }
    }

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