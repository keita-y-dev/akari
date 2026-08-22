<?php

session_start();

require_once __DIR__ . '/includes/db.php';

const FREE_SHIPPING_THRESHOLD = 5500;
const SHIPPING_FEE = 550;

if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$productIds = array_values(
    array_filter(
        array_map('intval', array_keys($_SESSION['cart'])),
        static fn (int $id): bool => $id > 0
    )
);

if (empty($productIds)) {
    header('Location: cart.php');
    exit;
}

$placeholders = implode(',', array_fill(0, count($productIds), '?'));

$sql = "
    SELECT
        p.id,
        p.name,
        p.price,
        p.stock,
        (
            SELECT pi.image_path
            FROM product_images AS pi
            WHERE pi.product_id = p.id
            ORDER BY pi.sort_order ASC, pi.id ASC
            LIMIT 1
        ) AS image_path
    FROM products AS p
    WHERE p.id IN ($placeholders)
";

$stmt = $pdo->prepare($sql);
$stmt->execute($productIds);

$productMap = [];

foreach ($stmt->fetchAll() as $product) {
    $productMap[(int)$product['id']] = $product;
}

$cartItems = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $productId => $quantity) {
    $productId = (int)$productId;

    if (!isset($productMap[$productId])) {
        unset($_SESSION['cart'][$productId]);
        continue;
    }

    $product = $productMap[$productId];
    $stock = max(0, (int)$product['stock']);

    if ($stock < 1) {
        unset($_SESSION['cart'][$productId]);
        continue;
    }

    $quantity = max(1, (int)$quantity);
    $quantity = min($quantity, $stock);

    $_SESSION['cart'][$productId] = $quantity;

    $product['price'] = (int)$product['price'];
    $product['quantity'] = $quantity;

    $subtotal += $product['price'] * $quantity;
    $cartItems[] = $product;
}

if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$shipping = $subtotal >= FREE_SHIPPING_THRESHOLD
    ? 0
    : SHIPPING_FEE;

$total = $subtotal + $shipping;

if (empty($_SESSION['checkout_csrf_token'])) {
    $_SESSION['checkout_csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$form = $_SESSION['checkout'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['checkout_csrf_token'], $token)) {
        $errors['form'] = '送信内容を確認できませんでした。もう一度お試しください。';
    }

    $form = [
        'lastName' => trim((string)($_POST['lastName'] ?? '')),
        'firstName' => trim((string)($_POST['firstName'] ?? '')),
        'lastNameKana' => trim((string)($_POST['lastNameKana'] ?? '')),
        'firstNameKana' => trim((string)($_POST['firstNameKana'] ?? '')),
        'email' => trim((string)($_POST['email'] ?? '')),
        'phone' => trim((string)($_POST['phone'] ?? '')),
        'postal' => trim((string)($_POST['postal'] ?? '')),
        'prefecture' => trim((string)($_POST['prefecture'] ?? '')),
        'address' => trim((string)($_POST['address'] ?? '')),
        'building' => trim((string)($_POST['building'] ?? '')),
        'delivery' => (string)($_POST['delivery'] ?? 'normal'),
        'deliveryDate' => (string)($_POST['deliveryDate'] ?? ''),
        'deliveryTime' => (string)($_POST['deliveryTime'] ?? ''),
        'payment' => (string)($_POST['payment'] ?? 'card'),
        'gift' => isset($_POST['gift']),
        'note' => trim((string)($_POST['note'] ?? '')),
        'agreement' => isset($_POST['agreement']),
    ];

    $required = [
        'lastName' => '姓を入力してください。',
        'firstName' => '名を入力してください。',
        'lastNameKana' => '姓のフリガナを入力してください。',
        'firstNameKana' => '名のフリガナを入力してください。',
        'email' => 'メールアドレスを入力してください。',
        'phone' => '電話番号を入力してください。',
        'postal' => '郵便番号を入力してください。',
        'prefecture' => '都道府県を選択してください。',
        'address' => '市町村・番地を入力してください。',
    ];

    foreach ($required as $key => $message) {
        if ($form[$key] === '') {
            $errors[$key] = $message;
        }
    }

    if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'メールアドレスの形式が正しくありません。';
    }

    foreach (['lastNameKana', 'firstNameKana'] as $key) {
        if ($form[$key] !== '' && !preg_match('/^[ァ-ヶー　\s]+$/u', $form[$key])) {
            $errors[$key] = 'フリガナは全角カタカナで入力してください。';
        }
    }

    $phoneDigits = preg_replace('/\D/u', '', $form['phone']);
    if ($form['phone'] !== '' && !preg_match('/^\d{10,11}$/', $phoneDigits)) {
        $errors['phone'] = '電話番号は10〜11桁の数字で入力してください。';
    }

    $postalDigits = preg_replace('/\D/u', '', $form['postal']);
    if ($form['postal'] !== '' && !preg_match('/^\d{7}$/', $postalDigits)) {
        $errors['postal'] = '郵便番号は7桁で入力してください。';
    }

    if (!in_array($form['prefecture'], ['北海道','東京都','大阪府','福岡県','鹿児島県'], true)) {
        $errors['prefecture'] = '都道府県を選択してください。';
    }

    if (!in_array($form['payment'], ['card', 'cod'], true)) {
        $errors['payment'] = 'お支払い方法が正しくありません。';
    }

    if (!$form['agreement']) {
        $errors['agreement'] = '利用規約とプライバシーポリシーへの同意が必要です。';
    }

    if (empty($errors)) {
        $_SESSION['checkout'] = $form;
        header('Location: order-confirm.php');
        exit;
    }
}

function old(array $form, string $key): string
{
    return htmlspecialchars((string)($form[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>ご購入手続き | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/checkout.css">
</head>


<body>

  <!-- =========================
       HEADER
  ========================== -->

  <?php include __DIR__ . '/includes/header.php'; ?>


  <main>

    <!-- =========================
         BREADCRUMB
    ========================== -->

    <div class="breadcrumb">

      <a href="index.php">
        TOP
      </a>

      <span>&gt;</span>

      <a href="cart.php">
        カート
      </a>

      <span>&gt;</span>

      <span>
        ご購入手続き
      </span>

    </div>


    <!-- =========================
         HEADING
    ========================== -->

    <section class="checkout-heading">

      <p class="checkout-heading__en">
        CHECKOUT
      </p>

      <h1>
        ご購入手続き
      </h1>

    </section>


    <!-- =========================
         STEP
    ========================== -->

    <div class="checkout-step">

      <div class="step is-current">
        <span>1</span>
        <p>情報入力</p>
      </div>

      <div class="step-line"></div>

      <div class="step">
        <span>2</span>
        <p>内容確認</p>
      </div>

      <div class="step-line"></div>

      <div class="step">
        <span>3</span>
        <p>完了</p>
      </div>

    </div>


    <!-- =========================
         CHECKOUT FORM
    ========================== -->

    <form
      class="checkout"
      id="checkoutForm"
      action="checkout.php"
      method="post"
    >
      <input type="hidden" name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['checkout_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">


      <!-- =========================
           ORDER ITEM
      ========================== -->

      <section class="checkout-section order-section">

        <h2>
          ご注文商品
        </h2>


        <?php foreach ($cartItems as $item): ?>

          <div class="order-item">

            <div class="order-item__image">

              <?php if (!empty($item['image_path'])): ?>
                <img
                  src="<?= htmlspecialchars($item['image_path'], ENT_QUOTES, 'UTF-8') ?>"
                  alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>"
                >
              <?php endif; ?>

            </div>

            <div class="order-item__info">

              <h3>
                <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
              </h3>

              <p>
                数量：<?= (int)$item['quantity'] ?>
              </p>

              <p class="order-item__price">
                ￥ <?= number_format($item['price'] * $item['quantity']) ?>
                <span>（税込）</span>
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


      <!-- =========================
           CUSTOMER
      ========================== -->

      <section class="checkout-section">

        <h2>
          お客様情報
        </h2>


        <!-- name -->

        <div class="form-group">

          <label>
            お名前
            <span class="required">必須</span>
          </label>


          <div class="form-row">

            <div class="form-field">

              <input
                type="text"
                name="lastName"
                placeholder="姓"
                required
               value="<?= old($form, 'lastName') ?>">

            </div>


            <div class="form-field">

              <input
                type="text"
                name="firstName"
                placeholder="名"
                required
               value="<?= old($form, 'firstName') ?>">

            </div>

          </div>

        </div>


        <!-- furigana -->

        <div class="form-group">

          <label>
            フリガナ
            <span class="required">必須</span>
          </label>


          <div class="form-row">

            <div class="form-field">

              <input
                type="text"
                name="lastNameKana"
                placeholder="セイ"
                required
               value="<?= old($form, 'lastNameKana') ?>">

            </div>


            <div class="form-field">

              <input
                type="text"
                name="firstNameKana"
                placeholder="メイ"
                required
               value="<?= old($form, 'firstNameKana') ?>">

            </div>

          </div>

        </div>


        <!-- email -->

        <div class="form-group">

          <label for="email">
            メールアドレス
            <span class="required">必須</span>
          </label>

          <input
            id="email"
            type="email"
            name="email"
            placeholder="example@email.com"
            required
           value="<?= old($form, 'email') ?>">

        </div>


        <!-- phone -->

        <div class="form-group">

          <label for="phone">
            電話番号
            <span class="required">必須</span>
          </label>

          <input
            id="phone"
            type="tel"
            name="phone"
            placeholder="09012345678"
            required
           value="<?= old($form, 'phone') ?>">

        </div>

      </section>


      <!-- =========================
           ADDRESS
      ========================== -->

      <section class="checkout-section">

        <h2>
          配送先
        </h2>


        <!-- postal -->

        <div class="form-group">

          <label for="postal">
            郵便番号
            <span class="required">必須</span>
          </label>


          <div class="postal-row">

            <input
              id="postal"
              type="text"
              name="postal"
              placeholder="123-4567"
              maxlength="8"
              required
             value="<?= old($form, 'postal') ?>">

            <button
              class="postal-button"
              id="postalButton"
              type="button"
            >
              住所検索
            </button>

          </div>

        </div>


        <!-- prefecture -->

        <div class="form-group">

          <label for="prefecture">
            都道府県
            <span class="required">必須</span>
          </label>

          <select
            id="prefecture"
            name="prefecture"
            required
          >

            <option value="">
              選択してください
            </option>

            <option value="北海道" <?= (($form['prefecture'] ?? '') === '北海道') ? 'selected' : '' ?>>
              北海道
            </option>

            <option value="東京都" <?= (($form['prefecture'] ?? '') === '東京都') ? 'selected' : '' ?>>
              東京都
            </option>

            <option value="大阪府" <?= (($form['prefecture'] ?? '') === '大阪府') ? 'selected' : '' ?>>
              大阪府
            </option>

            <option value="福岡県" <?= (($form['prefecture'] ?? '') === '福岡県') ? 'selected' : '' ?>>
              福岡県
            </option>

            <option value="鹿児島県" <?= (($form['prefecture'] ?? '') === '鹿児島県') ? 'selected' : '' ?>>
              鹿児島県
            </option>

          </select>

        </div>


        <!-- address -->

        <div class="form-group">

          <label for="address">
            市町村・番地
            <span class="required">必須</span>
          </label>

          <input
            id="address"
            type="text"
            name="address"
            required
           value="<?= old($form, 'address') ?>">

        </div>


        <!-- building -->

        <div class="form-group">

          <label for="building">
            建物名・部屋番号
            <span class="optional">任意</span>
          </label>

          <input
            id="building"
            type="text"
            name="building"
           value="<?= old($form, 'building') ?>">

        </div>

      </section>


      <!-- =========================
           DELIVERY
      ========================== -->

      <section class="checkout-section">

        <h2>
          配送方法・希望日時
        </h2>


        <div class="delivery-method">

          <label class="radio-card">

            <input
              type="radio"
              name="delivery"
              value="normal"
              checked
            >

            <span class="radio-custom"></span>

            <span class="radio-text">

              <strong>
                通常配送
              </strong>

              <small>
                送料 ￥550
              </small>

            </span>

          </label>


          <p class="delivery-note">
            3営業日以内に発送
          </p>

        </div>


        <!-- date -->

        <div class="form-group">

          <label for="deliveryDate">
            お届け希望日
            <span class="optional">任意</span>
          </label>

          <select
            id="deliveryDate"
            name="deliveryDate"
          >

            <option value="">
              指定なし
            </option>

            <option value="2026-08-22" <?= (($form['deliveryDate'] ?? '') === '2026-08-22') ? 'selected' : '' ?>>
              8月22日（土）
            </option>

            <option value="2026-08-23" <?= (($form['deliveryDate'] ?? '') === '2026-08-23') ? 'selected' : '' ?>>
              8月23日（日）
            </option>

            <option value="2026-08-24" <?= (($form['deliveryDate'] ?? '') === '2026-08-24') ? 'selected' : '' ?>>
              8月24日（月）
            </option>

          </select>

        </div>


        <!-- time -->

        <div class="form-group">

          <label for="deliveryTime">
            お届け先希望時間
            <span class="optional">任意</span>
          </label>

          <select
            id="deliveryTime"
            name="deliveryTime"
          >

            <option value="">
              指定なし
            </option>

            <option value="午前中" <?= (($form['deliveryTime'] ?? '') === '午前中') ? 'selected' : '' ?>>
              午前中
            </option>

            <option value="14-16" <?= (($form['deliveryTime'] ?? '') === '14-16') ? 'selected' : '' ?>>
              14:00〜16:00
            </option>

            <option value="16-18" <?= (($form['deliveryTime'] ?? '') === '16-18') ? 'selected' : '' ?>>
              16:00〜18:00
            </option>

            <option value="18-20" <?= (($form['deliveryTime'] ?? '') === '18-20') ? 'selected' : '' ?>>
              18:00〜20:00
            </option>

          </select>

        </div>

      </section>


      <!-- =========================
           PAYMENT
      ========================== -->

      <section class="checkout-section">

        <h2>
          お支払い方法
        </h2>


        <div class="payment-method">


          <label class="payment-option">

            <input
              type="radio"
              name="payment"
              value="card"
              <?= (($form['payment'] ?? 'card') === 'card') ? 'checked' : '' ?>
            >

            <span>
              クレジットカード
            </span>

          </label>


          <label class="payment-option">

            <input
              type="radio"
              name="payment"
              value="cod"
              <?= (($form['payment'] ?? '') === 'cod') ? 'checked' : '' ?>
            >

            <span>
              代金引換
            </span>

          </label>

        </div>


        <!-- card -->

        <div
          class="card-form"
          id="cardForm"
        >


          <div class="form-group">

            <label for="cardNumber">
              カード番号
            </label>

            <input
              id="cardNumber"
              type="text"
              inputmode="numeric"
              placeholder="0000 0000 0000"
            >

          </div>


          <div class="form-group">

            <label>
              有効期限
            </label>


            <div class="expiration">

              <select
                id="expirationMonth"
              >

                <option value="">
                  月
                </option>

                <option>01</option>
                <option>02</option>
                <option>03</option>
                <option>04</option>
                <option>05</option>
                <option>06</option>
                <option>07</option>
                <option>08</option>
                <option>09</option>
                <option>10</option>
                <option>11</option>
                <option>12</option>

              </select>


              <span>/</span>


              <select
                id="expirationYear"
              >

                <option value="">
                  年
                </option>

                <option>26</option>
                <option>27</option>
                <option>28</option>
                <option>29</option>
                <option>30</option>

              </select>

            </div>

          </div>


          <div class="form-group">

            <label for="cardName">
              カード名義
            </label>

            <input
              id="cardName"
              type="text"
              placeholder="TARO YAMADA"
            >

          </div>


          <p class="demo-notice">
            デモ画面のため、実際のカード情報は入力しないでください。
          </p>


          <div class="form-group">

            <label for="securityCode">
              セキュリティコード
            </label>

            <input
              id="securityCode"
              class="security-code"
              type="text"
              inputmode="numeric"
              maxlength="4"
              placeholder="000"
            >

          </div>

        </div>

      </section>


      <!-- =========================
           GIFT / NOTE
      ========================== -->

      <section class="checkout-section">

        <h2>
          ギフト・備考
        </h2>


        <label class="check-option">

          <input
            type="checkbox"
            id="gift"
            name="gift"
            <?= !empty($form['gift']) ? 'checked' : '' ?>
          >

          <span class="check-custom"></span>

          <span>
            ギフトラッピングを希望する
          </span>

        </label>


        <div class="form-group note-group">

          <label for="note">
            備考
            <span class="optional">任意</span>
          </label>

          <textarea
            id="note"
            name="note"
            rows="5"
          ><?= old($form, 'note') ?></textarea>

        </div>

      </section>


      <!-- =========================
           TOTAL
      ========================== -->

      <section class="billing">

        <h2>
          ご請求金額
        </h2>

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

      </section>


      <!-- =========================
           AGREEMENT
      ========================== -->

      <label class="agreement">

        <input
          type="checkbox"
          id="agreement"
          name="agreement"
          <?= !empty($form['agreement']) ? 'checked' : '' ?>
          required
        >

        <span class="check-custom"></span>

        <span>
          利用規約とプライバシーポリシーに同意する
          <em>必須</em>
        </span>

      </label>


      <!-- =========================
           CONFIRM
      ========================== -->

      <?php if (!empty($errors)): ?>
        <div class="form-error-summary" role="alert">
          <p>入力内容をご確認ください。</p>
          <ul>
            <?php foreach ($errors as $message): ?>
              <li><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <button
        class="confirm-button"
        id="confirmButton"
        type="submit"
      >
        ご 注 文 内 容 の 確 認
      </button>


      <a
        class="back-cart"
        href="cart.php"
      >
        ← カートへ戻る
      </a>

    </form>

  </main>


  <!-- =========================
       FOOTER
  ========================== -->

  <?php include __DIR__ . '/includes/footer.php'; ?>


  <script src="js/checkout.js"></script>

  <script src="js/common.js"></script>
</body>

</html>