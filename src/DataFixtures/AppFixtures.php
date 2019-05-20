<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $encoder;
    private $faker;
    private $customers;

    const CUSTOMER_NAME_ONE = "client1";
    const CUSTOMER_NAME_TOW = "client2";
    const CUSTOMER_NAME_THREE = "client3";

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
        $this->customers = [self::CUSTOMER_NAME_ONE, self::CUSTOMER_NAME_TOW, self::CUSTOMER_NAME_THREE];
    }

    public function load(ObjectManager $manager)
    {
        // Make Customers
        foreach ($this->customers as $key => $customer) {
            $newCustomer = $this->newCustomer($customer);
            $manager->persist($newCustomer);
            $this->customers[$key] = $newCustomer;
        }

        // Make Users
        for ($i = 0; $i <= 50; $i++) {
            $user = $this->newUser();
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function newCustomer(string $name)
    {
        $customer = new Customer;

        $customer
            ->setName($name)
            ->setEmail($name.'@'.$name.'.fr')
            ->setHash($this->encoder->encodePassword($customer, 'password'))
            ->setPhoneNumber('0'.mt_rand(100000000,999999999))
            ->setSiren(mt_rand(100000000,999999999));

        return $customer;
    }

    public function newUser()
    {
        $user = new User;

        $user
            ->setFirstname($this->faker->firstName)
            ->setLastname($this->faker->lastName)
            ->setHash($this->encoder->encodePassword($user, 'password'))
            ->setEmail($user->getFirstname() .'-'. $user->getLastname() . '@gmail.com')
            ->setCustomer($this->faker->randomElement($this->customers));

        return $user;
    }
}
