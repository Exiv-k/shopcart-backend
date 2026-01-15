<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Webhook;

class CartController extends AbstractController
{
    #[Route('/api/cart', name: 'api_cart', methods: ['GET'])]
    public function getCart(Database $db): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'User not valid'], 401);
        }
        $userid = $user->getId();

        $cart = $db->fetchOne(
            'SELECT id FROM carts WHERE user_id = :uid LIMIT 1',
            [':uid' => $userid]
        );

        if (!$cart) {
            return $this->json([]);
        }

        $cartId = $cart['id'];

        $items = $db->fetchAll(
            'SELECT ci.id, ci.product_id, ci.quantity, p.name, p.price, p.image FROM cart_items ci
            JOIN products p ON p.id = ci.product_id WHERE ci.cart_id=:cid AND p.active=TRUE',
            [':cid' => $cartId]
        );

        return $this->json($items);
    }

    #[Route('/api/cart/add', name: 'api_cart_add', methods: ['POST'])]
    public function addToCart(Request $request, Database $db): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'User not valid'], 401);
        }
        $userid = $user->getId();
        $data = json_decode($request->getContent(), true);

        $productId = (int)($data['product_id'] ?? 0);
        if ($productId <= 0) {
            return $this->json(['error' => 'No product id provided'], 400);
        }
        $product = $db->fetchOne(
            'SELECT id FROM products WHERE id=:pid',
            [':pid' => $productId]
        );
        if (!$product) {
            return $this->json(['error' => 'Product does not exist'], 400);
        }

        $cart = $db->fetchOne(
            'SELECT id FROM carts WHERE user_id = :uid LIMIT 1',
            [':uid' => $userid]
        );
        if (!$cart) {
            $db->execute(
                'INSERT INTO carts (user_id, created_at, updated_at)
                VALUES (:uid, NOW(), NOW())',
                [':uid' => $userid]
            );
            $cart = $db->fetchOne(
                'SELECT id FROM carts WHERE user_id = :uid LIMIT 1',
                [':uid' => $userid]
            );
        }
        $cartId = $cart['id'];

        $db->execute(
            'INSERT INTO cart_items (cart_id, product_id)
            VALUES (:cid, :pid) ON DUPLICATE KEY UPDATE quantity = quantity + 1',
            [
                ':cid' => $cartId,
                ':pid' => $productId,
            ]
        );

        return $this->json(['message' => 'Item added to cart'], 200);
    }

    #[Route('/api/cart/remove', name: 'api_cart_remove', methods: ['POST'])]
    public function removeFromCart(Request $request, Database $db): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'User not valid'], 401);
        }
        $userid = $user->getId();
        $data = json_decode($request->getContent(), true);

        $productId = (int)($data['product_id'] ?? 0);
        if ($productId <= 0) {
            return $this->json(['error' => 'No product id provided'], 400);
        }

        $cart = $db->fetchOne(
            'SELECT id FROM carts WHERE user_id = :uid LIMIT 1',
            [':uid' => $userid]
        );
        if (!$cart) {
            return $this->json(['error' => 'That is very weird. Could not find cart'], 400);
        }
        $cartId = $cart['id'];

        $db->execute(
            'DELETE FROM cart_items WHERE cart_id = :cid AND product_id = :pid',
            [
                ':pid' => $productId,
                ':cid' => $cartId
            ]
        );
        return $this->json(["message" => "Product deleted"], 200);
    }

    #[Route('/api/checkout', name: 'api_checkout', methods: ['POST'])]
    public function checkout(Database $db): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'User not valid'], 401);
        }
        $userid = $user->getId();

        $cart = $db->fetchOne(
            'SELECT id FROM carts WHERE user_id = :uid LIMIT 1',
            [':uid' => $userid]
        );

        if (!$cart) {
            return $this->json(['error' => 'Cart is empty'], 400);
        }

        $cartId = $cart['id'];

        $items = $db->fetchAll(
            'SELECT ci.quantity, p.name, p.price FROM cart_items ci
            JOIN products p ON p.id = ci.product_id WHERE ci.cart_id=:cid AND p.active=TRUE',
            [':cid' => $cartId]
        );

        if (empty($items)) {
            return $this->json(['error' => 'Cart is empty'], 400);
        }

        // Stripe checkout session creation would go here
        $StripeSecret = $_ENV['STRIPE_SECRET_KEY'];

        if (!$StripeSecret) {
            return $this->json(['error' => 'Stripe not configured. Add stripe secret key and webhook key to .env file'], 503);
        }
        $currency = $_ENV['STRIPE_CURRENCY'] ?? 'aud';
        $frontendUrl = rtrim($_ENV['FRONTEND_URL'] ?? 'http://localhost:5173', '/');
        Stripe::setApiKey($StripeSecret);
        $lineItems = [];
        foreach ($items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => (int)($item['price'] * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $session = StripeCheckoutSession::create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => $frontendUrl . '/checkout/success',
            'cancel_url' => $frontendUrl . '/checkout/cancel',
            'metadata' => [
                'user_id' => $userid,
                'cart_id' => $cartId,
            ],
        ]);

        return $this->json([
            'message' => 'Checkout session created',
            'sessionId' => $session->id,
            'sessionUrl' => $session->url,
        ], 200);
    }

    #[Route('/api/stripe/webhook', name: 'api_stripe_webhook', methods: ['POST'])]
    public function stripeWebhook(Request $request, Database $db): Response
    {
        $webhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'];
        if (!$webhookSecret) {
            return new Response('Webhook secret not configured', 503);
        }

        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $userId = $session->metadata->user_id ?? null;
            $cartId = $session->metadata->cart_id ?? null;

            if ($userId && $cartId) {
                $db->execute(
                    'DELETE FROM cart_items WHERE cart_id = :cid',
                    [':cid' => $cartId]
                );
            }
        }
        return new Response('Payment finished', 200);
    }
}
