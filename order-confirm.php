<?php

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/cart-functions.php';

if (empty($_SESSION['order_csrf_token'])) {
    $_SESSION['order_csrf_token'] = bin2hex(random_bytes(32));
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

$checkout = $_SESSION['checkout'];

$cartItems = fetchCartItems($pdo, $_SESSION['cart']);

if (empty($cartItems)) {
    header('Location: cart');
    exit;
}

$summary = calculateCartSummary($cartItems);
$subtotal = $summary['subtotal'];
$shipping = $summary['shipping'];
$total = $summary['total'];

$deliveryLabels = [
    'normal' => '通常配送',
];


$deliveryDate = $checkout['deliveryDate'] ?? '';
$deliveryDateLabel = '指定なし';

if ($deliveryDate !== '') {
    $timestamp = strtotime($deliveryDate);
    $deliveryDateLabel = $timestamp !== false
        ? date('Y年n月j日', $timestamp)
        : $deliveryDate;
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>ご注文内容の確認 | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/order-confirm.css">
</head>

<body>

  <?php include __DIR__ . '/includes/header.php'; ?>

  <main>

    <?php
      $breadcrumbs = [
        ['label' => 'TOP', 'url' => 'index'],
        ['label' => 'カート', 'url' => 'cart'],
        ['label' => 'ご注文内容確認', 'url' => null],
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?>

    <section class="page-heading">
      <p class="page-heading__en">ORDER CONFIRMATION</p>
      <h1>ご注文内容の確認</h1>
    </section>

    <div class="checkout-step">
      <div class="step">
        <span>1</span>
        <p>情報入力</p>
      </div>

      <div class="step-line"></div>

      <div class="step is-current">
        <span>2</span>
        <p>内容確認</p>
      </div>

      <div class="step-line"></div>

      <div class="step">
        <span>3</span>
        <p>完了</p>
      </div>
    </div>

    <div class="confirmation-description">
      <p>入力内容をご確認ください。</p>
      <p>修正する場合は、各項目の「変更する」を押してください。</p>
    </div>

    <div class="confirmation">

      <?php if (!empty($_SESSION['order_error'])): ?>
        <div class="form-error-summary" role="alert">
          <?= h($_SESSION['order_error']) ?>
        </div>
        <?php unset($_SESSION['order_error']); ?>
      <?php endif; ?>

      <section class="confirm-section">

        <div class="section-heading">
          <h2>ご注文商品</h2>

          <a class="change-button" href="cart">
            変更する
            <span>&gt;</span>
          </a>
        </div>

        <?php foreach ($cartItems as $item): ?>
          <div class="order-item">

            <div class="order-item__image">
              <?php if (!empty($item['image_path'])): ?>
                <img
                  src="<?= h($item['image_path']) ?>"
                  alt="<?= h($item['name']) ?>"
                >
              <?php endif; ?>
            </div>

            <div class="order-item__info">
              <h3><?= h($item['name']) ?></h3>

              <p>
                ￥<?= number_format($item['price']) ?>（税込）
              </p>

              <p>
                数量：<?= (int)$item['quantity'] ?>
              </p>
            </div>

          </div>
        <?php endforeach; ?>

        <div class="price-list">

          <div class="price-row">
            <p>小計</p>
            <p>￥<?= number_format($subtotal) ?></p>
          </div>

          <div class="price-row">
            <p>送料</p>
            <p>￥<?= number_format($shipping) ?></p>
          </div>

          <div class="price-total">
            <p>合計（税込）</p>
            <p>￥<?= number_format($total) ?></p>
          </div>

        </div>

      </section>

      <section class="confirm-section">

        <div class="section-heading">
          <h2>お客様情報</h2>

          <a class="change-button" href="checkout">
            変更する
            <span>&gt;</span>
          </a>
        </div>

        <dl class="confirm-list">

          <div class="confirm-row">
            <dt>お名前</dt>
            <dd>
              <?= h($checkout['lastName'] ?? '') ?>
              <?= h($checkout['firstName'] ?? '') ?>
            </dd>
          </div>

          <div class="confirm-row">
            <dt>フリガナ</dt>
            <dd>
              <?= h($checkout['lastNameKana'] ?? '') ?>
              <?= h($checkout['firstNameKana'] ?? '') ?>
            </dd>
          </div>

          <div class="confirm-row">
            <dt>メールアドレス</dt>
            <dd><?= h($checkout['email'] ?? '') ?></dd>
          </div>

          <div class="confirm-row">
            <dt>電話番号</dt>
            <dd><?= h($checkout['phone'] ?? '') ?></dd>
          </div>

        </dl>

      </section>

      <section class="confirm-section">

        <div class="section-heading">
          <h2>配送先</h2>

          <a class="change-button" href="checkout">
            変更する
            <span>&gt;</span>
          </a>
        </div>

        <div class="address">
          <p>〒 <?= h($checkout['postal'] ?? '') ?></p>

          <p>
            <?= h($checkout['prefecture'] ?? '') ?>
            <?= h($checkout['address'] ?? '') ?>
          </p>

          <?php if (!empty($checkout['building'])): ?>
            <p><?= h($checkout['building']) ?></p>
          <?php endif; ?>
        </div>

      </section>

      <section class="confirm-section">

        <div class="section-heading">
          <h2>配送方法・希望日時</h2>

          <a class="change-button" href="checkout">
            変更する
            <span>&gt;</span>
          </a>
        </div>

        <dl class="confirm-list">

          <div class="confirm-row">
            <dt>配送方法</dt>
            <dd>
              <?= h($deliveryLabels[$checkout['delivery'] ?? 'normal'] ?? '通常配送') ?>
            </dd>
          </div>

          <div class="confirm-row">
            <dt>お届け希望日</dt>
            <dd><?= h($deliveryDateLabel) ?></dd>
          </div>

          <div class="confirm-row">
            <dt>お届け希望時間</dt>
            <dd>
              <?= h(deliveryTimeLabel((string)($checkout['deliveryTime'] ?? ''))) ?>
            </dd>
          </div>

        </dl>

      </section>

      <section class="confirm-section">

        <div class="section-heading">
          <h2>お支払い方法</h2>

          <a class="change-button" href="checkout">
            変更する
            <span>&gt;</span>
          </a>
        </div>

        <dl class="confirm-list">

          <div class="confirm-row">
            <dt>お支払い方法</dt>
            <dd>
              <?= h(paymentLabel((string)($checkout['payment'] ?? 'card'))) ?>
            </dd>
          </div>

          <?php if (($checkout['payment'] ?? 'card') === 'card'): ?>
            <div class="confirm-row">
              <dt>カード情報</dt>
              <dd>
                デモ画面のため保存しません
                <small>※実際のカード情報は入力しないでください</small>
              </dd>
            </div>
          <?php endif; ?>

        </dl>

      </section>

      <section class="confirm-section">

        <div class="section-heading">
          <h2>ギフト・備考</h2>

          <a class="change-button" href="checkout">
            変更する
            <span>&gt;</span>
          </a>
        </div>

        <dl class="confirm-list">

          <div class="confirm-row">
            <dt>ギフトラッピング</dt>
            <dd>
              <?= !empty($checkout['gift']) ? '希望する' : '希望しない' ?>
            </dd>
          </div>

          <div class="confirm-row">
            <dt>備考</dt>
            <dd>
              <?= ($checkout['note'] ?? '') !== ''
                  ? nl2br(h($checkout['note']))
                  : 'なし' ?>
            </dd>
          </div>

        </dl>

      </section>

      <section class="confirm-section billing-section">

        <div class="section-heading">
          <h2>ご請求金額</h2>
        </div>

        <div class="billing">

          <div class="billing-row">
            <p>小計</p>
            <p>￥<?= number_format($subtotal) ?></p>
          </div>

          <div class="billing-row">
            <p>送料</p>
            <p>￥<?= number_format($shipping) ?></p>
          </div>

          <div class="billing-total">
            <p>合計（税込）</p>
            <p>￥<?= number_format($total) ?></p>
          </div>

        </div>

      </section>

      <section class="order-action">

        <form action="order-process" method="post">
          <input
            type="hidden"
            name="csrf_token"
            value="<?= h($_SESSION['order_csrf_token']) ?>"
          >

          <button
            class="order-button"
            id="orderButton"
            type="submit"
          >
            注 文 を 確 定 す る
          </button>
        </form>

        <a
          class="back-button"
          id="backButton"
          href="checkout"
        >
          ←入力内容の修正に戻る
        </a>

        <p class="order-note">
          ※ボタンを押すと注文が確定します。
        </p>

      </section>

    </div>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="js/common.js"></script>
</body>

</html>
