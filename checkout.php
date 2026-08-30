<?php

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/cart-functions.php';
require_once __DIR__ . '/includes/helpers.php';

if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header('Location: cart');
    exit;
}

$cartItems = fetchCartItems($pdo, $_SESSION['cart'], true);

if (empty($cartItems)) {
    header('Location: cart');
    exit;
}

$summary = calculateCartSummary($cartItems);
$subtotal = $summary['subtotal'];
$shipping = $summary['shipping'];
$total = $summary['total'];

$prefectures = [
    '北海道',
    '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
    '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
    '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県',
    '岐阜県', '静岡県', '愛知県', '三重県',
    '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
    '鳥取県', '島根県', '岡山県', '広島県', '山口県',
    '徳島県', '香川県', '愛媛県', '高知県',
    '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県',
];

$deliveryTimeOptions = [
    '午前中' => '午前中',
    '14-16' => '14:00〜16:00',
    '16-18' => '16:00〜18:00',
    '18-20' => '18:00〜20:00',
];

$weekdayLabels = ['日', '月', '火', '水', '木', '金', '土'];

/*
 * お届け希望日
 * 「3営業日以内に発送」を考慮し、3日後から7日分を表示。
 * 毎回現在日から生成するため、過去日は候補に入りません。
 */
$deliveryDateOptions = [];
$today = new DateTimeImmutable('today');

for ($i = 3; $i <= 9; $i++) {
    $date = $today->modify("+{$i} days");
    $value = $date->format('Y-m-d');
    $weekday = $weekdayLabels[(int)$date->format('w')];

    $deliveryDateOptions[$value] =
        $date->format('n月j日') . "（{$weekday}）";
}

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

    if (!in_array($form['prefecture'], $prefectures, true)) {
        $errors['prefecture'] = '都道府県を選択してください。';
    }

    if (!in_array($form['delivery'], ['normal'], true)) {
        $errors['delivery'] = '配送方法が正しくありません。';
    }

    if (
        $form['deliveryDate'] !== '' &&
        !array_key_exists($form['deliveryDate'], $deliveryDateOptions)
    ) {
        $errors['deliveryDate'] = 'お届け希望日を選び直してください。';
    }

    if (
        $form['deliveryTime'] !== '' &&
        !array_key_exists($form['deliveryTime'], $deliveryTimeOptions)
    ) {
        $errors['deliveryTime'] = 'お届け希望時間を選び直してください。';
    }

    if (!in_array($form['payment'], ['card', 'cod'], true)) {
        $errors['payment'] = 'お支払い方法が正しくありません。';
    }

    $maxLengths = [
        'lastName' => 100,
        'firstName' => 100,
        'lastNameKana' => 100,
        'firstNameKana' => 100,
        'email' => 255,
        'phone' => 30,
        'postal' => 10,
        'address' => 255,
        'building' => 255,
        'note' => 1000,
    ];

    foreach ($maxLengths as $key => $maxLength) {
        if (mb_strlen((string)$form[$key]) > $maxLength) {
            $errors[$key] = '入力文字数が上限を超えています。';
        }
    }

    if (!$form['agreement']) {
        $errors['agreement'] = '利用規約とプライバシーポリシーへの同意が必要です。';
    }

    if (empty($errors)) {
        $_SESSION['checkout'] = $form;
        header('Location: order-confirm');
        exit;
    }
}

function old(array $form, string $key): string
{
    return h($form[$key] ?? '');
}

function hasError(array $errors, string $key): bool
{
    return isset($errors[$key]);
}

function errorMessage(array $errors, string $key): string
{
    return h($errors[$key] ?? '');
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="icon" href="images/logos/favicon.svg" type="image/svg+xml">

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

    <?php
      $breadcrumbs = [
        ['label' => 'TOP', 'url' => 'index'],
        ['label' => 'カート', 'url' => 'cart'],
        ['label' => 'ご購入手続き', 'url' => null],
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?>


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
      action="checkout"
      method="post"
    >
      <input type="hidden" name="csrf_token"
        value="<?= h($_SESSION['checkout_csrf_token']) ?>">


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
                  src="<?= h($item['image_path']) ?>"
                  alt="<?= h($item['name']) ?>"
                >
              <?php endif; ?>

            </div>

            <div class="order-item__info">

              <h3>
                <?= h($item['name']) ?>
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

              <input class="<?= hasError($errors, 'lastName') ? 'form-error' : '' ?>"
                type="text"
                name="lastName"
                autocomplete="family-name"
                maxlength="100"
                placeholder="姓"
                required
               value="<?= old($form, 'lastName') ?>">

              <?php if (hasError($errors, 'lastName')): ?>
                <p class="error-message"><?= errorMessage($errors, 'lastName') ?></p>
              <?php endif; ?>

            </div>


            <div class="form-field">

              <input class="<?= hasError($errors, 'firstName') ? 'form-error' : '' ?>"
                type="text"
                name="firstName"
                autocomplete="given-name"
                maxlength="100"
                placeholder="名"
                required
               value="<?= old($form, 'firstName') ?>">

              <?php if (hasError($errors, 'firstName')): ?>
                <p class="error-message"><?= errorMessage($errors, 'firstName') ?></p>
              <?php endif; ?>

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

              <input class="<?= hasError($errors, 'lastNameKana') ? 'form-error' : '' ?>"
                type="text"
                name="lastNameKana"
                maxlength="100"
                pattern="[ァ-ヶー　 ]+"
                placeholder="セイ"
                required
               value="<?= old($form, 'lastNameKana') ?>">

              <?php if (hasError($errors, 'lastNameKana')): ?>
                <p class="error-message"><?= errorMessage($errors, 'lastNameKana') ?></p>
              <?php endif; ?>

            </div>


            <div class="form-field">

              <input class="<?= hasError($errors, 'firstNameKana') ? 'form-error' : '' ?>"
                type="text"
                name="firstNameKana"
                maxlength="100"
                pattern="[ァ-ヶー　 ]+"
                placeholder="メイ"
                required
               value="<?= old($form, 'firstNameKana') ?>">

              <?php if (hasError($errors, 'firstNameKana')): ?>
                <p class="error-message"><?= errorMessage($errors, 'firstNameKana') ?></p>
              <?php endif; ?>

            </div>

          </div>

        </div>


        <!-- email -->

        <div class="form-group">

          <label for="email">
            メールアドレス
            <span class="required">必須</span>
          </label>

          <input class="<?= hasError($errors, 'email') ? 'form-error' : '' ?>"
            id="email"
            type="email"
            name="email"
              autocomplete="email"
              maxlength="255"
            placeholder="example@email.com"
            required
           value="<?= old($form, 'email') ?>">

          <?php if (hasError($errors, 'email')): ?>
            <p class="error-message"><?= errorMessage($errors, 'email') ?></p>
          <?php endif; ?>

        </div>


        <!-- phone -->

        <div class="form-group">

          <label for="phone">
            電話番号
            <span class="required">必須</span>
          </label>

          <input class="<?= hasError($errors, 'phone') ? 'form-error' : '' ?>"
            id="phone"
            type="tel"
            name="phone"
              autocomplete="tel"
              inputmode="tel"
              maxlength="14"
            placeholder="09012345678"
            required
           value="<?= old($form, 'phone') ?>">

          <?php if (hasError($errors, 'phone')): ?>
            <p class="error-message"><?= errorMessage($errors, 'phone') ?></p>
          <?php endif; ?>

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

            <input class="<?= hasError($errors, 'postal') ? 'form-error' : '' ?>"
              id="postal"
              type="text"
              name="postal"
                autocomplete="postal-code"
                inputmode="numeric"
                pattern="\d{3}-?\d{4}"
              placeholder="123-4567"
              maxlength="8"
              required
             value="<?= old($form, 'postal') ?>">

            <?php if (hasError($errors, 'postal')): ?>
              <p class="error-message postal-error"><?= errorMessage($errors, 'postal') ?></p>
            <?php endif; ?>

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

          <select class="<?= hasError($errors, 'prefecture') ? 'form-error' : '' ?>"
            id="prefecture"
            name="prefecture"
            autocomplete="address-level1"
            required
          >

            <option value="">
              選択してください
            </option>

            <?php foreach ($prefectures as $prefecture): ?>
              <option
                value="<?= h($prefecture) ?>"
                <?= (($form['prefecture'] ?? '') === $prefecture) ? 'selected' : '' ?>
              >
                <?= h($prefecture) ?>
              </option>
            <?php endforeach; ?>

          </select>
          <?php if (hasError($errors, 'prefecture')): ?>
            <p class="error-message"><?= errorMessage($errors, 'prefecture') ?></p>
          <?php endif; ?>

        </div>


        <!-- address -->

        <div class="form-group">

          <label for="address">
            市町村・番地
            <span class="required">必須</span>
          </label>

          <input class="<?= hasError($errors, 'address') ? 'form-error' : '' ?>"
            id="address"
            type="text"
            name="address"
              autocomplete="address-level2"
              maxlength="255"
            required
           value="<?= old($form, 'address') ?>">

          <?php if (hasError($errors, 'address')): ?>
            <p class="error-message"><?= errorMessage($errors, 'address') ?></p>
          <?php endif; ?>

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
            autocomplete="address-line2"
            maxlength="255"
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

          <select class="<?= hasError($errors, 'deliveryDate') ? 'form-error' : '' ?>"
            id="deliveryDate"
            name="deliveryDate"
          >

            <option value="">
              指定なし
            </option>

            <?php foreach ($deliveryDateOptions as $value => $label): ?>
              <option
                value="<?= h($value) ?>"
                <?= (($form['deliveryDate'] ?? '') === $value) ? 'selected' : '' ?>
              >
                <?= h($label) ?>
              </option>
            <?php endforeach; ?>

          </select>
          <?php if (hasError($errors, 'deliveryDate')): ?>
            <p class="error-message"><?= errorMessage($errors, 'deliveryDate') ?></p>
          <?php endif; ?>

        </div>


        <!-- time -->

        <div class="form-group">

          <label for="deliveryTime">
            お届け先希望時間
            <span class="optional">任意</span>
          </label>

          <select class="<?= hasError($errors, 'deliveryTime') ? 'form-error' : '' ?>"
            id="deliveryTime"
            name="deliveryTime"
          >

            <option value="">
              指定なし
            </option>

            <?php foreach ($deliveryTimeOptions as $value => $label): ?>
              <option
                value="<?= h($value) ?>"
                <?= (($form['deliveryTime'] ?? '') === $value) ? 'selected' : '' ?>
              >
                <?= h($label) ?>
              </option>
            <?php endforeach; ?>

          </select>
          <?php if (hasError($errors, 'deliveryTime')): ?>
            <p class="error-message"><?= errorMessage($errors, 'deliveryTime') ?></p>
          <?php endif; ?>

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
              autocomplete="off"
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
              autocomplete="off"
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
              autocomplete="off"
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
            maxlength="1000"
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

      <?php if (hasError($errors, 'agreement')): ?>
        <p class="error-message agreement-error">
          <?= errorMessage($errors, 'agreement') ?>
        </p>
      <?php endif; ?>


      <!-- =========================
           CONFIRM
      ========================== -->

      <?php if (!empty($errors)): ?>
        <div class="form-error-summary" role="alert">
          <p>入力内容をご確認ください。</p>
          <ul>
            <?php foreach ($errors as $message): ?>
              <li><?= h($message) ?></li>
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
        href="cart"
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