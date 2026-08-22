<?php

session_start();

require_once __DIR__ . '/includes/db.php';

const FREE_SHIPPING_THRESHOLD = 5500;
const SHIPPING_FEE = 550;

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function jsonResponse(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
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

function fetchCartItems(PDO $pdo, array $cart): array
{
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

    $sql = "
        SELECT
            p.id,
            p.name,
            p.price,
            p.stock,
            c.name AS category_name,
            (
                SELECT pi.image_path
                FROM product_images AS pi
                WHERE pi.product_id = p.id
                ORDER BY pi.sort_order ASC, pi.id ASC
                LIMIT 1
            ) AS image_path
        FROM products AS p
        INNER JOIN categories AS c
            ON c.id = p.category_id
        WHERE p.id IN ($placeholders)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($productIds);

    $products = [];

    foreach ($stmt->fetchAll() as $product) {
        $productId = (int)$product['id'];
        $stock = max(0, (int)$product['stock']);

        if ($stock === 0) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $quantity = max(1, (int)($cart[$productId] ?? 1));
        $quantity = min($quantity, $stock);

        $_SESSION['cart'][$productId] = $quantity;

        $product['id'] = $productId;
        $product['price'] = (int)$product['price'];
        $product['stock'] = $stock;
        $product['quantity'] = $quantity;

        $products[$productId] = $product;
    }

    foreach ($productIds as $productId) {
        if (!isset($products[$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    $items = [];

    foreach (array_keys($_SESSION['cart']) as $productId) {
        if (isset($products[$productId])) {
            $items[] = $products[$productId];
        }
    }

    return $items;
}

function categoryLabel(string $categoryName): string
{
    $map = [
        'キッチン' => 'KITCHEN',
        'インテリア' => 'INTERIOR',
        'ファブリック' => 'FABRIC',
        'アロマ' => 'AROMA',
    ];

    return $map[$categoryName] ?? $categoryName;
}

/*
 * カート追加・更新・削除（Ajax）
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        jsonResponse([
            'success' => false,
            'message' => '不正なリクエストです。',
        ], 403);
    }

    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if (!$productId || $productId < 1) {
        jsonResponse([
            'success' => false,
            'message' => '商品情報が正しくありません。',
        ], 400);
    }

    if ($action === 'add') {
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$quantity || $quantity < 1) {
            jsonResponse([
                'success' => false,
                'message' => '数量が正しくありません。',
            ], 400);
        }

        $stmt = $pdo->prepare("
            SELECT stock
            FROM products
            WHERE id = ?
        ");
        $stmt->execute([$productId]);

        $stock = $stmt->fetchColumn();

        if ($stock === false || (int)$stock < 1) {
            jsonResponse([
                'success' => false,
                'message' => 'この商品は現在購入できません。',
            ], 400);
        }

        $currentQuantity = (int)($_SESSION['cart'][$productId] ?? 0);

        $newQuantity = min(
            $currentQuantity + $quantity,
            (int)$stock
        );

        $_SESSION['cart'][$productId] = $newQuantity;

    } elseif ($action === 'update') {
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$quantity || $quantity < 1) {
            jsonResponse([
                'success' => false,
                'message' => '数量が正しくありません。',
            ], 400);
        }

        $stmt = $pdo->prepare("
            SELECT stock
            FROM products
            WHERE id = ?
        ");
        $stmt->execute([$productId]);

        $stock = $stmt->fetchColumn();

        if ($stock === false || (int)$stock < 1) {
            unset($_SESSION['cart'][$productId]);

            jsonResponse([
                'success' => false,
                'message' => 'この商品は現在購入できません。',
            ], 400);
        }

        $quantity = min($quantity, (int)$stock);
        $_SESSION['cart'][$productId] = $quantity;

    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$productId]);

    } else {
        jsonResponse([
            'success' => false,
            'message' => '操作が正しくありません。',
        ], 400);
    }

    $cartItems = fetchCartItems($pdo, $_SESSION['cart']);
    $summary = calculateCartSummary($cartItems);

    jsonResponse([
        'success' => true,
        'cartCount' => count($cartItems),
        'subtotal' => $summary['subtotal'],
        'shipping' => $summary['shipping'],
        'total' => $summary['total'],
    ]);
}

$cartItems = fetchCartItems($pdo, $_SESSION['cart']);
$summary = calculateCartSummary($cartItems);

/*
 * おすすめ商品
 * カート内の商品を除外して3件表示
 */
$excludeIds = array_map(
    static fn (array $item): int => (int)$item['id'],
    $cartItems
);

$recommendSql = "
    SELECT
        p.id,
        p.name,
        p.price,
        (
            SELECT pi.image_path
            FROM product_images AS pi
            WHERE pi.product_id = p.id
            ORDER BY pi.sort_order ASC, pi.id ASC
            LIMIT 1
        ) AS image_path
    FROM products AS p
";

$recommendParams = [];

if (!empty($excludeIds)) {
    $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
    $recommendSql .= " WHERE p.id NOT IN ($placeholders)";
    $recommendParams = $excludeIds;
}

$recommendSql .= " ORDER BY p.id ASC LIMIT 3";

$stmt = $pdo->prepare($recommendSql);
$stmt->execute($recommendParams);
$recommendProducts = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>カート | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/cart.css">
</head>

<body>

  <?php include __DIR__ . '/includes/header.php'; ?>

  <main>

    <div class="breadcrumb">
      <a href="index.php">TOP</a>
      <span>&gt;</span>
      <span>カート</span>
    </div>

    <section class="cart-heading">
      <p class="cart-heading__en">SHOPPING CART</p>
      <h1>カート</h1>
    </section>

    <section
      class="cart"
      id="cart"
      <?= empty($cartItems) ? 'hidden' : '' ?>
    >

      <div id="cartItems">

        <?php foreach ($cartItems as $item): ?>

          <div
            class="cart-item"
            data-cart-item
            data-product-id="<?= (int)$item['id'] ?>"
            data-price="<?= (int)$item['price'] ?>"
          >

            <div class="cart-item__top">

              <a
                href="product-detail.php?id=<?= (int)$item['id'] ?>"
                class="cart-item__image"
              >
                <?php if (!empty($item['image_path'])): ?>
                  <img
                    src="<?= htmlspecialchars($item['image_path'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>"
                  >
                <?php endif; ?>
              </a>

              <div class="cart-item__info">

                <p class="cart-item__category">
                  <?= htmlspecialchars(categoryLabel($item['category_name']), ENT_QUOTES, 'UTF-8') ?>
                </p>

                <h2>
                  <a href="product-detail.php?id=<?= (int)$item['id'] ?>">
                    <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </h2>

                <p class="cart-item__price">
                  ￥ <?= number_format((int)$item['price']) ?>
                  <span>（税込）</span>
                </p>

              </div>

            </div>

            <div class="cart-item__bottom">

              <p class="quantity__label">数量</p>

              <div class="quantity">

                <button
                  class="quantity__button quantity__minus"
                  type="button"
                  aria-label="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>の数量を1減らす"
                >
                  −
                </button>

                <input
                  class="quantity__input"
                  type="number"
                  value="<?= (int)$item['quantity'] ?>"
                  min="1"
                  max="<?= (int)$item['stock'] ?>"
                  aria-label="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>の数量"
                >

                <button
                  class="quantity__button quantity__plus"
                  type="button"
                  aria-label="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>の数量を1増やす"
                >
                  ＋
                </button>

              </div>

              <button
                class="delete-button"
                type="button"
              >
                削除
              </button>

            </div>

          </div>

        <?php endforeach; ?>

      </div>

      <div class="cart-summary">

        <div class="summary-row">
          <p>小計</p>
          <p>￥ <span id="subtotal"><?= number_format($summary['subtotal']) ?></span></p>
        </div>

        <div class="summary-row">
          <p>送料</p>
          <p>￥ <span id="shipping"><?= number_format($summary['shipping']) ?></span></p>
        </div>

        <div class="summary-total">
          <p>合計</p>
          <p>￥ <span id="total"><?= number_format($summary['total']) ?></span></p>
        </div>

        <div class="free-shipping">

          <div class="free-shipping__text">

            <p id="freeShippingMessage">
              <?php if ($summary['subtotal'] >= FREE_SHIPPING_THRESHOLD): ?>
                送料無料です
              <?php else: ?>
                あと￥<?= number_format(FREE_SHIPPING_THRESHOLD - $summary['subtotal']) ?>で送料無料
              <?php endif; ?>
            </p>

            <p>￥<?= number_format(FREE_SHIPPING_THRESHOLD) ?></p>

          </div>

          <div class="progress">
            <div
              class="progress__bar"
              id="progressBar"
              style="width: <?= min(100, ($summary['subtotal'] / FREE_SHIPPING_THRESHOLD) * 100) ?>%;"
            ></div>
          </div>

        </div>

        <a
          href="checkout.php"
          class="checkout-button"
          id="checkoutButton"
        >
          ご購入手続きへ
        </a>

        <a
          href="products.php"
          class="continue-shopping"
        >
          お買い物を続ける →
        </a>

      </div>

      <div class="cart-guide">

        <div class="guide-item">

          <button
            class="guide-button"
            type="button"
            aria-expanded="false"
          >
            <span
              class="guide-button__arrow"
              aria-hidden="true"
            >
              ▶
            </span>

            <span>ギフトラッピングについて</span>
          </button>

          <div class="guide-content">
            <div class="guide-content__inner">
              <p>
                ギフトラッピングをご希望の場合は、
                ご購入手続きの際にラッピングをご選択ください。
              </p>
            </div>
          </div>

        </div>

        <div class="guide-item">

          <button
            class="guide-button"
            type="button"
            aria-expanded="false"
          >
            <span
              class="guide-button__arrow"
              aria-hidden="true"
            >
              ▶
            </span>

            <span>配送・返品について</span>
          </button>

          <div class="guide-content">
            <div class="guide-content__inner">
              <p>
                ご注文の商品は3営業日以内に発送します。
                返品・交換についてはご利用ガイドをご確認ください。
              </p>
            </div>
          </div>

        </div>

      </div>

    </section>

    <section
      class="empty-cart"
      id="emptyCart"
      <?= !empty($cartItems) ? 'hidden' : '' ?>
    >
      <p>カートに商品がありません。</p>

      <a href="products.php">
        商品を見る
      </a>
    </section>

    <?php if (!empty($recommendProducts)): ?>

      <section class="recommend">

        <h2>YOU MAY ALSO LIKE</h2>

        <div class="recommend__slider">

          <?php foreach ($recommendProducts as $recommend): ?>

            <a
              href="product-detail.php?id=<?= (int)$recommend['id'] ?>"
              class="recommend-card"
            >

              <div class="recommend-card__image">

                <?php if (!empty($recommend['image_path'])): ?>

                  <img
                    src="<?= htmlspecialchars($recommend['image_path'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($recommend['name'], ENT_QUOTES, 'UTF-8') ?>"
                  >

                <?php endif; ?>

              </div>

              <h3>
                <?= htmlspecialchars($recommend['name'], ENT_QUOTES, 'UTF-8') ?>
              </h3>

              <div class="recommend-card__bottom">

                <p>
                  ￥ <?= number_format((int)$recommend['price']) ?>
                  <span>（税込）</span>
                </p>

                <span
                  class="heart"
                  aria-hidden="true"
                >
                  ♡
                </span>

              </div>

            </a>

          <?php endforeach; ?>

        </div>

      </section>

    <?php endif; ?>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script>
    window.AKARI_CART = {
      csrfToken: <?= json_encode($_SESSION['csrf_token'], JSON_UNESCAPED_UNICODE) ?>,
      freeShippingThreshold: <?= FREE_SHIPPING_THRESHOLD ?>,
      shippingFee: <?= SHIPPING_FEE ?>
    };
  </script>

  <script src="js/cart.js"></script>
  <script src="js/common.js"></script>

</body>

</html>
