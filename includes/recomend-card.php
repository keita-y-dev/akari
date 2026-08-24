<?php
/* recomend-card.php */
$recommendCardOptions = $recommendCardOptions ?? [];
$_rcShowFavorite = $recommendCardOptions['showFavorite'] ?? false;
$_rcProductId = (int)($recommend['id'] ?? 0);
$_rcProductName = (string)($recommend['name'] ?? '');
$_rcProductPrice = (int)($recommend['price'] ?? 0);
$_rcProductImage = (string)($recommend['image_path'] ?? '');
?>

<article class="recommend-card">
  <a href="product-detail?id=<?= $_rcProductId ?>" class="recommend-card__link">
    <div class="recommend-card__image">
      <?php if ($_rcProductImage !== ''): ?>
        <img
          src="<?= htmlspecialchars($_rcProductImage, ENT_QUOTES, 'UTF-8') ?>"
          alt="<?= htmlspecialchars($_rcProductName, ENT_QUOTES, 'UTF-8') ?>"
          loading="lazy"
        >
      <?php else: ?>
        <div class="recommend-card__placeholder" aria-label="商品画像は準備中です">NO IMAGE</div>
      <?php endif; ?>
    </div>

    <h3 class="recommend-card__name"><?= htmlspecialchars($_rcProductName, ENT_QUOTES, 'UTF-8') ?></h3>

    <div class="recommend-card__bottom">
      <p class="recommend-card__price">
        ￥ <?= number_format($_rcProductPrice) ?>
        <span>（税込）</span>
      </p>
    </div>
  </a>

  <?php if ($_rcShowFavorite): ?>
    <button
      class="favorite-button recommend-card__favorite"
      type="button"
      data-favorite-product-id="<?= $_rcProductId ?>"
      aria-label="<?= htmlspecialchars($_rcProductName, ENT_QUOTES, 'UTF-8') ?>をお気に入りに追加"
      aria-pressed="false"
    >
      <img class="favorite-button__icon favorite-button__icon--outline" src="images/icons/heart-outline.svg" alt="">
      <img class="favorite-button__icon favorite-button__icon--solid" src="images/icons/heart-solid.svg" alt="">
    </button>
  <?php endif; ?>
</article>
