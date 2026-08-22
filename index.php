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
        <a class="button button--hero" href="products.php">商品を見る</a>
      </div>
    </section>

    <section class="section new-arrival" id="new-arrival">
      <div class="section__inner">
        <h2 class="section-title">NEW ARRIVAL</h2>

        <div class="product-grid">
          <article class="product-card">
            <a href="product-detail.php">
              <img class="product-card__image" src="images/products/mug.png" alt="木の持ち手のマグカップ">
              <h3 class="product-card__name">木の持ち手のマグカップ</h3>
              <p class="product-card__price">￥2,750 <span>（税込）</span></p>
            </a>
          </article>

          <article class="product-card">
            <a href="product-detail.php">
              <img class="product-card__image" src="images/products/flower-vase.png" alt="ガラスの一輪挿し">
              <h3 class="product-card__name">ガラスの一輪挿し</h3>
              <p class="product-card__price">￥3,850 <span>（税込）</span></p>
            </a>
          </article>

          <article class="product-card">
            <a href="product-detail.php">
              <img class="product-card__image" src="images/products/candle-holder.png" alt="真鍮のキャンドルホルダー">
              <h3 class="product-card__name">真鍮のキャンドルホルダー</h3>
              <p class="product-card__price">￥3,850 <span>（税込）</span></p>
            </a>
          </article>

          <article class="product-card">
            <a href="product-detail.php">
              <img class="product-card__image" src="images/products/wood-tray.png" alt="木製トレー">
              <h3 class="product-card__name">木製トレー</h3>
              <p class="product-card__price">￥4,400 <span>（税込）</span></p>
            </a>
          </article>
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

        <a class="button button--outline" href="products.php">すべてのカテゴリーを見る</a>
      </div>
    </section>

    <section class="section pickup">
      <div class="section__inner">
        <h2 class="section-title">PICK UP</h2>
        <p class="section-lead">季節の特集とお知らせ</p>

        <div class="pickup-list">
          <article class="pickup-card">
            <a class="pickup-card__link" href="journal.php">
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

        <div class="ranking-filter">
          <button class="ranking-filter__button" type="button">総合⌄</button>
        </div>

        <div class="product-scroll">
          <article class="product-card product-card--ranking">
            <span class="product-card__rank">1</span>
            <a href="product-detail.php">
              <img class="product-card__image" src="images/products/mug.png" alt="木の持ち手のマグカップ">
              <h3 class="product-card__name">木の持ち手のマグカップ</h3>
              <p class="product-card__price">￥2,750 <span>（税込）</span></p>
            </a>
          </article>

          <article class="product-card product-card--ranking">
            <span class="product-card__rank">2</span>
            <a href="product-detail.php">
              <img class="product-card__image" src="images/products/linen-cloth.png" alt="リネンクロス">
              <h3 class="product-card__name">リネンクロス</h3>
              <p class="product-card__price">￥1,650 <span>（税込）</span></p>
            </a>
          </article>

          <article class="product-card product-card--ranking">
            <span class="product-card__rank">3</span>
            <a href="product-detail.php">
              <img class="product-card__image" src="images/products/wood-tray.png" alt="木製トレー">
              <h3 class="product-card__name">木製トレー</h3>
              <p class="product-card__price">￥4,400 <span>（税込）</span></p>
            </a>
          </article>
        </div>

        <a class="button button--text" href="products.php">ランキングをすべて見る</a>
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
          <a class="button button--text" href="about.php">灯々について</a>
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
</body>
</html>
