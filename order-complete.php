<?php

session_start();

if (
    empty($_SESSION['completed_order']) ||
    !is_array($_SESSION['completed_order'])
) {
    header('Location: index');
    exit;
}

$completedOrder = $_SESSION['completed_order'];

$orderNumber = (string)($completedOrder['order_number'] ?? '');
$orderId = (int)($completedOrder['order_id'] ?? 0);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$stmt = $pdo->prepare("
    SELECT
        order_number,
        last_name,
        first_name,
        email,
        postal_code,
        prefecture,
        address,
        building,
        delivery_date,
        delivery_time,
        payment_method,
        total,
        created_at
    FROM orders
    WHERE id = ?
      AND order_number = ?
    LIMIT 1
");

$stmt->execute([
    $orderId,
    $orderNumber,
]);

$order = $stmt->fetch();

if (!$order) {
    unset($_SESSION['completed_order']);
    header('Location: index');
    exit;
}


$paymentLabels = [
    'card' => 'クレジットカード',
    'cod' => '代金引換',
];

$timeLabels = [
    '' => '指定なし',
    '午前中' => '午前中',
    '14-16' => '14:00〜16:00',
    '16-18' => '16:00〜18:00',
    '18-20' => '18:00〜20:00',
];

$createdAt = strtotime((string)$order['created_at']);
$createdAtLabel = $createdAt !== false
    ? date('Y年n月j日 H:i', $createdAt)
    : (string)$order['created_at'];

$deliveryDateLabel = '指定なし';

if (!empty($order['delivery_date'])) {
    $deliveryTimestamp = strtotime((string)$order['delivery_date']);

    $deliveryDateLabel = $deliveryTimestamp !== false
        ? date('Y年n月j日', $deliveryTimestamp)
        : (string)$order['delivery_date'];
}

$deliveryTimeLabel =
    $timeLabels[$order['delivery_time'] ?? '']
    ?? '指定なし';

$paymentLabel =
    $paymentLabels[$order['payment_method'] ?? '']
    ?? (string)$order['payment_method'];

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>ご注文完了 | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/order-complete.css">
</head>


<body>

  <!-- =========================
       HEADER
  ========================== -->

  <?php include __DIR__ . '/includes/header.php'; ?>


  <main>

    <?php
      $breadcrumbs = [
        ['label' => 'TOP', 'url' => 'index'],
        ['label' => 'ご注文完了', 'url' => null],
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?>


    <!-- =========================
         PAGE HEADING
    ========================== -->

    <section class="page-heading">

      <p class="page-heading__en">
        ORDER COMPLETE
      </p>

      <h1>
        ご注文完了
      </h1>

    </section>


    <!-- =========================
         STEP
    ========================== -->

    <div class="checkout-step">

      <div class="step">
        <span>1</span>
        <p>情報入力</p>
      </div>

      <div class="step-line"></div>

      <div class="step">
        <span>2</span>
        <p>内容確認</p>
      </div>

      <div class="step-line"></div>

      <div class="step is-current">
        <span>3</span>
        <p>完了</p>
      </div>

    </div>


    <!-- =========================
         COMPLETE MESSAGE
    ========================== -->

    <section class="complete-message">

      <h2>
        ご注文ありがとうございます
      </h2>

      <p>
        ご注文を承りました。
      </p>

      <p>
        ご登録のメールアドレスへ<br>
        注文確認メールを送りしました。
      </p>

      <p class="demo-note">
        こちらはデモサイトのため、実際の注文・決済・メール返信は<br>
        行われません。
      </p>

    </section>


    <!-- =========================
         ORDER INFORMATION
    ========================== -->

    <section class="order-section">

      <h2 class="section-title">
        ご注文情報
      </h2>


      <dl class="order-info">

        <div class="order-info__row">

          <dt>
            注文番号
          </dt>

          <dd>
            <?= h($order['order_number']) ?>
          </dd>

        </div>


        <div class="order-info__row">

          <dt>
            注文日時
          </dt>

          <dd>
            <?= h($createdAtLabel) ?>
          </dd>

        </div>


        <div class="order-info__row">

          <dt>
            ご請求金額
          </dt>

          <dd>
            ￥ <?= number_format((int)$order['total']) ?>（税込）
          </dd>

        </div>


        <div class="order-info__row">

          <dt>
            お支払方法
          </dt>

          <dd>
            <?= h($paymentLabel) ?>
          </dd>

        </div>

      </dl>

    </section>


    <!-- =========================
         DELIVERY INFORMATION
    ========================== -->

    <section class="order-section">

      <h2 class="section-title">
        お届け先
      </h2>


      <div class="delivery-info">

        <p>
          <?= h($order['last_name']) ?> <?= h($order['first_name']) ?> 様
        </p>

        <p>
          〒 <?= h($order['postal_code']) ?>
        </p>

        <p>
          <?= h($order['prefecture']) ?><?= h($order['address']) ?>
        </p>

        <?php if (!empty($order['building'])): ?>
          <p>
            <?= h($order['building']) ?>
          </p>
        <?php endif; ?>

      </div>


      <dl class="delivery-date">

        <div class="delivery-date__row">

          <dt>
            お届け希望日
          </dt>

          <dd>
            <?= h($deliveryDateLabel) ?>
          </dd>

        </div>


        <div class="delivery-date__row">

          <dt>
            希望時間
          </dt>

          <dd>
            <?= h($deliveryTimeLabel) ?>
          </dd>

        </div>

      </dl>

    </section>


    <!-- =========================
         SHIPPING
    ========================== -->

    <section class="shipping-section">

      <h2 class="section-title">
        発送について
      </h2>

      <p>
        商品は通常3営業日以内に発送いたします。
      </p>

      <p>
        発送後、メールでお知らせします。
      </p>

    </section>


    <!-- =========================
         ACTION
    ========================== -->

    <section class="complete-action">

      <a
        href="index"
        class="top-button"
      >
        T O P ペ ー ジ へ 戻 る
      </a>


      <a
        href="index"
        class="continue-link"
      >
        買い物を続ける→
      </a>

    </section>


    <!-- =========================
         CONTACT
    ========================== -->

    <section class="contact-section">

      <p>
        ご不明な点がございましたら、
      </p>

      <p>
        お問い合わせページよりご連絡ください。
      </p>

      <a
        href="#"
        class="contact-link"
      >
        お問い合わせはこちら→
      </a>

    </section>

  </main>


  <!-- =========================
       FOOTER
  ========================== -->

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="js/common.js"></script>
  
</body>

</html>