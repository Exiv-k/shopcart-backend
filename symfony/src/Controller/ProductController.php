<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function list(Database $db): JsonResponse
    {
        $products = $db->fetchAll('SELECT id, name, price, stock, description, image FROM products
        WHERE active=TRUE');

        return $this->json($products);
    }

    #[Route('/api/product/{id}', name: 'api_product_show', methods: ['GET'])]
    public function show(int $id, Database $db): JsonResponse
    {
        $product = $db->fetchOne(
            'SELECT id, name, price, stock, description, image FROM products WHERE id=:id AND active=TRUE',
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
            SET active=FALSE WHERE id=:id',
            [
                ':id' => $id
            ]
        );
        return $this->json(['message' => 'Product deleted'], 200);
    }

    #[Route('/api/product/{id}', name: 'api_products_edit', methods: ['PUT'])]
    public function editProduct(int $id, Request $request, Database $db): JsonResponse
    {
        $user = $this->getUser();
        if (!in_array('ADMIN', $user->getRoles())) {
            return $this->json(['message' => 'Forbidden action'], 403);
        }

        $existing = $db->fetchOne(
            'SELECT id FROM products WHERE id=:id AND active=TRUE',
            [
                ':id' => $id
            ]
        );
        if ($existing === null) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid input'], 400);
        }

        $updates = [];
        $params = [':id' => $id];

        if (isset($data['name'])) {
            $updates[] = 'name=:name';
            $params[':name'] = $data['name'];
        }

        if (array_key_exists('price', $data)) {
            if (!is_numeric($data['price'])) {
                return $this->json(['error' => 'Price must be numeric'], 400);
            }
            $price = (float)$data['price'];
            if ($price < 0) {
                return $this->json(['error' => 'Price cannot be negative'], 400);
            }
            $updates[] = 'price=:price';
            $params[':price'] = $data['price'];
        }

        if (isset($data['image'])) {
            $updates[] = 'image=:image';
            $params[':image'] = $data['image'];
        }

        if (isset($data['description'])) {
            $updates[] = 'description=:description';
            $params[':description'] = $data['description'];
        }

        if (count($updates) === 0) {
            return $this->json(['error' => 'No valid fields to update'], 400);
        }

        $sql = 'UPDATE products SET ' . implode(', ', $updates) . ' WHERE id=:id AND active=TRUE';
        $db->execute($sql, $params);

        $updated = $db->fetchOne(
            'SELECT id, name, price, stock, description, image FROM products WHERE id=:id AND active=TRUE',
            [
                ':id' => $id
            ]
        );

        return $this->json([
            'message' => 'Product updated',
            'product' => $updated
        ], 200);
    }
}
