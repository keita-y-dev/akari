<?php
/*
 * category-card.php
 *
 * 必須:
 * $categoryCard['slug']
 * $categoryCard['name']
 * $categoryCard['image']
 * $categoryCard['items']
 *
 * 任意:
 * $categoryCard['alt']
 */

$categorySlug = (string)($categoryCard['slug'] ?? '');
$categoryName = (string)($categoryCard['name'] ?? '');
$categoryImage = (string)($categoryCard['image'] ?? '');
$categoryItems = $categoryCard['items'] ?? [];
$categoryAlt = (string)($categoryCard['alt'] ?? $categoryName . 'カテゴリーのイラスト');

if (!is_array($categoryItems)) {
    $categoryItems = [];
}
?>

<a
  class="category-card"
  href="products?category=<?= h($categorySlug) ?>"
  aria-label="<?= h($categoryName) ?>の商品を見る"
>
  <div class="category-card__visual">
    <img
      class="category-card__image"
      src="<?= h($categoryImage) ?>"
      alt="<?= h($categoryAlt) ?>"
    >
  </div>

  <div class="category-card__body">
    <div class="category-card__heading">
      <h3 class="category-card__title">
        <?= h($categoryName) ?>
      </h3>

      <span
        class="category-card__arrow"
        aria-hidden="true"
      >
        →
      </span>
    </div>

    <?php if (!empty($categoryItems)): ?>
      <ul class="category-card__list">
        <?php foreach ($categoryItems as $item): ?>
          <li><?= h((string)$item) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</a>
