<?php

session_start();

require_once __DIR__ . '/includes/db.php';

const FREE_SHIPPING_THRESHOLD = 5500;
const SHIPPING_FEE = 550;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: order-confirm');
    exit;
}

if (
    empty($_SESSION['cart']) ||
    !is_array($_SESSION['cart']) ||
    empty($_SESSION['checkout']) ||
    !is_array($_SESSION['checkout'])
) {
    header('Location: checkout');
    exit;
}

$csrfToken = (string)($_POST['csrf_token'] ?? '');

if (
    empty($_SESSION['order_csrf_token']) ||
    !hash_equals($_SESSION['order_csrf_token'], $csrfToken)
) {
    $_SESSION['order_error'] =
        '送信内容を確認できませんでした。もう一度お試しください。';

    header('Location: order-confirm');
    exit;
}

$checkout = $_SESSION['checkout'];
$cart = $_SESSION['cart'];

try {
    $pdo->beginTransaction();

    /*
     * カートの商品を注文確定直前に再取得し、
     * FOR UPDATE で在庫行をロックします。
     */
    $productIds = array_values(
        array_filter(
            array_map('intval', array_keys($cart)),
            static fn (int $id): bool => $id > 0
        )
    );

    if (empty($productIds)) {
        throw new RuntimeException('カートに商品がありません。');
    }

    $placeholders =
        implode(',', array_fill(0, count($productIds), '?'));

    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            price,
            stock
        FROM products
        WHERE id IN ($placeholders)
        FOR UPDATE
    ");

    $stmt->execute($productIds);

    $productMap = [];

    foreach ($stmt->fetchAll() as $product) {
        $productMap[(int)$product['id']] = $product;
    }

    $orderItems = [];
    $subtotal = 0;

    foreach ($cart as $productId => $quantity) {
        $productId = (int)$productId;
        $quantity = (int)$quantity;

        if (
            $productId < 1 ||
            $quantity < 1 ||
            !isset($productMap[$productId])
        ) {
            throw new RuntimeException(
                'カートの商品情報を確認できませんでした。'
            );
        }

        $product = $productMap[$productId];
        $stock = (int)$product['stock'];
        $price = (int)$product['price'];

        if ($stock < $quantity) {
            throw new RuntimeException(
                $product['name'] .
                'の在庫が不足しています。カートをご確認ください。'
            );
        }

        $itemSubtotal =
            $price * $quantity;

        $subtotal +=
            $itemSubtotal;

        $orderItems[] = [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'price' => $price,
            'quantity' => $quantity,
            'subtotal' => $itemSubtotal,
        ];
    }

    $shippingFee =
        $subtotal >= FREE_SHIPPING_THRESHOLD
            ? 0
            : SHIPPING_FEE;

    $total =
        $subtotal + $shippingFee;

    /*
     * 注文番号
     */
    $orderNumber =
        'AKARI-' .
        date('Ymd') .
        '-' .
        strtoupper(bin2hex(random_bytes(4)));

    /*
     * orders
     */
    $orderStmt = $pdo->prepare("
        INSERT INTO orders (
            order_number,
            last_name,
            first_name,
            last_name_kana,
            first_name_kana,
            email,
            phone,
            postal_code,
            prefecture,
            address,
            building,
            delivery_method,
            delivery_date,
            delivery_time,
            payment_method,
            gift,
            note,
            subtotal,
            shipping_fee,
            total
        ) VALUES (
            :order_number,
            :last_name,
            :first_name,
            :last_name_kana,
            :first_name_kana,
            :email,
            :phone,
            :postal_code,
            :prefecture,
            :address,
            :building,
            :delivery_method,
            :delivery_date,
            :delivery_time,
            :payment_method,
            :gift,
            :note,
            :subtotal,
            :shipping_fee,
            :total
        )
    ");

    $deliveryDate =
        !empty($checkout['deliveryDate'])
            ? $checkout['deliveryDate']
            : null;

    $deliveryTime =
        !empty($checkout['deliveryTime'])
            ? $checkout['deliveryTime']
            : null;

    $building =
        !empty($checkout['building'])
            ? $checkout['building']
            : null;

    $note =
        !empty($checkout['note'])
            ? $checkout['note']
            : null;

    $orderStmt->execute([
        ':order_number' => $orderNumber,
        ':last_name' => $checkout['lastName'],
        ':first_name' => $checkout['firstName'],
        ':last_name_kana' => $checkout['lastNameKana'],
        ':first_name_kana' => $checkout['firstNameKana'],
        ':email' => $checkout['email'],
        ':phone' => $checkout['phone'],
        ':postal_code' => $checkout['postal'],
        ':prefecture' => $checkout['prefecture'],
        ':address' => $checkout['address'],
        ':building' => $building,
        ':delivery_method' => $checkout['delivery'],
        ':delivery_date' => $deliveryDate,
        ':delivery_time' => $deliveryTime,
        ':payment_method' => $checkout['payment'],
        ':gift' => !empty($checkout['gift']) ? 1 : 0,
        ':note' => $note,
        ':subtotal' => $subtotal,
        ':shipping_fee' => $shippingFee,
        ':total' => $total,
    ]);

    $orderId =
        (int)$pdo->lastInsertId();

    /*
     * order_items
     */
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (
            order_id,
            product_id,
            product_name,
            price,
            quantity,
            subtotal
        ) VALUES (
            :order_id,
            :product_id,
            :product_name,
            :price,
            :quantity,
            :subtotal
        )
    ");

    /*
     * 在庫減算
     */
    $stockStmt = $pdo->prepare("
        UPDATE products
        SET stock = stock - :quantity
        WHERE id = :product_id
    ");

    foreach ($orderItems as $item) {
        $itemStmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $item['product_id'],
            ':product_name' => $item['product_name'],
            ':price' => $item['price'],
            ':quantity' => $item['quantity'],
            ':subtotal' => $item['subtotal'],
        ]);

        $stockStmt->execute([
            ':quantity' => $item['quantity'],
            ':product_id' => $item['product_id'],
        ]);
    }

    $pdo->commit();

    /*
     * 完了画面用データのみ残し、
     * カートと入力情報は注文成功後に削除します。
     */
    $_SESSION['completed_order'] = [
        'order_id' => $orderId,
        'order_number' => $orderNumber,
        'email' => $checkout['email'],
        'total' => $total,
    ];

    unset(
        $_SESSION['cart'],
        $_SESSION['checkout'],
        $_SESSION['order_csrf_token']
    );

    header('Location: order-complete');
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    /*
     * 本番では内部エラー内容を画面に出さず、
     * ユーザー向けメッセージだけ表示します。
     */
    if ($e instanceof RuntimeException) {
        $_SESSION['order_error'] =
            $e->getMessage();
    } else {
        $_SESSION['order_error'] =
            '注文処理中にエラーが発生しました。もう一度お試しください。';
    }

    header('Location: order-confirm');
    exit;
}
