<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CustomerService;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractFOSRestController
{
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @Rest\Get(path="/api/user")
     * @Rest\View(
     *     serializerGroups={"list"}
     * )
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function list(UserRepository $repo): array
    {
        $users = $repo->findBy(['customer' => $this->customerService->getUser()]);

        return $users;
    }

    /**
     * @Rest\Get(
     *     path="/api/user/{id}",
     *     requirements={"id": "\d+"}
     * )
     * @Rest\View(
     *     statusCode=200,
     *     serializerGroups={"detail"}
     * )
     * @Security("user.getId() == userApi.getCustomer().getId()")
     * @param User $user
     * @return User
     */
    public function show(User $userApi): User
    {
        return $userApi;
    }

    /**
     * @Rest\Post(
     *     path="/api/user/create"
     * )
     * @Rest\View(
     *     statusCode=201,
     *     serializerGroups={"create"}
     * )
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function create(User $user, ObjectManager $manager, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        $user->setCustomer($this->customerService->getUser());
        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    /**
     * @Rest\Delete(
     *     path="/api/user/delete/{id}",
     *     requirements={"id": "\d+"}
     * )
     * @Rest\View(
     *     statusCode=200
     * )
     * @Security("user.getId() == existingUser.getCustomer().getId()")
     */
    public function delete(User $existingUser, ObjectManager $manager)
    {
        $manager->remove($existingUser);
        $manager->flush();
    }

    /**
     * @Rest\Patch(
     *     path="/api/user/update/{id}",
     *     requirements={"id": "\d+"}
     * )
     * @Rest\View(
     *     statusCode=200,
     *     serializerGroups={"detail"}
     * )
     * @Security("user.getId() == existingUser.getCustomer().getId()")
     * @param Request $request
     * @param ObjectManager $manager
     * @return User
     */
    public function update(User $existingUser, Request $request, ObjectManager $manager, ValidatorInterface $validator): User
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
             }
         }

        $errors = $validator->validate($existingUser);
        if (count($errors)) {
            return $this->view($errors, Response::HTTP_BAD_REQUEST);
        }

        $manager->flush();

        return $existingUser;
    }
}
