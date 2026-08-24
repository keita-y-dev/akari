<?php
/*
 * product-card.php
 *
 * 必須:
 * $product['id'], ['name'], ['price'], ['image_path']
 *
 * 任意:
 * $productCardOptions = [
 *   'showFavorite' => true,
 *   'rank' => null,
 *   'extraClass' => '',
 *   'headingTag' => 'h3',
 *   'category' => null,
 *   'price' => null,
 *   'order' => null,
 * ];
 */
$productCardOptions = $productCardOptions ?? [];
$_pcShowFavorite = $productCardOptions['showFavorite'] ?? true;
$_pcRank = $productCardOptions['rank'] ?? null;

if ($_pcRank === '' || $_pcRank === false) {
    $_pcRank = null;
}
$_pcExtraClass = trim((string)($productCardOptions['extraClass'] ?? ''));
$_pcHeadingTag = $productCardOptions['headingTag'] ?? 'h3';

if (!in_array($_pcHeadingTag, ['h2', 'h3'], true)) {
    $_pcHeadingTag = 'h3';
}
$_pcProductId = (int)($product['id'] ?? 0);
$_pcProductName = (string)($product['name'] ?? '');
$_pcProductPrice = (int)($product['price'] ?? 0);
$_pcProductImage = (string)($product['image_path'] ?? '');
$_pcDataCategory = $productCardOptions['category'] ?? null;
$_pcDataPrice = $productCardOptions['price'] ?? null;
$_pcDataOrder = $productCardOptions['order'] ?? null;
?>

<article
  class="product-card<?= $_pcExtraClass !== '' ? ' ' . htmlspecialchars($_pcExtraClass, ENT_QUOTES, 'UTF-8') : '' ?>"
  data-product-id="<?= $_pcProductId ?>"
  <?= $_pcDataCategory !== null ? 'data-category="' . htmlspecialchars((string)$_pcDataCategory, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
  <?= $_pcDataPrice !== null ? 'data-price="' . (int)$_pcDataPrice . '"' : '' ?>
  <?= $_pcDataOrder !== null ? 'data-order="' . (int)$_pcDataOrder . '"' : '' ?>
>

  <?php if ($_pcRank !== null): ?>
    <span class="product-card__rank"><?= (int)$_pcRank ?></span>
  <?php endif; ?>

  <a
    href="product-detail?id=<?= $_pcProductId ?>"
    class="product-card__image-link"
  >
    <div class="product-card__image">
      <?php if ($_pcProductImage !== ''): ?>
        <img
          src="<?= htmlspecialchars($_pcProductImage, ENT_QUOTES, 'UTF-8') ?>"
          alt="<?= htmlspecialchars($_pcProductName, ENT_QUOTES, 'UTF-8') ?>"
          loading="lazy"
        >
      <?php else: ?>
        <div class="product-card__placeholder" aria-label="商品画像は準備中です">NO IMAGE</div>
      <?php endif; ?>
    </div>
  </a>

  <div class="product-card__info">
    <?= '<' . $_pcHeadingTag . ' class="product-card__name">' ?>
      <a href="product-detail?id=<?= $_pcProductId ?>">
        <?= htmlspecialchars($_pcProductName, ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?= '</' . $_pcHeadingTag . '>' ?>

    <p class="product-card__price">
      ￥ <?= number_format($_pcProductPrice) ?>
      <span>（税込）</span>
    </p>
  </div>

  <?php if ($_pcShowFavorite): ?>
    <button
      class="favorite-button"
      type="button"
      data-favorite-product-id="<?= $_pcProductId ?>"
      aria-label="<?= htmlspecialchars($_pcProductName, ENT_QUOTES, 'UTF-8') ?>をお気に入りに追加"
      aria-pressed="false"
    >
      <img class="favorite-button__icon favorite-button__icon--outline" src="images/icons/heart-outline.svg" alt="">
      <img class="favorite-button__icon favorite-button__icon--solid" src="images/icons/heart-solid.svg" alt="">
    </button>
  <?php endif; ?>

</article>
