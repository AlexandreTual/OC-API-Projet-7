<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CustomerService;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
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
    public function list(UserRepository $repo)
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
     */
    public function show(User $userApi)
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
    public function create(User $user, ObjectManager $manager)
    {
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
     * @param SerializerInterface $serializer
     */
    public function update(User $existingUser, Request $request, ObjectManager $manager)
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
        $manager->flush();

        return $existingUser;
    }
}
