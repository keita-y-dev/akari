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

    <!-- ==============================
         BREADCRUMB
    ============================== -->

    <div class="breadcrumb">

      <a href="index.php">
        TOP
      </a>

      <span>&gt;</span>

      <span>
        カート
      </span>

    </div>


    <!-- ==============================
         HEADING
    ============================== -->

    <section class="cart-heading">

      <p class="cart-heading__en">
        SHOPPING CART
      </p>

      <h1>
        カート
      </h1>

    </section>


    <!-- ==============================
         CART
    ============================== -->

    <section
      class="cart"
      id="cart"
    >


      <!-- ==============================
           CART ITEM
      ============================== -->

      <div
        class="cart-item"
        id="cartItem"
        data-product-id="1"
        data-price="2750"
      >

        <div class="cart-item__top">


          <!-- image -->

          <a
            href="product-detail.php?id=1"
            class="cart-item__image"
          >

            <img
              src="images/products/01_mug/mug_main.png"
              alt="木の持ち手のマグカップ"
            >

          </a>


          <!-- information -->

          <div class="cart-item__info">

            <p class="cart-item__category">
              KITCHEN
            </p>

            <h2>

              <a href="product-detail.php?id=1">
                木の持ち手のマグカップ
              </a>

            </h2>

            <p class="cart-item__price">

              ￥ 2,750

              <span>
                （税込）
              </span>

            </p>

          </div>

        </div>


        <!-- quantity -->

        <div class="cart-item__bottom">

          <p class="quantity__label">
            数量
          </p>

          <div class="quantity">

            <button
              class="quantity__button quantity__minus"
              type="button"
              aria-label="数量を1減らす"
            >
              −
            </button>

            <input
              class="quantity__input"
              type="number"
              value="1"
              min="1"
              max="10"
              aria-label="数量"
            >

            <button
              class="quantity__button quantity__plus"
              type="button"
              aria-label="数量を1増やす"
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


      <!-- ==============================
           PRICE
      ============================== -->

      <div class="cart-summary">


        <div class="summary-row">

          <p>
            小計
          </p>

          <p>
            ￥ <span id="subtotal">2,750</span>
          </p>

        </div>


        <div class="summary-row">

          <p>
            送料
          </p>

          <p>
            ￥ <span id="shipping">550</span>
          </p>

        </div>


        <div class="summary-total">

          <p>
            合計
          </p>

          <p>
            ￥ <span id="total">3,300</span>
          </p>

        </div>


        <!-- ==============================
             FREE SHIPPING
        ============================== -->

        <div class="free-shipping">

          <div class="free-shipping__text">

            <p id="freeShippingMessage">
              あと￥2,750で送料無料
            </p>

            <p>
              ￥5,500
            </p>

          </div>


          <div class="progress">

            <div
              class="progress__bar"
              id="progressBar"
            ></div>

          </div>

        </div>


        <!-- ==============================
             BUTTON
        ============================== -->

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


      <!-- ==============================
           GUIDE ACCORDION
      ============================== -->

      <div class="cart-guide">


        <!-- gift -->

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

            <span>
              ギフトラッピングについて
            </span>

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


        <!-- delivery -->

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

            <span>
              配送・返品について
            </span>

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


    <!-- ==============================
         EMPTY CART
    ============================== -->

    <section
      class="empty-cart"
      id="emptyCart"
    >

      <p>
        カートに商品がありません。
      </p>

      <a href="products.php">
        商品を見る
      </a>

    </section>


    <!-- ==============================
         YOU MAY ALSO LIKE
    ============================== -->

    <section class="recommend">

      <h2>
        YOU MAY ALSO LIKE
      </h2>


      <div class="recommend__slider">


        <!-- product 01 -->

        <a
          href="product-detail.php?id=5"
          class="recommend-card"
        >

          <div class="recommend-card__image">

            <img
              src="images/products/05_ceramic-plate/ceramic-plate_main.png"
              alt="陶器の小皿"
            >

          </div>

          <h3>
            陶器の小皿
          </h3>

          <div class="recommend-card__bottom">

            <p>

              ￥ 1,320

              <span>
                （税込）
              </span>

            </p>

            <span
              class="heart"
              aria-hidden="true"
            >
              ♡
            </span>

          </div>

        </a>


        <!-- product 02 -->

        <a
          href="product-detail.php?id=3"
          class="recommend-card"
        >

          <div class="recommend-card__image">

            <img
              src="images/products/03_linen-cloth/linen-cloth_main.png"
              alt="リネンクロス"
            >

          </div>

          <h3>
            リネンクロス
          </h3>

          <div class="recommend-card__bottom">

            <p>

              ￥ 1,650

              <span>
                （税込）
              </span>

            </p>

            <span
              class="heart"
              aria-hidden="true"
            >
              ♡
            </span>

          </div>

        </a>


        <!-- product 03 -->

        <a
          href="product-detail.php?id=6"
          class="recommend-card"
        >

          <div class="recommend-card__image">

            <img
              src="images/products/06_wood-tray/wood-tray_main.png"
              alt="木製トレー"
            >

          </div>

          <h3>
            木製トレー
          </h3>

          <div class="recommend-card__bottom">

            <p>

              ￥ 4,400

              <span>
                （税込）
              </span>

            </p>

            <span
              class="heart"
              aria-hidden="true"
            >
              ♡
            </span>

          </div>

        </a>

      </div>

    </section>

  </main>


  <?php include __DIR__ . '/includes/footer.php'; ?>


  <script src="js/cart.js"></script>
  <script src="js/common.js"></script>

</body>

</html>