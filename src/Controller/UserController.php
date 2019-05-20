<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Representation\Representation;
use App\Service\CustomerService;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="10",
     *     description="Max user per page"
     * )
     * @Rest\View(
     *     serializerGroups={"list"}
     * )
     * @IsGranted("ROLE_CUSTOMER")
     * @param UserRepository $repo
     */
    public function list(UserRepository $repo, PaginatorInterface $paginator, Request $request, ParamFetcher $paramFetcher)
    {
        $users = $repo->findBy(['customer' => $this->customerService->getUser()]);

        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt(
                'page',
                $paramFetcher->get('offset')
                ),
            $paramFetcher->get('limit')
        );

        $result = array(
            'data' => $pagination->getItems(),
            'meta' => $pagination->getPaginationData());

        return $result;
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
     * @param User $userApi
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
     * @param User $user
     * @param ObjectManager $manager
     * @param ConstraintViolationList $violations
     * @return mixed
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
     * @param User $existingUser
     * @param ObjectManager $manager
     * @return void
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
     * @param User $existingUser
     * @param Request $request
     * @param ObjectManager $manager
     * @param ValidatorInterface $validator
     * @return mixed
     */
    public function update(User $existingUser, Request $request, ObjectManager $manager, ValidatorInterface $validator)
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
