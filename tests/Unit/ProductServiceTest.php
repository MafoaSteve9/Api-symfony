<?php

namespace App\Tests\Unit;

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
        // Arrange
        $jsonData = '{"name": "Produit test"}';

        $product = new Product();
        $product->setName("Produit test");

        /** @var SerializerSerializerInterface&\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this->createMock(SerializerSerializerInterface::class);
        $serializer->method('deserialize')->willReturn($product);

        /** @var ValidatorInterface&\PHPUnit\Framework\MockObject\MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        /** @var EntityManagerInterface&\PHPUnit\Framework\MockObject\MockObject $em */
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist')->with($product);
        $em->expects($this->once())->method('flush');

        /** @var ProductRepository&\PHPUnit\Framework\MockObject\MockObject $repository */
        $repository = $this->createMock(ProductRepository::class);

        $service = new ProductService($em, $serializer, $validator, $repository);

        // Act
        $result = $service->createProduct($jsonData);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals("Produit test", $result->getName());
    }
}
