<?php

namespace App\Service;

use App\Entity\Customer;
use Doctrine\Common\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomerService
{
    private $manager;
    private $TokenExtractor;
    private $JwtEncoder;
    private $request;

    public function __construct(ObjectManager $manager, TokenExtractorInterface $extractor, JWTEncoderInterface $encoder, RequestStack $request)
    {
        $this->manager = $manager;
        $this->TokenExtractor = $extractor;
        $this->JwtEncoder = $encoder;
        $this->request = $request;
    }

    public function getUser()
    {
        $token = $this->TokenExtractor->extract($this->request->getCurrentRequest());
        $payload = $this->JwtEncoder->decode($token);
        $customer = $this->manager
            ->getRepository(Customer::class)
            ->findOneBy([
                    'email' => $payload['username']
                ]
            );

        return $customer;
    }
}