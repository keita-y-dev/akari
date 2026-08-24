<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/product-functions.php';

function fetchCartItems(
    PDO $pdo,
    array $cart,
    bool $syncSession = false,
    bool $includeCategory = false
): array {
    if (empty($cart)) {
        return [];
    }

    $productIds = array_values(
        array_filter(
            array_map('intval', array_keys($cart)),
            static fn (int $id): bool => $id > 0
        )
    );

    if (empty($productIds)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    $categorySelect = $includeCategory
        ? ', c.name AS category_name'
        : '';

    $categoryJoin = $includeCategory
        ? 'INNER JOIN categories AS c ON c.id = p.category_id'
        : '';

    $imageSql = primaryProductImageSql();

    $sql = "
        SELECT
            p.id,
            p.name,
            p.price,
            p.stock
            $categorySelect,
            $imageSql AS image_path
        FROM products AS p
        $categoryJoin
        WHERE p.id IN ($placeholders)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($productIds);

    $products = [];

    foreach ($stmt->fetchAll() as $product) {
        $productId = (int)$product['id'];
        $stock = max(0, (int)$product['stock']);

        if ($stock === 0) {
            if ($syncSession) {
                unset($_SESSION['cart'][$productId]);
            }
            continue;
        }

        $quantity = max(1, (int)($cart[$productId] ?? 1));
        $quantity = min($quantity, $stock);

        if ($syncSession) {
            $_SESSION['cart'][$productId] = $quantity;
        }

        $product['id'] = $productId;
        $product['price'] = (int)$product['price'];
        $product['stock'] = $stock;
        $product['quantity'] = $quantity;

        $products[$productId] = $product;
    }

    if ($syncSession) {
        foreach ($productIds as $productId) {
            if (!isset($products[$productId])) {
                unset($_SESSION['cart'][$productId]);
            }
        }
    }

    $items = [];

    foreach (array_keys($cart) as $productId) {
        $productId = (int)$productId;

        if (isset($products[$productId])) {
            $items[] = $products[$productId];
        }
    }

    return $items;
}

function calculateCartSummary(array $cartItems): array
{
    $subtotal = 0;

    foreach ($cartItems as $item) {
        $subtotal += (int)$item['price'] * (int)$item['quantity'];
    }

    $shipping = $subtotal > 0 && $subtotal < FREE_SHIPPING_THRESHOLD
        ? SHIPPING_FEE
        : 0;

    return [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'total' => $subtotal + $shipping,
    ];
}


function getProductStock(PDO $pdo, int $productId): int|false
{
    $stmt = $pdo->prepare("
        SELECT stock
        FROM products
        WHERE id = ?
    ");
    $stmt->execute([$productId]);

    $stock = $stmt->fetchColumn();

    return $stock === false ? false : (int)$stock;
}
