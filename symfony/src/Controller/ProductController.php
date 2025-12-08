<?php

namespace App\Controller;

use App\Service\Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function list(Database $db): JsonResponse
    {
        $products = $db->fetchAll('SELECT id, name, price, stock, description, image FROM products');

        return $this->json($products);
    }

    #[Route('/api/product/{id}', name: 'api_product_show', methods: ['GET'])]
    public function show(int $id, Database $db): JsonResponse
    {
        $product = $db->fetchOne(
            'SELECT id, name, price, stock, description, image FROM products WHERE id=:id',
            [
                ':id' => $id
            ]
        );
        if ($product === null) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        return $this->json($product);
    }
}
