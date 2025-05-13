<?php

namespace App\Controller;


use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


final class ProductController extends AbstractController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/api/products', name: 'app_product_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }

    #[Route('/api/products', name: 'app_product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $result = $this->productService->createProduct($request->getContent());

        if (is_array($result) && isset($result['errors'])) {
        return $this->json(['errors' => $result['errors']], 400);
    }

        return $this->json($result, 201, [], ['groups' => 'product:read']);
    }


    #[Route('/api/products/{id}', name: 'app_product_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
    $result = $this->productService->updateProduct($id, $request->getContent());

    if (isset($result['errors'])) {
        return new JsonResponse(['errors' => $result['errors']], $result['status']);
    }

    return new JsonResponse($result['data'], $result['status'], [], true);
    }


    #[Route('/api/products/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $result = $this->productService->deleteProduct($id);
        return new JsonResponse($result['data'], $result['status']);
    }
}