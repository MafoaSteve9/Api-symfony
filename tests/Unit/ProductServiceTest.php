<?php

namespace App\Tests\Unit;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductServiceTest extends WebTestCase
{
     public function testCreateProductReturnsProductIfValid()
    {
         $jsonData = json_encode([
        'name' => 'Produit random',
        'price' => 19.99,
        'category' => 'Test category',
        'stock' => true
    ]);

    // On veut que le serializer désérialise le JSON en DTO
    $dto = new ProductDTO();
    $dto->name = 'Produit random';
    $dto->price = 19.99;
    $dto->category = 'Test category';
    $dto->stock = true;

    /** @var SerializerSerializerInterface&\PHPUnit\Framework\MockObject\MockObject $serializer */
    $serializer = $this->createMock(SerializerSerializerInterface::class);
    $serializer->method('deserialize')->with($jsonData, ProductDTO::class, 'json')->willReturn($dto);

    /** @var ValidatorInterface&\PHPUnit\Framework\MockObject\MockObject $validator */
    $validator = $this->createMock(ValidatorInterface::class);
    $validator->method('validate')->willReturn(new ConstraintViolationList());

    /** @var EntityManagerInterface&\PHPUnit\Framework\MockObject\MockObject $em */
    $em = $this->createMock(EntityManagerInterface::class);
    // On attend une entité Product dans persist
    $em->expects($this->once())->method('persist')->with($this->isInstanceOf(Product::class));
    $em->expects($this->once())->method('flush');

    /** @var ProductRepository&\PHPUnit\Framework\MockObject\MockObject $repository */
    $repository = $this->createMock(ProductRepository::class);

    $service = new ProductService($em, $serializer, $validator, $repository);

    $result = $service->createProduct($jsonData);

    $this->assertInstanceOf(Product::class, $result);
    $this->assertEquals('Produit random', $result->getName());
    }
}
