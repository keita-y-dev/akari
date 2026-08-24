<?php

require_once __DIR__ . '/includes/db.php';

/*
 * NEW ARRIVAL
 * 登録日時の新しい商品から4件取得
 */
$newArrivalSql = "
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
    ORDER BY p.created_at DESC, p.id DESC
    LIMIT 4
";

$newArrivalStmt = $pdo->query($newArrivalSql);
$newArrivals = $newArrivalStmt->fetchAll();


/*
 * BEST SELLER
 *
 * 総合: 現在のランキング
 * 新着: created_at の新しい順
 * ギフト: ギフト向けとして選んだ商品
 */
$rankingType = (string)($_GET['ranking'] ?? 'overall');

$rankingOptions = [
    'overall' => '総合',
    'new'     => '新着',
    'gift'    => 'ギフト',
];

if (!array_key_exists($rankingType, $rankingOptions)) {
    $rankingType = 'overall';
}

function fetchProductsByIds(PDO $pdo, array $ids): array
{
    if (empty($ids)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

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
    $stmt->execute($ids);

    $map = [];

    foreach ($stmt->fetchAll() as $row) {
        $map[(int)$row['id']] = $row;
    }

    $products = [];

    foreach ($ids as $id) {
        if (isset($map[$id])) {
            $products[] = $map[$id];
        }
    }

    return $products;
}

switch ($rankingType) {
    case 'new':
        $rankingSql = "
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
            ORDER BY p.created_at DESC, p.id DESC
            LIMIT 3
        ";

        $bestSellers = $pdo->query($rankingSql)->fetchAll();
        break;

    case 'gift':
        /*
         * ギフト向け商品は現時点では手動セレクト。
         * 商品属性をDB化したくなった場合は後から置き換え可能。
         */
        $giftProductIds = [1, 5, 6];
        $bestSellers = fetchProductsByIds($pdo, $giftProductIds);
        break;

    case 'overall':
    default:
        $bestSellerIds = [1, 3, 6];
        $bestSellers = fetchProductsByIds($pdo, $bestSellerIds);
        break;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="暮らしに、小さな灯りを。生活雑貨店「灯々」のオンラインショップです。">
  <title>灯々 | 暮らしに、小さな灯りを。</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/top.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main>
    <section class="hero">
      <img class="hero__image" src="images/top/hero.png" alt="花瓶とマグカップのある静かな暮らしの風景">
      <div class="hero__content">
        <h1 class="hero__title">暮らしに、<br>小さな灯りを。</h1>
        <p class="hero__text">毎日の風景になじむ、<br>長く愛せる暮らしの道具を集めました。</p>
        <a class="button button--hero" href="products">商品を見る</a>
      </div>
    </section>

    <section class="section new-arrival" id="new-arrival">
      <div class="section__inner">
        <h2 class="section-title">NEW ARRIVAL</h2>

        <div class="product-grid">
          <?php foreach ($newArrivals as $product): ?>
            <?php
              $productCardOptions = [
                'showFavorite' => true,
                'rank' => null,
              ];
              include __DIR__ . '/includes/product-card.php';
            ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section category">
      <div class="section__inner">
        <h2 class="section-title">CATEGORY</h2>

        <div class="category-grid">
          <article class="category-card">
            <img class="category-card__image" src="images/category/kitchen.png" alt="Kitchenカテゴリーのイラスト">
            <h3 class="category-card__title">Kitchen</h3>
            <ul class="category-card__list">
              <li>マグカップ</li>
              <li>プレート</li>
              <li>カトラリー</li>
            </ul>
          </article>

          <article class="category-card">
            <img class="category-card__image" src="images/category/interior.png" alt="Interiorカテゴリーのイラスト">
            <h3 class="category-card__title">Interior</h3>
            <ul class="category-card__list">
              <li>フラワーベース</li>
              <li>キャンドルホルダー</li>
              <li>オブジェ</li>
            </ul>
          </article>

          <article class="category-card">
            <img class="category-card__image" src="images/category/fabric.png" alt="Fabricカテゴリーのイラスト">
            <h3 class="category-card__title">Fabric</h3>
            <ul class="category-card__list">
              <li>リネンクロス</li>
              <li>クッションカバー</li>
              <li>ブランケット</li>
            </ul>
          </article>

          <article class="category-card">
            <img class="category-card__image" src="images/category/aroma.png" alt="Aromaカテゴリーのイラスト">
            <h3 class="category-card__title">Aroma</h3>
            <ul class="category-card__list">
              <li>アロマキャンドル</li>
              <li>ディフューザー</li>
            </ul>
          </article>
        </div>

        <a class="button button--outline" href="products">すべてのカテゴリーを見る</a>
      </div>
    </section>

    <section class="section pickup">
      <div class="section__inner">
        <h2 class="section-title">PICK UP</h2>
        <p class="section-lead">季節の特集とお知らせ</p>

        <div class="pickup-list">
          <article class="pickup-card">
            <a class="pickup-card__link" href="journal">
                <img class="pickup-card__image" src="images/top/autumn-night.png" alt="秋の夜を心地よく過ごす特集">
            </a>
          </article>

          <article class="pickup-card">
            <a class="pickup-card__link" href="#">
                <img class="pickup-card__image" src="images/top/autumn-sale.png" alt="秋じたくセールの特集">
            </a>
          </article>
        </div>
      </div>
    </section>

    <section class="section bestseller">
      <div class="section__inner section__inner--flush-right">
        <h2 class="section-title">BEST SELLER</h2>

        <form
          class="ranking-filter"
          action="index"
          method="get"
          id="rankingFilterForm"
        >
          <label class="visually-hidden" for="rankingType">
            ランキングの表示切替
          </label>

          <select
            class="ranking-filter__select"
            id="rankingType"
            name="ranking"
          >
            <?php foreach ($rankingOptions as $value => $label): ?>
              <option
                value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"
                <?= $rankingType === $value ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>

          <noscript>
            <button class="ranking-filter__submit" type="submit">
              表示
            </button>
          </noscript>
        </form>

        <div class="product-scroll">
          <?php foreach ($bestSellers as $index => $product): ?>
            <?php
              $productCardOptions = [
                'showFavorite' => false,
                'headingTag' => 'h3',
                'rank' => $index + 1,
                'extraClass' => 'product-card--ranking',
              ];
              include __DIR__ . '/includes/product-card.php';
            ?>
          <?php endforeach; ?>
        </div>

        <a class="button button--text" href="products">ランキングをすべて見る</a>
      </div>
    </section>

    <section class="section brand-story">
      <div class="section__inner section__inner--wide">
        <h2 class="section-title">BRAND STORY</h2>
        <img class="brand-story__image" src="images/top/brand-story.jpg" alt="窓辺に生活雑貨が並ぶ穏やかな部屋">

        <div class="brand-story__body">
          <h3 class="brand-story__title">日々の暮らしに、小さな喜びを</h3>
          <p>何気なく使うものが少し変わるだけで、<br>いつもの時間はほんの少し心地よくなる。</p>
          <p>灯々は、暮らしに寄り添う生活雑貨を<br>ひとつひとつ丁寧に選んでいます。</p>
          <a class="button button--text" href="about">灯々について</a>
        </div>
      </div>
    </section>

    <section class="section instagram">
      <div class="section__inner">
        <h2 class="section-title">INSTAGRAM</h2>
        <h3 class="instagram__title">灯々のある暮らし</h3>
        <p class="instagram__text">新しく届いたものや、季節のしつらえ。<br>灯々のアイテムがある日々の風景をお届けします。</p>
        <a class="instagram__account" href="#">@akari_life</a>

        <div class="instagram-grid">
          <img src="images/instagram/post01.png" alt="灯々のInstagram投稿 1">
          <img src="images/instagram/post02.png" alt="灯々のInstagram投稿 2">
          <img src="images/instagram/post03.png" alt="灯々のInstagram投稿 3">
          <img src="images/instagram/post04.png" alt="灯々のInstagram投稿 4">
          <img src="images/instagram/post05.png" alt="灯々のInstagram投稿 5">
          <img src="images/instagram/post06.png" alt="灯々のInstagram投稿 6">
        </div>

        <a class="button button--outline" href="#">Instagramをフォローする</a>
      </div>
    </section>

    <section class="section newsletter">
      <div class="section__inner">
        <h2 class="section-title">NEWSLETTER</h2>
        <h3 class="newsletter__title">灯々から、<br>暮らしのお便り。</h3>
        <p class="newsletter__text">新商品や季節の特集、<br>暮らしのヒントを<br>お届けします。</p>

        <form class="newsletter-form" action="#" method="post">
          <label class="visually-hidden" for="newsletter-email">メールアドレス</label>
          <input id="newsletter-email" name="email" type="email" autocomplete="email" required>
          <button class="button button--submit" type="submit">登録する</button>
        </form>

        <p class="newsletter__note">※ いつでも解除できます</p>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="js/top.js"></script>
  <script src="js/common.js"></script>
</body>
</html>
