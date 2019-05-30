<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CacheService;
use App\Service\CustomerService;
use App\Service\PaginatedService;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/api.bilmo")
 */
class UserController extends AbstractFOSRestController
{
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @Rest\Get(path="/user")
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
     *     statusCode=200,
     *     serializerGroups={"list"}
     * )
     * @SWG\Get(
     *     description="Get list user",
     *     tags={"User"},
     *     @SWG\Response(
     *          response="200",
     *          description="Returns the user list attached to the client",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=User::class, groups={"create"}))
     *          )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required=true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token"
     *     ),
     *     @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="string",
     *          description="Field used to define the requested page"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="string",
     *          description="Field used to define result number per page"
     *     )
     * )
     * @Security("is_granted('ROLE_CUSTOMER') or is_granted('ROLE_ADMIN')")
     * @param UserRepository $repo
     * @param ParamFetcher $paramFetcher
     * @param PaginatedService $paginatedService
     * @param CacheService $cacheService
     * @return array
     */
    public function list(UserRepository $repo, ParamFetcher $paramFetcher,
                         PaginatedService $paginatedService, CacheService $cacheService): array
    {
        $cacheData = $cacheService->cache(
            'user_list',
            $repo->findBy(['customer' => $this->customerService->getUser()]),
            7380
        );

        $data = $paginatedService->pagination(
            $cacheData,
            $paramFetcher->get('offset'),
            $paramFetcher->get('limit')
        );

        return $data;
    }

    /**
     * @Rest\Get(
     *     path="/user/{id}",
     *     requirements={"id": "\d+"}
     * )
     * @Rest\View(
     *     statusCode=200,
     *     serializerGroups={"detail"}
     * )
     * @Security("(user.getId() == userApi.getCustomer().getId()) or is_granted('ROLE_ADMIN')")
     * @SWG\Get(
     *     description="Get one user",
     *     tags={"User"},
     *     @SWG\Response(
     *          response="200",
     *          description="Returns one user by id attached to the client",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=User::class, groups={"detail"}))
     *          )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Product object not found: Invalid ID supplied/Invalid Route",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required=true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token"
     *     )
     * )
     * @param User $userApi
     * @return User
     */
    public function show(User $userApi): User
    {
        return $userApi;
    }

    /**
     * @Rest\Post(path="/user")
     * @Rest\View(
     *     statusCode=201,
     *     serializerGroups={"create"}
     * )
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Security("is_granted('ROLE_CUSTOMER') or is_granted('ROLE_ADMIN')")
     * @SWG\Post(
     *     description="Create one user",
     *     tags={"User"},
     *     @SWG\Response(
     *          response="201",
     *          description="Returns the created user",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=User::class, groups={"detail"}))
     *          )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Product object not found: Invalid ID supplied/Invalid Route",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required=true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token"
     *     ),
     *      @SWG\Parameter(
     *          name="Body",
     *          required=true,
     *          in="body",
     *          type="string",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=User::class, groups={"create"}))
     *          ),
     *          description="*All properties required to add"
     *      )
     * )
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
     *     path="/user/{id}",
     *     requirements={"id": "\d+"}
     * )
     * @Rest\View(
     *     statusCode=200
     * )
     * @Security("(user.getId() == existingUser.getCustomer().getId()) or is_granted('ROLE_ADMIN')")
     * @SWG\Delete(
     *     description="Delete a user",
     *     tags={"User"},
     *     @SWG\Response(
     *          response="200",
     *          description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Product object not found: Invalid ID supplied/Invalid Route",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required=true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token"
     *     )
     * )
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
     *     path="/user/{id}",
     *     requirements={"id": "\d+"}
     * )
     * @Rest\View(
     *     statusCode=200,
     *     serializerGroups={"detail"}
     * )
     * @Security("(user.getId() == existingUser.getCustomer().getId()) or is_granted('ROLE_ADMIN')")
     * @SWG\Patch(
     *     description="Update user",
     *     tags={"User"},
     *     @SWG\Response(
     *          response="200",
     *          description="Returns the updated user",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=User::class, groups={"detail"}))
     *          )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Product object not found: Invalid ID supplied/Invalid Route",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required=true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token"
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          required=true,
     *          in="query",
     *     type="string",
     *     description="The unique identifier of a user"
     *      ),
     *      @SWG\Parameter(
     *          name="Body",
     *          required=true,
     *          in="body",
     *          type="string",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=User::class, groups={"update"}))
     *          ),
     *          description="At least one property required for the update"
     *      )
     * )
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
