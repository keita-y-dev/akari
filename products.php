<?php

require_once __DIR__ . '/includes/db.php';

/*
 * 商品一覧を取得
 * product_images の sort_order = 1 を代表画像として使用
 */
$sql = "
  SELECT
    p.id,
    p.name,
    p.price,
    p.description,
    c.name AS category_name,
    pi.image_path
  FROM products AS p
  INNER JOIN categories AS c
    ON p.category_id = c.id
  LEFT JOIN product_images AS pi
    ON p.id = pi.product_id
    AND pi.sort_order = 1
  ORDER BY p.id ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();

/*
 * カテゴリ名を data-category 用に変換
 */
$categoryMap = [
  'キッチン' => 'kitchen',
  'インテリア' => 'interior',
  'ファブリック' => 'fabric',
  'アロマ' => 'aroma',
];

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>商品一覧 | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/products.css">
</head>

<body>

  <?php include __DIR__ . '/includes/header.php'; ?>

  <main>

    <?php
      $breadcrumbs = [
        ['label' => 'TOP', 'url' => 'index.php'],
        ['label' => '商品一覧', 'url' => null],
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?>

    <section class="products-heading">

      <p class="section-label">SHOP</p>

      <h1>商品一覧</h1>

    </section>

    <section class="products-intro">

      <p>
        暮らしに小さな灯りをともす、<br>
        日々の道具を集めました。
      </p>

    </section>

    <nav class="category-nav" aria-label="商品カテゴリー">

      <button
        class="category-tab is-active"
        type="button"
        data-category="all"
      >
        すべて
      </button>

      <?php foreach ($categoryMap as $categoryKey => $categoryValue): ?>

        <button
          class="category-tab"
          type="button"
          data-category="<?= htmlspecialchars($categoryValue, ENT_QUOTES, 'UTF-8') ?>"
        >
          <?= htmlspecialchars($categoryKey, ENT_QUOTES, 'UTF-8') ?>
        </button>

      <?php endforeach; ?>

    </nav>

    <section class="products">

      <div class="products-toolbar">

        <p class="item-count">
          <span id="itemCount"><?= count($products) ?></span> ITEMS
        </p>

        <label class="sort-label">

          <span class="sort-text">
            並び替え
          </span>

          <select id="sortSelect" class="sort-select">

            <option value="recommended">
              おすすめ順
            </option>

            <option value="price-low">
              価格の安い順
            </option>

            <option value="price-high">
              価格の高い順
            </option>

          </select>

        </label>

      </div>

      <div class="product-grid" id="productGrid">

        <?php foreach ($products as $index => $product): ?>

          <?php
            $category = $categoryMap[$product['category_name']] ?? '';

            $productCardOptions = [
              'showFavorite' => true,
              'headingTag' => 'h2',
              'category' => $category,
              'price' => (int)$product['price'],
              'order' => $index + 1,
            ];

            include __DIR__ . '/includes/product-card.php';
          ?>

        <?php endforeach; ?>

      </div>

      <p
        class="no-result"
        id="noResult"
      >
        該当する商品がありません。
      </p>

      <?php if (count($products) > 6): ?>

        <button
          class="more-button"
          type="button"
        >
          もっと見る
        </button>

      <?php endif; ?>

    </section>

    <section class="products-about">

      <a
        class="products-about__link"
        href="about.php"
        aria-label="灯々について"
      >
        <img
          class="products-about__image"
          src="images/products/products-about.jpg"
          alt="暮らしになじむものをひとつずつ丁寧に。ABOUT 灯々"
        >
      </a>

    </section>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="js/products.js"></script>
  <script src="js/common.js"></script>

</body>

</html>