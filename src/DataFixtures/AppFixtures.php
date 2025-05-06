<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product1 = new Product();
        $product1->setName('Chaussures de sport');
        $product1->setPrice(59.99);
        $product1->setCategory('Sport');
        $product1->setStock(true);

        $product2 = new Product();
        $product2->setName('Montre connectée');
        $product2->setPrice(199.99);
        $product2->setCategory('Technologie');
        $product2->setStock(false);

        $product3 = new Product();
        $product3->setName('Sac à dos');
        $product3->setPrice(29.99);
        $product3->setCategory('Mode');
        $product3->setStock(true);

        $product4 = new Product();
        $product4->setName('T-shirt en coton');
        $product4->setPrice(19.99);
        $product4->setCategory('Mode');
        $product4->setStock(true);

        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);
        $manager->flush();
    }
}
