<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        return $this->json([
            'data' => $productRepository->findAll(),
        ]);
    }

    #[Route('/products/{product}', name: 'product_single', methods: ['GET'])]
    public function single(int $product, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($product);

        if (!$product) throw $this->createNotFoundException();

        return $this->json([
            'data' => $product
        ]);
    }

    #[Route('/products', name: 'products_create', methods: ['POST'])]
    public function create(Request $request, ProductRepository $productRepository): JsonResponse
    {
        if ($request->headers->get('Content-Type') == 'application/json') {
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        $product = new Product();
        $product->setName($data['name']);
        $product->setQuantity($data['quantity']);
        $product->setBuyValue($data['buy_value']);
        $product->setSaleValue($data['sale_value']);
        $product->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $product->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $productRepository->save($product, true);

        return $this->json([
            'message' => 'Product created!!',
            'data' => $product
        ], 201);
    }

    #[Route('/products/{product}', name: 'products_update', methods: ['PUT', 'PATCH'])]
    public function update(int $product, Request $request, ManagerRegistry $doctrine, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($product);

        if (!$product) throw $this->createNotFoundException();

        $data = $request->request->all();

        $product->setName($data['name']);
        $product->setQuantity($data['quantity']);
        $product->setBuyValue($data['buy_value']);
        $product->setSaleValue($data['sale_value']);
        $product->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $doctrine->getManager()->flush();

        return $this->json([
            'message' => 'Product updated!!',
            'data' => $product
        ], 201);
    }

    #[Route('/products/{product}', name: 'products_delete', methods: ['DELETE'])]
    public function delete(int $product, Request $request, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($product);

        $productRepository->remove($product, true);

        return $this->json([
            'message' => 'Product deleted!!',
            'data' => $product
        ], 201);
    }
}
