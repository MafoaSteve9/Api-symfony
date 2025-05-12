<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;


class CreateProductService {
    private SerializerInterface $serializer;
    private Request $request;
    private ValidatorInterface $validator;
    private EntityManagerInterface $em;


    public function __construct(SerializerInterface $serializer, Request $request, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->request = $request;
        $this->validator = $validator;
        $this->em = $em;
    }

    public function createProduct() {
        
        $product = $this->serializer->deserialize($this->request->getContent(), Product::class, 'json', ['groups' => ['product:write']]);

        $errors = $this->validator->validate($product);
        $errorMessages = [];
        if(count($errors) > 0) {
             foreach($errors as $error){
                $errorMessages [] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }
        
        $this->em->persist($product);
        $this->em->flush();
    }
}