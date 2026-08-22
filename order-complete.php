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

    <!-- =========================
         BREADCRUMB
    ========================== -->

    <div class="breadcrumb">

      <a href="index.php">
        TOP
      </a>

      <span>&gt;</span>

      <span>
        ご注文完了
      </span>

    </div>


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
            TT-20260728-001
          </dd>

        </div>


        <div class="order-info__row">

          <dt>
            注文日時
          </dt>

          <dd>
            2026年7月28日 10:30
          </dd>

        </div>


        <div class="order-info__row">

          <dt>
            ご請求金額
          </dt>

          <dd>
            ￥ 3,300（税込）
          </dd>

        </div>


        <div class="order-info__row">

          <dt>
            お支払方法
          </dt>

          <dd>
            クレジットカード
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
          山田 太郎 様
        </p>

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


      <dl class="delivery-date">

        <div class="delivery-date__row">

          <dt>
            お届け希望日
          </dt>

          <dd>
            指定なし
          </dd>

        </div>


        <div class="delivery-date__row">

          <dt>
            希望時間
          </dt>

          <dd>
            18:00 ~ 20:00
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
        href="index.php"
        class="top-button"
      >
        T O P ペ ー ジ へ 戻 る
      </a>


      <a
        href="index.php"
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
</body>

</html>