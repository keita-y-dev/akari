<?php

require_once __DIR__ . '/includes/db.php';

/*
 * 特集ページで使用する商品
 * SCENE 01: 1, 3
 * SCENE 02: 4, 2
 * SCENE 03: 6, 5
 */
$productIds = [1, 3, 4, 2, 6, 5];

$placeholders = implode(',', array_fill(0, count($productIds), '?'));

$sql = "
    SELECT
        p.id,
        p.name,
        p.price,
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

$sceneProducts = [
    1 => [1, 3],
    2 => [4, 2],
    3 => [6, 5],
];

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>特集 | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/journal.css">
</head>

<body>

  <!-- header -->
  <?php include __DIR__ . '/includes/header.php'; ?>


  <main>

    <?php
      $breadcrumbs = [
        ['label' => 'TOP', 'url' => 'index.php'],
        ['label' => '特集', 'url' => null],
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?>


    <!-- page heading -->
    <section class="feature-heading">

      <p class="section-label">JOURNAL</p>

      <h1>特集</h1>

    </section>


    <!-- journal hero -->
    <section class="journal-hero">

      <div class="journal-hero__image">
        <img
          src="images/journal/journal-main.jpg"
          alt="秋の夜、暖かな灯りのそばで過ごす時間"
        >
      </div>

      <div class="journal-hero__content">

        <p class="journal-title">
          AUTUMN JOURNAL
        </p>

        <h2>
          秋の夜を、心地よく。
        </h2>

        <p class="journal-lead">
          温かな飲み物とやわらかな灯りで、<br>
          一日の終わりに心ほどける時間を。
        </p>

        <time datetime="2026-09-01">
          2026.09.01
        </time>

      </div>

    </section>


    <!-- scene 01 -->
    <section class="scene">

      <div class="scene__heading">
        <p class="scene-number">SCENE 01</p>

        <h2>
          温かな飲み物を楽しむ
        </h2>
      </div>

      <div class="scene__image">
        <img
          src="images/journal/scene01.jpg"
          alt="秋の夜に温かな飲み物を楽しむ様子"
        >
      </div>

      <p class="scene__text">
        少し肌寒くなる秋の夜。お気に入りのマグカップに温かな飲み物を注いで、ゆっくり過ごしてみませんか。
      </p>


      <div class="product-list">

        <?php foreach ($sceneProducts[1] as $productId): ?>
          <?php if (!isset($productMap[$productId])) continue; ?>
          <?php
            $product = $productMap[$productId];
            $productCardOptions = [
              'showFavorite' => true,
              'headingTag' => 'h3',
            ];
            include __DIR__ . '/includes/product-card.php';
          ?>
        <?php endforeach; ?>

      </div>

      <a href="products.php" class="scene__link">
        商品を見る<span>→</span>
      </a>

    </section>


    <!-- scene 02 -->
    <section class="scene">

      <div class="scene__heading">
        <p class="scene-number">SCENE 02</p>

        <h2>
          小さな灯りをともす
        </h2>
      </div>

      <div class="scene__image">
        <img
          src="images/journal/scene02.jpg"
          alt="キャンドルの柔らかな灯り"
        >
      </div>

      <p class="scene__text">
        部屋の明かりを少し落として、キャンドルの柔らかな光を。いつもの部屋に、穏やかな時間が生まれます。
      </p>


      <div class="product-list">

        <?php foreach ($sceneProducts[2] as $productId): ?>
          <?php if (!isset($productMap[$productId])) continue; ?>
          <?php
            $product = $productMap[$productId];
            $productCardOptions = [
              'showFavorite' => true,
              'headingTag' => 'h3',
            ];
            include __DIR__ . '/includes/product-card.php';
          ?>
        <?php endforeach; ?>

      </div>

      <a href="products.php" class="scene__link">
        商品を見る<span>→</span>
      </a>

    </section>


    <!-- scene 03 -->
    <section class="scene">

      <div class="scene__heading">
        <p class="scene-number">SCENE 03</p>

        <h2>
          身の回りを静かに整える
        </h2>
      </div>

      <div class="scene__image">
        <img
          src="images/journal/scene03.jpg"
          alt="トレーに本や器を置いて整えた暮らしの風景"
        >
      </div>

      <p class="scene__text">
        読みかけの本やアクセサリーを、お気に入りのトレーへ。明日のために身の回りを整える、小さな習慣をご提案します。
      </p>


      <div class="product-list">

        <?php foreach ($sceneProducts[3] as $productId): ?>
          <?php if (!isset($productMap[$productId])) continue; ?>
          <?php
            $product = $productMap[$productId];
            $productCardOptions = [
              'showFavorite' => true,
              'headingTag' => 'h3',
            ];
            include __DIR__ . '/includes/product-card.php';
          ?>
        <?php endforeach; ?>

      </div>

      <a href="products.php" class="scene__link">
        商品を見る<span>→</span>
      </a>

    </section>


    <!-- closing -->
    <section class="feature-closing">

      <div class="feature-closing__image">
        <img
          src="images/journal/closing.jpg"
          alt="秋の夜を灯々の道具とともに過ごす風景"
        >
      </div>

      <div class="feature-closing__content">

        <h2>
          あたらしい秋の夜を、<br>
          灯々の道具とともに。
        </h2>

        <a href="products.php" class="closing-button">
          すべての商品を見る
        </a>

        <a href="about.php" class="closing-about">
          ABOUT 灯々<span>→</span>
        </a>

      </div>

    </section>

  </main>


  <!-- footer -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="js/common.js"></script>
</body>

</html>