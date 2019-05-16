<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CustomerService;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
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
     */
    public function list(UserRepository $repo, SerializerInterface $serializer)
    {
        $users = $repo->findAll();

        $data = $serializer->serialize($users, 'json', SerializationContext::create()->setGroups('detail'));

        $response = new Response($data, Response::HTTP_OK);
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }

    /**
     * @Route(path="/api/user/{id}", methods={"GET"})
     * @param User $user
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(User $user, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($user, 'json');

        $response = new Response($data, Response::HTTP_OK);
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }

    /**
     * @Route(path="/api/user/create", methods={"POST"})
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
     * @param Request $request
     * @param ObjectManager $manager
     * @param SerializerInterface $serializer
     */
    public function delete(User $user, ObjectManager $manager)
    {
        $manager->remove($user);
        $manager->flush();

        return new Response('', Response::HTTP_OK);
    }
}
