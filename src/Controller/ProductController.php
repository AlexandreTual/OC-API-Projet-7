<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/api/product")
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
     * @Security("is_granted('ROLE_CUSTOMER') or is_granted('ROLE_ADMIN')")
     * @return array
     */
    public function list(ProductRepository $repository, Request $request, PaginatorInterface $paginator, ParamFetcher $paramFetcher): array
    {
        $products = $repository->findAll();

        $productsPaginated = $paginator->paginate($products, $request->query->getInt('page', $paramFetcher->get('offset')), $paramFetcher->get('limit'));

        $results = [
            'data' => $productsPaginated->getItems(),
            'meta' => $productsPaginated->getPaginationData(),
        ];

        return $results;
    }

    /**
     * @Rest\Get(
     *     path="/api/product/{id}",
     *     requirements={"id": "\d+"})
     * @Rest\View(
     *     statusCode=200,
     *     serializerGroups={"detail"}
     * )
     * @Security("is_granted('ROLE_CUSTOMER') or is_granted('ROLE_ADMIN')")
     * @return Product
     */
    public function show(Product $product): Product
    {
        return $product;
    }
}
