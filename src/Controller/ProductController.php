<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CacheService;
use App\Service\PaginatedService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;

/**
 * Class ProductController
 * @package App\Controller
 * @Route("api.bilmo")
 */
class ProductController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/product")
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
     *     description="Get list product",
     *     tags={"Product"},
     *     @SWG\Response(
     *          response="200",
     *          description="Returns the list product",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Product::class, groups={"list"}))
     *          )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request: Method Not Allowed",
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
     *          name="offset",
     *          in="query",
     *          type="string",
     *          description="Field used to define the requested page"
     *     ),
     *     @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="string",
     *          description="Field used to define result number per page"
     *     )
     * )
     * @Security("is_granted('ROLE_CUSTOMER') or is_granted('ROLE_ADMIN')")
     * @param ProductRepository $repo
     * @param ParamFetcher $paramFetcher
     * @param PaginatedService $paginatedService
     * @param CacheService $cacheService
     * @return array
     */
    public function list(ProductRepository $repo, ParamFetcher $paramFetcher,
                         PaginatedService $paginatedService, CacheService $cacheService): array
    {
        $cacheData = $cacheService->cache(
            'product_list',
            $repo->findAll()
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
     *     path="/product/{id}",
     *     requirements={"id": "\d+"})
     * @Rest\View(
     *     statusCode=200,
     *     serializerGroups={"detail"}
     * )
     * @SWG\Get(
     *     description="Get one product",
     *     tags={"Product"},
     *     @SWG\Response(
     *          response="200",
     *          description="Returns one product by id",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Product::class, groups={"detail"}))
     *          )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request: Method Not Allowed",
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
     * @Security("is_granted('ROLE_CUSTOMER') or is_granted('ROLE_ADMIN')")
     * @return Product
     */
    public function show(Product $product): Product
    {
        return $product;
    }
}
