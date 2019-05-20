<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;


class ProductFixtures extends Fixture
{
    private $brands;
    private $cellTech;
    private $memoryStorage;

    public function __construct()
    {
        $this->brands = ['Samsung', 'Archos', 'Asus', 'Honor', 'HTC', 'Huawei'];
        $this->cellTech = ['3G', '3G+', '4G', '4G+', '5G'];
        $this->memoryStorage = ['16', '32', '64', '128'];
    }

    public function load(ObjectManager $manager)
    {
        for ($i=0; $i<=60; $i++) {
            $product = $this->newProduct();
            $manager->persist($product);
        }

        $manager->flush();
    }

    public function newProduct()
    {
        $faker = Factory::create();

        $product = new Product;

        $product
            ->setName($faker->firstNameFemale)
            ->setBrand($faker->randomElement($this->brands))
            ->setCellTechnology($faker->randomElement($this->cellTech))
            ->setDescription($faker->text)
            ->setWeight(mt_rand(150, 200).'g')
            ->setMemoryStorage($faker->randomElement($this->memoryStorage).'GB')
            ->setOperatingSystem('Android')
            ->setDimensions(mt_rand(70,80).' x '.mt_rand(140,160));

        return $product;
    }
}
