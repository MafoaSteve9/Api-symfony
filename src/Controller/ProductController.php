<?php

namespace App\Controller;

use App\Service\CreateProductService;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


final class ProductController extends AbstractController
{
    #[Route('api/products', name: 'app_product_list', methods: ['GET'])]
    public function index(ProductService $productService): JsonResponse
    {   
        $json = $productService->getAllProductsAsJson();
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('api/products', name: 'app_product_create', methods: ['POST'])]
    public function  create(CreateProductService  $createProductService): JsonResponse
    {
        $json = $createProductService->createProduct();
        return new JsonResponse($json, 201, [], true);
    }


    
}
