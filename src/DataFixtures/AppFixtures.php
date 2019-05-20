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
    public $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $customer = new Customer();
        $customers = [];
        $customer
            ->setName('orange')
            ->setEmail('orange@orange.fr')
            ->setHash($this->encoder->encodePassword($customer, 'password'))
            ->setPhoneNumber('0112121212')
            ->setSiren('123456789');

        $manager->persist($customer);
        $customers[] = $customer;

        $customer2 = new Customer();
        $customer2
            ->setName('levyathan')
            ->setEmail('levyathan@gmail.com')
            ->setHash($this->encoder->encodePassword($customer, 'password'))
            ->setPhoneNumber('0112121212')
            ->setSiren('123457845');

        $manager->persist($customer2);
        $customers[] = $customer2;

        for ($i = 0; $i <= 30; $i++) {
            $user = new User();
            $user
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setHash($this->encoder->encodePassword($user, 'password'))
                ->setEmail($user->getFirstname() .'-'. $user->getLastname() . '@gmail.com')
                ->setCustomer($faker->randomElement($customers));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
