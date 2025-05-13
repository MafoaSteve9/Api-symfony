<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductService
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function getAllProducts(): array
    {
        return $this->em->getRepository(Product::class)->findAll();
    }

    public function createProduct(string $jsonData): Product|array
    {
        $product = $this->serializer->deserialize($jsonData, Product::class, 'json', ['groups' => 'product:write']);
        $errors = $this->validator->validate($product);

    if (count($errors) > 0) {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return ['errors' => $errorMessages];
    }

    $this->em->persist($product);
    $this->em->flush();

    return $product;
    }


    public function updateProduct(int $id, string $jsonData): array
    {
    $product = $this->em->getRepository(Product::class)->find($id);

    if (!$product) {
        return [
            'errors' => ['Produit non trouvé'],
            'status' => 404
        ];
    }

    try {
        $this->serializer->deserialize(
            $jsonData,
            Product::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $product,
                'groups' => 'product:write'
            ]
        );
    } catch (\Throwable $e) {
        return [
            'errors' => ['Erreur lors de la désérialisation : ' . $e->getMessage()],
            'status' => 400
        ];
    }

    $errors = $this->validator->validate($product);

    if (count($errors) > 0) {
        $errorMessages = array_map(fn($error) => $error->getMessage(), iterator_to_array($errors));
        return [
            'errors' => $errorMessages,
            'status' => 400
        ];
    }

    $this->em->flush();

    $json = $this->serializer->serialize($product, 'json', ['groups' => 'product:read']);

    return [
        'data' => $json,
        'status' => 200
    ];
    }


    public function deleteProduct(int $id): array
    {
        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            return ['errors' => ['Produit non trouvé'], 'status' => 404];
        }

        $this->em->remove($product);
        $this->em->flush();

        return ['data' => null, 'status' => 204];
    }
}