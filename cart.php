<?php

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/cart-functions.php';

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

        $stock = getProductStock($pdo, $productId);

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

        $stock = getProductStock($pdo, $productId);

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

    $cartItems = fetchCartItems($pdo, $_SESSION['cart'], true, true);
    $summary = calculateCartSummary($cartItems);

    jsonResponse([
        'success' => true,
        'cartCount' => count($cartItems),
        'subtotal' => $summary['subtotal'],
        'shipping' => $summary['shipping'],
        'total' => $summary['total'],
    ]);
}

$cartItems = fetchCartItems($pdo, $_SESSION['cart'], true, true);
$summary = calculateCartSummary($cartItems);

/*
 * おすすめ商品
 * カート内の商品を除外して3件表示
 */
$excludeIds = array_map(
    static fn (array $item): int => (int)$item['id'],
    $cartItems
);

$recommendProducts = fetchRecommendedProducts(
    $pdo,
    $excludeIds,
    3
);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="icon" href="images/logos/favicon.svg" type="image/svg+xml">

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

    <?php
      $breadcrumbs = [
        ['label' => 'TOP', 'url' => 'index'],
        ['label' => 'カート', 'url' => null],
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?>

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
          <?php include __DIR__ . '/includes/cart-item.php'; ?>
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
          href="checkout"
          class="checkout-button"
          id="checkoutButton"
        >
          ご購入手続きへ
        </a>

        <a
          href="products"
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
                ギフトラッピングをご希望の場合は、ご購入手続き画面でご指定ください。
                商品の雰囲気に合わせた簡易ラッピングでお包みします。
                複数の商品をまとめてラッピングする場合や、個別包装をご希望の場合は、
                ご注文時の備考欄に内容をご記入ください。
                なお、商品の形状やサイズによってはラッピングを承れない場合があります。
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
                ご注文確定後、通常3営業日以内に発送いたします。
                送料は全国一律￥550、商品合計￥5,500以上で送料無料です。
                お客様都合による返品・交換は、未使用・未開封の商品に限り、
                商品到着後7日以内にご連絡ください。
                不良品や誤配送の場合は、送料当店負担にて交換または返金対応いたします。
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

      <a href="products">
        商品を見る
      </a>
    </section>

    <?php if (!empty($recommendProducts)): ?>

      <section class="recommend">

        <h2>YOU MAY ALSO LIKE</h2>

        <div class="recommend__slider">

          <?php foreach ($recommendProducts as $recommend): ?>
            <?php
              $recommendCardOptions = [
                'showFavorite' => true,
              ];
              include __DIR__ . '/includes/recomend-card.php';
            ?>
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
