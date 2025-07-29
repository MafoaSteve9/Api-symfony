<?php

namespace App\Service;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductService
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, ProductRepository $productRepository)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }
      public function getProductByName($name): ?Product
    {
        return $this->em->getRepository(Product::class)->findOneBy(['name' => $name]);
    }

    public function createProduct(string $jsonData): Product|array
    {
        $dto = $this->serializer->deserialize($jsonData, ProductDTO::class, 'json', ['disable_type_enforcement' => true], ['groups' => 'product:write']);
        $errors = $this->validator->validate($dto);

    if (count($errors) > 0) {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return ['errors' => $errorMessages];
    }

        $product = new Product();
        $product->setName($dto->name);
        $product->setPrice((float) $dto->price);    
        $product->setPhoto($dto->photo);
        $product->setCategory($dto->category);
        $product->setStock($dto->stock);

        
    // --- Gestion de la photo ---
    if ($dto->photo) {
        // Décoder base64 (format data:image/png;base64,...)
        if (preg_match('/^data:image\/(\w+);base64,/', $dto->photo, $type)) {
            $data = substr($dto->photo, strpos($dto->photo, ',') + 1);
            $data = base64_decode($data);

            if ($data === false) {
                return ['errors' => ['Image invalide'], 'status' => 400];
            }

            $extension = $type[1]; // png, jpeg, gif, ...
            $fileName = uniqid() . '.' . $extension;
            $filePath = __DIR__ . '/../../public/uploads/' . $fileName;

            // Sauvegarde sur le disque
            file_put_contents($filePath, $data);

            // Enregistre chemin relatif dans la base
            $product->setPhoto('/uploads/' . $fileName);
        } else {
            return ['errors' => ['Format d\'image non supporté'], 'status' => 400];
        }
    } else {
        $product->setPhoto(null);
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