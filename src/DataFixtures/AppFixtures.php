<?php

namespace App\DataFixtures;

use App\Entity\Address;
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

        $customer
            ->setName('orange')
            ->setEmail('orange@orange.fr')
            ->setHash($this->encoder->encodePassword($customer, 'password'))
            ->setPhoneNumber('0112121212')
            ->setSiren('123456789');

        $manager->persist($customer);

        for ($i = 0; $i <= 10; $i++) {
            $user = new User();
            $user
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setHash($this->encoder->encodePassword($user, 'password'))
                ->setEmail($user->getFirstname() .'-'. $user->getLastname() . '@gmail.com')
                ->setCustomer($customer);


            $address = new Address();
            $address
                ->setWay($faker->address)
                ->setNumberWay(mt_rand(1, 500))
                ->setCity($faker->city)
                ->setPostalCode($faker->postcode)
                ->setCountry($faker->country)
                ->setPhoneNumber($faker->phoneNumber)
                ->setByDefault(1)
                ->setUser($user);

            $manager->persist($address);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
