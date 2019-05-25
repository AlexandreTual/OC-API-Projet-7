<?php

namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginatedService
{
    private $paginator;
    private $request;

    public function __construct(RequestStack $request, PaginatorInterface $paginator)
    {
        $this->request = $request;
        $this->paginator = $paginator;
    }

    public function pagination($data, $offset, $limit)
    {
        $dataPaginated = $this->paginator->paginate(
            $data,
            $this->request->getCurrentRequest()->query->getInt('page', $offset),
            $limit
        );

        return [
            'data' => $dataPaginated->getItems(),
            'meta' => $dataPaginated->getPaginationData(),
        ];
    }
}
