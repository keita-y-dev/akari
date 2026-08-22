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
        ご注文内容確認
      </span>

    </div>


    <!-- =========================
         HEADING
    ========================== -->

    <section class="page-heading">

      <p class="page-heading__en">
        ORDER CONFIRMATION
      </p>

      <h1>
        ご注文内容の確認
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


    <!-- =========================
         DESCRIPTION
    ========================== -->

    <div class="confirmation-description">

      <p>
        入力内容をご確認ください。
      </p>

      <p>
        修正する場合は、各項目の「変更する」を押してください。
      </p>

    </div>


    <!-- =========================
         CONFIRMATION
    ========================== -->

    <div class="confirmation">


      <!-- =========================
           ORDER ITEM
      ========================== -->

      <section class="confirm-section">

        <div class="section-heading">

          <h2>
            ご注文商品
          </h2>

          <button
            class="change-button"
            type="button"
            data-change="input"
          >
            変更する
            <span>&gt;</span>
          </button>

        </div>


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
              ￥ 2,750（税込）
            </p>

            <p>
              アイボリー × 1
            </p>

          </div>

        </div>


        <div class="price-list">

          <div class="price-row">

            <p>
              小計
            </p>

            <p>
              ￥2,750
            </p>

          </div>


          <div class="price-row">

            <p>
              送料
            </p>

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

      <section class="confirm-section">

        <div class="section-heading">

          <h2>
            お客様情報
          </h2>

          <button
            class="change-button"
            type="button"
            data-change="input"
          >
            変更する
            <span>&gt;</span>
          </button>

        </div>


        <dl class="confirm-list">

          <div class="confirm-row">

            <dt>
              お名前
            </dt>

            <dd>
              山田 太郎
            </dd>

          </div>


          <div class="confirm-row">

            <dt>
              フリガナ
            </dt>

            <dd>
              ヤマダ タロウ
            </dd>

          </div>


          <div class="confirm-row">

            <dt>
              メールアドレス
            </dt>

            <dd>
              example@email.com
            </dd>

          </div>


          <div class="confirm-row">

            <dt>
              電話番号
            </dt>

            <dd>
              090-1234-5678
            </dd>

          </div>

        </dl>

      </section>


      <!-- =========================
           DELIVERY ADDRESS
      ========================== -->

      <section class="confirm-section">

        <div class="section-heading">

          <h2>
            配送先
          </h2>

          <button
            class="change-button"
            type="button"
            data-change="input"
          >
            変更する
            <span>&gt;</span>
          </button>

        </div>


        <div class="address">

          <p>
            〒 123-4567
          </p>

          <p>
            東京都〇〇区〇〇1-2-3
          </p>

          <p>
            〇〇マンション102号室
          </p>

        </div>

      </section>


      <!-- =========================
           DELIVERY
      ========================== -->

      <section class="confirm-section">

        <div class="section-heading">

          <h2>
            配送方法・希望日時
          </h2>

          <button
            class="change-button"
            type="button"
            data-change="input"
          >
            変更する
            <span>&gt;</span>
          </button>

        </div>


        <dl class="confirm-list">

          <div class="confirm-row">

            <dt>
              配送方法
            </dt>

            <dd>
              通常配達
            </dd>

          </div>


          <div class="confirm-row">

            <dt>
              お届け希望日
            </dt>

            <dd>
              指定なし
            </dd>

          </div>


          <div class="confirm-row">

            <dt>
              お届け希望時間
            </dt>

            <dd>
              18:00 ~ 20:00
            </dd>

          </div>

        </dl>

      </section>


      <!-- =========================
           PAYMENT
      ========================== -->

      <section class="confirm-section">

        <div class="section-heading">

          <h2>
            お支払い方法
          </h2>

          <button
            class="change-button"
            type="button"
            data-change="input"
          >
            変更する
            <span>&gt;</span>
          </button>

        </div>


        <dl class="confirm-list">

          <div class="confirm-row">

            <dt>
              お支払い方法
            </dt>

            <dd>
              クレジットカード
            </dd>

          </div>


          <div class="confirm-row">

            <dt>
              カード番号
            </dt>

            <dd>
              **** **** **** 1234
              <small>
                ※カード情報は一部のみ表示します
              </small>
            </dd>

          </div>

        </dl>

      </section>


      <!-- =========================
           GIFT
      ========================== -->

      <section class="confirm-section">

        <div class="section-heading">

          <h2>
            ギフト・備考
          </h2>

          <button
            class="change-button"
            type="button"
            data-change="input"
          >
            変更する
            <span>&gt;</span>
          </button>

        </div>


        <dl class="confirm-list">

          <div class="confirm-row">

            <dt>
              ギフトラッピング
            </dt>

            <dd>
              希望しない
            </dd>

          </div>


          <div class="confirm-row">

            <dt>
              備考
            </dt>

            <dd>
              なし
            </dd>

          </div>

        </dl>

      </section>


      <!-- =========================
           BILLING
      ========================== -->

      <section class="confirm-section billing-section">

        <div class="section-heading">

          <h2>
            ご請求金額
          </h2>

          <button
            class="change-button"
            type="button"
            data-change="input"
          >
            変更する
            <span>&gt;</span>
          </button>

        </div>


        <div class="billing">

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

        </div>

      </section>


      <!-- =========================
           ORDER BUTTON
      ========================== -->

      <section class="order-action">

        <button
          class="order-button"
          id="orderButton"
          type="button"
        >
          注 文 を 確 定 す る
        </button>


        <button
          class="back-button"
          id="backButton"
          type="button"
        >
          ←入力内容の修正に戻る
        </button>


        <p class="order-note">
          ※ボタンを押すと注文が確定します。
        </p>

      </section>

    </div>


    <!-- =========================
         COMPLETE
    ========================== -->

    <section
      class="complete"
      id="complete"
      aria-hidden="true"
    >

      <p class="complete__en">
        ORDER COMPLETE
      </p>

      <h2>
        ご注文ありがとうございます
      </h2>

      <p class="complete__text">
        ご注文を受け付けました。
        <br>
        ご登録いただいたメールアドレスへ
        <br>
        確認メールをお送りします。
      </p>

      <a
        class="complete-button"
        href="index.php"
      >
        TOPへ戻る
      </a>

    </section>

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


  <script src="js/order-confirm.js"></script>

  <script src="js/common.js"></script>
</body>

</html>