<?php

session_start();

require_once __DIR__ . '/includes/db.php';

/*
 * CSRFトークン
 */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/*
 * 商品ID
 */
$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    header('Location: products.php');
    exit;
}


/*
 * 商品基本情報
 */
$stmt = $pdo->prepare("
    SELECT
        p.*,
        c.name AS category_name
    FROM products AS p
    INNER JOIN categories AS c
        ON c.id = p.category_id
    WHERE p.id = ?
");

$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}


/*
 * 商品画像
 */
$stmt = $pdo->prepare("
    SELECT image_path
    FROM product_images
    WHERE product_id = ?
    ORDER BY sort_order ASC
");

$stmt->execute([$productId]);
$images = $stmt->fetchAll();


/*
 * 商品詳細
 */
$stmt = $pdo->prepare("
    SELECT
        title,
        content
    FROM product_details
    WHERE product_id = ?
    ORDER BY sort_order ASC
");

$stmt->execute([$productId]);
$details = $stmt->fetchAll();


/*
 * 商品ストーリー
 */
$stmt = $pdo->prepare("
    SELECT
        title,
        content,
        image_path
    FROM product_stories
    WHERE product_id = ?
    ORDER BY sort_order ASC
    LIMIT 1
");

$stmt->execute([$productId]);
$story = $stmt->fetch();


/*
 * おすすめ商品
 *
 * 同じカテゴリの商品から最大3件
 */
$stmt = $pdo->prepare("
    SELECT
        p.id,
        p.name,
        p.price,
        pi.image_path
    FROM products AS p
    LEFT JOIN product_images AS pi
        ON pi.product_id = p.id
        AND pi.sort_order = 1
    WHERE
        p.category_id = ?
        AND p.id != ?
    ORDER BY p.id ASC
    LIMIT 3
");

$stmt->execute([
    $product['category_id'],
    $productId
]);

$recommendProducts = $stmt->fetchAll();


/*
 * メイン画像
 */
$mainImage = $images[0]['image_path'] ?? 'images/products/no-image.png';


/*
 * カテゴリ英語表記
 */
$categoryMap = [
    'キッチン' => 'KITCHEN',
    'インテリア' => 'INTERIOR',
    'ファブリック' => 'FABRIC',
    'アロマ' => 'AROMA',
];

$categoryLabel =
    $categoryMap[$product['category_name']]
    ?? $product['category_name'];

?>

<!DOCTYPE html>
<html lang="ja">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>
    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> | 灯々
  </title>

  <link
    rel="preconnect"
    href="https://fonts.googleapis.com"
  >

  <link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin
  >

  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link
    rel="stylesheet"
    href="css/reset.css"
  >

  <link
    rel="stylesheet"
    href="css/common.css"
  >

  <link
    rel="stylesheet"
    href="css/product-detail.css"
  >

</head>

<body>

  <?php include __DIR__ . '/includes/header.php'; ?>


  <main>

    <!-- breadcrumb -->

    <div class="breadcrumb">

      <a href="index.php">
        TOP
      </a>

      <span>&gt;</span>

      <a href="products.php">
        <?= htmlspecialchars(
            $categoryLabel,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
      </a>

      <span>&gt;</span>

      <span>
        <?= htmlspecialchars(
            $product['name'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>
      </span>

    </div>


    <!-- product -->

    <section class="product">

      <div class="product-gallery">

        <button
          class="product-gallery__main"
          type="button"
          aria-label="画像を拡大する"
        >

          <img
            id="mainImage"
            src="<?= htmlspecialchars(
                $mainImage,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            alt="<?= htmlspecialchars(
                $product['name'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
          >

          <span class="zoom-icon">
            ＋
          </span>

        </button>


        <?php if ($images): ?>

          <div class="product-gallery__thumbs">

            <?php foreach ($images as $i => $image): ?>

              <button
                class="gallery-thumb<?= $i === 0 ? ' is-active' : '' ?>"
                type="button"
                data-alt="<?= htmlspecialchars(
                    $product['name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
              >

                <img
                  src="<?= htmlspecialchars(
                      $image['image_path'],
                      ENT_QUOTES,
                      'UTF-8'
                  ) ?>"
                  alt=""
                >

              </button>

            <?php endforeach; ?>

          </div>

        <?php endif; ?>

      </div>


      <!-- product information -->

      <div class="product-info">

        <p class="product-info__category">

          <?= htmlspecialchars(
              $categoryLabel,
              ENT_QUOTES,
              'UTF-8'
          ) ?>

        </p>


        <div class="product-info__heading">

          <h1 class="product-info__title">

            <?= htmlspecialchars(
                $product['name'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>

          </h1>


          <button
            class="favorite-button"
            type="button"
            aria-label="お気に入りに追加"
            aria-pressed="false"
          >

            <img
              src="images/icons/heart-outline.svg"
              alt=""
              width="22"
              height="22"
            >

          </button>

        </div>


        <p class="product-info__price">

          ￥<?= number_format(
              (int) $product['price']
          ) ?>

          <span>
            （税込）
          </span>

        </p>


        <?php if (!empty($product['description'])): ?>

          <p class="product-info__description">

            <?= htmlspecialchars(
                $product['description'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>

          </p>

        <?php endif; ?>


        <p class="product-stock">

          <?php if ((int) $product['stock'] > 0): ?>

            在庫あり

          <?php else: ?>

            在庫切れ

          <?php endif; ?>

        </p>


        <div class="quantity">

          <p class="quantity__title">
            数量
          </p>

          <div class="quantity__control">

            <button
              id="quantityMinus"
              class="quantity__button"
              type="button"
              aria-label="数量を1減らす"
            >
              −
            </button>

            <input
              id="quantityInput"
              class="quantity__input"
              type="number"
              value="1"
              min="1"
              max="<?= max(1, (int) $product['stock']) ?>"
              aria-label="数量"
              <?= (int) $product['stock'] <= 0 ? 'disabled' : '' ?>
            >

            <button
              id="quantityPlus"
              class="quantity__button"
              type="button"
              aria-label="数量を1増やす"
              <?= (int) $product['stock'] <= 0 ? 'disabled' : '' ?>
            >
              ＋
            </button>

          </div>

        </div>


        <button
          class="cart-button"
          type="button"
          data-product-id="<?= (int) $product['id'] ?>"
          <?= (int) $product['stock'] <= 0 ? 'disabled' : '' ?>
        >

          <?= (int) $product['stock'] > 0
              ? 'カートに入れる'
              : '在庫切れ'
          ?>

        </button>

      </div>

    </section>


    <!-- delivery -->

    <section class="delivery">

      <p class="section-label">
        DELIVERY
      </p>

      <ul class="delivery__list">

        <li>
          3営業日以内に発送
        </li>

        <li>
          全国一律 550円
        </li>

        <li>
          5,500円以上で送料無料
        </li>

        <li>
          ギフト対応
        </li>

      </ul>

    </section>


    <!-- product information -->

    <?php if ($details): ?>

      <section class="product-information">

        <p class="section-label">
          PRODUCT INFORMATION
        </p>

        <h2>
          商品について
        </h2>


        <?php foreach ($details as $detail): ?>

          <div class="accordion">

            <button
              class="accordion__button"
              type="button"
              aria-expanded="false"
            >

              <span>
                <?= htmlspecialchars(
                    $detail['title'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
              </span>

              <span class="accordion__icon">
                ＋
              </span>

            </button>

            <div class="accordion__content">

              <div class="accordion__inner">

                <?php
                  $lines = preg_split(
                      '/\r\n|\r|\n/',
                      $detail['content']
                  );
                ?>

                <?php foreach ($lines as $line): ?>

                  <?php if (trim($line) !== ''): ?>

                    <p>
                      <?= htmlspecialchars(
                          $line,
                          ENT_QUOTES,
                          'UTF-8'
                      ) ?>
                    </p>

                  <?php endif; ?>

                <?php endforeach; ?>

              </div>

            </div>

          </div>

        <?php endforeach; ?>

      </section>

    <?php endif; ?>


    <!-- story -->

    <?php if ($story): ?>

      <section class="story">

        <p class="section-label">
          STORY
        </p>


        <?php if (!empty($story['image_path'])): ?>

          <div class="story__image">

            <img
              src="<?= htmlspecialchars(
                  $story['image_path'],
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>"
              alt="<?= htmlspecialchars(
                  $product['name'],
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>のストーリー"
            >

          </div>

        <?php endif; ?>


        <div class="story__content">

          <h2>
            <?= htmlspecialchars(
                $story['title'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
          </h2>


          <?php
            $paragraphs = preg_split(
                '/\r?\n\r?\n+/',
                $story['content']
            );
          ?>

          <?php foreach ($paragraphs as $paragraph): ?>

            <?php if (trim($paragraph) !== ''): ?>

              <p>
                <?= nl2br(
                    htmlspecialchars(
                        trim($paragraph),
                        ENT_QUOTES,
                        'UTF-8'
                    )
                ) ?>
              </p>

            <?php endif; ?>

          <?php endforeach; ?>

        </div>

      </section>

    <?php endif; ?>


    <!-- recommend -->

    <?php if ($recommendProducts): ?>

      <section class="recommend">

        <p class="section-label">
          YOU MAY ALSO LIKE
        </p>


        <div class="recommend__grid">

          <?php foreach ($recommendProducts as $recommend): ?>
            <?php
              $recommendCardOptions = [
                'showFavorite' => false,
              ];
              include __DIR__ . '/includes/recomend-card.php';
            ?>
          <?php endforeach; ?>

        </div>

      </section>

    <?php endif; ?>

  </main>


  <!-- image modal -->

  <div
    class="image-modal"
    id="imageModal"
    aria-hidden="true"
  >

    <div class="image-modal__overlay"></div>

    <div class="image-modal__content">

      <button
        id="modalClose"
        class="image-modal__close"
        type="button"
        aria-label="閉じる"
      >
        ×
      </button>

      <img
        id="modalImage"
        src=""
        alt=""
      >

    </div>

  </div>


  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script>
    window.AKARI_PRODUCT = {
      productId: <?= (int)$product['id'] ?>,
      csrfToken: <?= json_encode(
          $_SESSION['csrf_token'],
          JSON_UNESCAPED_UNICODE
      ) ?>
    };
  </script>

  <script src="js/product-detail.js"></script>
  <script src="js/common.js"></script>

</body>

</html>