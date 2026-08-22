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
      novalidate
    >


      <!-- =========================
           ORDER ITEM
      ========================== -->

      <section class="checkout-section order-section">

        <h2>
          ご注文商品
        </h2>


        <div class="order-item">

          <div class="order-item__image">

            <img
              src="images/products/mug.png"
              alt="木の持ち手のマグカップ"
            >

          </div>


          <div class="order-item__info">

            <h3>
              木の持ち手のマグカップ
            </h3>

            <p>
              アイボリー × 1
            </p>

            <p class="order-item__price">
              ￥ 2,750
              <span>（税込）</span>
            </p>

          </div>

        </div>


        <div class="price-list">

          <div class="price-row">

            <p>小計</p>

            <p>
              ￥2,750
            </p>

          </div>


          <div class="price-row">

            <p>送料</p>

            <p>
              ￥550
            </p>

          </div>


          <div class="price-total">

            <p>
              合計（税込）
            </p>

            <p>
              ￥3,300
            </p>

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
              >

            </div>


            <div class="form-field">

              <input
                type="text"
                name="firstName"
                placeholder="名"
                required
              >

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
              >

            </div>


            <div class="form-field">

              <input
                type="text"
                name="firstNameKana"
                placeholder="メイ"
                required
              >

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
          >

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
          >

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
            >

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

            <option value="北海道">
              北海道
            </option>

            <option value="東京都">
              東京都
            </option>

            <option value="大阪府">
              大阪府
            </option>

            <option value="福岡県">
              福岡県
            </option>

            <option value="鹿児島県">
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
          >

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
          >

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

            <option value="2026-08-22">
              8月22日（土）
            </option>

            <option value="2026-08-23">
              8月23日（日）
            </option>

            <option value="2026-08-24">
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

            <option value="午前中">
              午前中
            </option>

            <option value="14-16">
              14:00〜16:00
            </option>

            <option value="16-18">
              16:00〜18:00
            </option>

            <option value="18-20">
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
              checked
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
          ></textarea>

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

          <p>
            小計
          </p>

          <p>
            ￥2,750
          </p>

        </div>


        <div class="billing-row">

          <p>
            送料
          </p>

          <p>
            ￥550
          </p>

        </div>


        <div class="billing-total">

          <p>
            合計（税込）
          </p>

          <p>
            ￥3,300
          </p>

        </div>

      </section>


      <!-- =========================
           AGREEMENT
      ========================== -->

      <label class="agreement">

        <input
          type="checkbox"
          id="agreement"
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

  <footer class="footer">
    <div class="footer__inner">
      <div class="footer__brand">
        <p class="footer__logo">灯々</p>
        <p>東京都杉並区x xx xx</p>
        <a href="mailto:info@akari.example">info@akari.example</a>
      </div>

      <div class="footer__accordion">
        <button class="footer__accordion-button" type="button" aria-expanded="false" aria-controls="footer-links">
          <span>サイトメニュー</span>
          <span class="footer__accordion-icon" aria-hidden="true">＋</span>
        </button>

        <nav class="footer__nav" id="footer-links" aria-label="フッターナビゲーション">
          <ul>
            <li><a href="#">ご利用ガイド</a></li>
            <li><a href="#">サポート</a></li>
            <li><a href="about.php">灯々について</a></li>
            <li><a href="#">規約・ポリシー</a></li>
          </ul>
        </nav>
      </div>

      <p class="footer__copy">© 2026 AKARI.inc</p>
    </div>
  </footer>


  <script src="js/checkout.js"></script>

  <script src="js/common.js"></script>
</body>

</html>