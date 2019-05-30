<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;

class UserService
{

    public function updateField($request, $existingUser)
    {
        $array = json_decode($request->getContent(), true);
        // sette the user object dynamically
        foreach ($array as $key => $value) {
            $method = 'set'.$key;
            if (preg_match('/_/', $key)) {
                $arrayExp = explode('_', $key);
                foreach ($arrayExp as $entry => $val) {
                    $arrayExpUcF[$entry] = ucfirst($val);
                }

                $method = 'set' . implode($arrayExpUcF);
            }
            if (method_exists($existingUser, $method)) {
                $existingUser->$method($array[$key]);
            } else {
                $view = View::create();
                $view
                    ->setResponse(Response::create($key. ' does not exist', 400))
                    ->setStatusCode(400);

                return $view;
            }
        }

        return $existingUser;
    }
}