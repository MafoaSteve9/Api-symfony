<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class ProductService {

    private EntityManagerInterface $em;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer){
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public function getAllProductsAsJson(): string {
        $products = $this->em->getRepository(Product::class)->findAll();
        return $this->serializer->serialize($products, 'json', ['groups' => ['product:read']]);
}

}