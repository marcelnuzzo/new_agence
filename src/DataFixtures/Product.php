<?php

namespace App\DataFixtures;

//use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Product extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /*
        $product = new Product();
        
        
        $manager->persist($product);

        $manager->flush();
        */
    }
}
