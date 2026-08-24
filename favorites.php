<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/product-functions.php';

/*
 * お気に入りID自体はブラウザの localStorage に保存。
 * PHPでは表示候補となる全商品を取得し、JS側で絞り込みます。
 */
$products = fetchAllProductsWithImage($pdo);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>お気に入り | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/favorites.css">
</head>

<body>

  <?php include __DIR__ . '/includes/header.php'; ?>

  <main>

    <?php
      $breadcrumbs = [
        ['label' => 'TOP', 'url' => 'index'],
        ['label' => 'お気に入り', 'url' => null],
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?>

    <section class="favorites-heading">

      <p class="favorites-heading__en">
        FAVORITES
      </p>

      <h1>
        お気に入り
      </h1>

      <p class="favorites-heading__text">
        気になるものを、あとでゆっくり。
      </p>

    </section>

    <section
      class="favorites"
      aria-labelledby="favoritesCount"
    >

      <div class="favorites-toolbar">

        <p
          class="favorites-count"
          id="favoritesCount"
          aria-live="polite"
        >
          <span id="favoriteCount">0</span> ITEMS
        </p>

        <a
          class="favorites-shop-link"
          href="products"
        >
          商品一覧を見る →
        </a>

      </div>

      <div
        class="favorites-grid"
        id="favoritesGrid"
      >

        <?php foreach ($products as $product): ?>

          <?php
            $productCardOptions = [
              'showFavorite' => true,
              'headingTag' => 'h2',
              'extraClass' => 'favorite-product-card',
            ];

            include __DIR__ . '/includes/product-card.php';
          ?>

        <?php endforeach; ?>

      </div>

      <div
        class="favorites-empty"
        id="favoritesEmpty"
        hidden
      >

        <p class="favorites-empty__icon" aria-hidden="true">
          ♡
        </p>

        <h2>
          まだお気に入りの商品がありません
        </h2>

        <p>
          気になる商品のハートを押すと、<br>
          ここからいつでも見返すことができます。
        </p>

        <a
          class="favorites-empty__button"
          href="products"
        >
          商品を見つける
        </a>

      </div>

    </section>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="js/common.js"></script>
  <script src="js/favorites.js"></script>

</body>

</html>
