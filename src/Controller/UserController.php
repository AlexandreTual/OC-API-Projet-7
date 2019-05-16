<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CustomerService;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @Route(path="/api/user", methods={"GET"})
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function list(UserRepository $repo, SerializerInterface $serializer)
    {
        $users = $repo->findBy(['customer' => $this->customerService->getUser()]);

        $data = $serializer->serialize($users, 'json', SerializationContext::create()->setGroups(['list']));

        $response = new Response($data, Response::HTTP_OK);
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }

    /**
     * @Route(path="/api/user/{id}", methods={"GET"})
     * @Security("user.getId() == john.getCustomer().getId()")
     * @param User $user
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(User $john, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($john, 'json', SerializationContext::create()->setGroups(['detail']));

        $response = new Response($data, Response::HTTP_OK);
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }

    /**
     * @Route(path="/api/user/create", methods={"POST"})
     * @IsGranted("ROLE_CUSTOMER")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ObjectManager $manager
     * @return Response
     */
    public function create(Request $request, SerializerInterface $serializer, CustomerService $customerService, ObjectManager $manager)
    {
        $data = $request->getContent();
                $user = $serializer->deserialize(
            $data,
            User::class,
            'json',
            DeserializationContext::create()->setGroups(['create']));

        $user->setCustomer($customerService->getUser());
        $manager->persist($user);
        $manager->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/api/user/delete/{id}", methods={"DELETE"})
     * @Security("user.getId() == john.getCustomer().getId()")
     * @param Request $request
     * @param ObjectManager $manager
     * @param SerializerInterface $serializer
     */
    public function delete(User $john, ObjectManager $manager)
    {
        $manager->remove($john);
        $manager->flush();

        return new Response('', Response::HTTP_OK);
    }
}
