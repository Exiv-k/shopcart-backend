<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function list(Database $db): JsonResponse
    {
        $products = $db->fetchAll('SELECT id, name, price, stock, description, image FROM products
        WHERE stock > 0');

        return $this->json($products);
    }

    #[Route('/api/product/{id}', name: 'api_product_show', methods: ['GET'])]
    public function show(int $id, Database $db): JsonResponse
    {
        $product = $db->fetchOne(
            'SELECT id, name, price, stock, description, image FROM products WHERE id=:id AND stock > 0',
            [
                ':id' => $id
            ]
        );
        if ($product === null) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        return $this->json($product);
    }

    #[Route('/api/product/{id}', name: 'api_product_delete', methods: ['DELETE'])]
    public function deleteProduct(int $id, Database $db): JsonResponse
    {
        $user = $this->getUser();
        if (!in_array('ADMIN', $user->getRoles())) {
            return $this->json(['message' => 'Forbidden action'], 403);
        }

        $db->execute(
            'UPDATE products 
            SET stock = 0 WHERE id=:id',
            [
                ':id' => $id
            ]
        );
        return $this->json(['message' => 'Product deleted'], 200);
    }
}
