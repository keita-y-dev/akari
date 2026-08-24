<?php
/*
 * cart-item.php
 *
 * 必須:
 * $item['id']
 * $item['name']
 * $item['price']
 * $item['quantity']
 * $item['stock']
 * $item['image_path']
 *
 * 任意:
 * $item['category_name']
 *
 * cart 側に categoryLabel() がある場合はそれを利用します。
 */

$_ciProductId = (int)($item['id'] ?? 0);
$_ciProductName = (string)($item['name'] ?? '');
$_ciProductPrice = (int)($item['price'] ?? 0);
$_ciProductQuantity = max(1, (int)($item['quantity'] ?? 1));
$_ciProductStock = max(1, (int)($item['stock'] ?? 1));
$_ciProductImage = (string)($item['image_path'] ?? '');
$_ciCategoryName = (string)($item['category_name'] ?? '');

$_ciCategoryDisplay =
    $_ciCategoryName !== '' && function_exists('categoryLabel')
        ? categoryLabel($_ciCategoryName)
        : $_ciCategoryName;
?>

<div
  class="cart-item"
  data-cart-item
  data-product-id="<?= $_ciProductId ?>"
  data-price="<?= $_ciProductPrice ?>"
>

  <div class="cart-item__top">

    <a
      href="product-detail?id=<?= $_ciProductId ?>"
      class="cart-item__image"
    >
      <?php if ($_ciProductImage !== ''): ?>
        <img
          src="<?= htmlspecialchars($_ciProductImage, ENT_QUOTES, 'UTF-8') ?>"
          alt="<?= htmlspecialchars($_ciProductName, ENT_QUOTES, 'UTF-8') ?>"
        >
      <?php else: ?>
        <div
          class="cart-item__placeholder"
          aria-label="商品画像は準備中です"
        >
          NO IMAGE
        </div>
      <?php endif; ?>
    </a>

    <div class="cart-item__info">

      <?php if ($_ciCategoryDisplay !== ''): ?>
        <p class="cart-item__category">
          <?= htmlspecialchars($_ciCategoryDisplay, ENT_QUOTES, 'UTF-8') ?>
        </p>
      <?php endif; ?>

      <h2 class="cart-item__name">
        <a href="product-detail?id=<?= $_ciProductId ?>">
          <?= htmlspecialchars($_ciProductName, ENT_QUOTES, 'UTF-8') ?>
        </a>
      </h2>

      <p class="cart-item__price">
        ￥<?= number_format($_ciProductPrice) ?>
        <span>（税込）</span>
      </p>

    </div>

  </div>

  <div class="cart-item__bottom">

    <p class="quantity__label">
      数量
    </p>

    <div class="quantity">

      <button
        class="quantity__button quantity__minus"
        type="button"
        aria-label="<?= htmlspecialchars($_ciProductName, ENT_QUOTES, 'UTF-8') ?>の数量を1減らす"
      >
        −
      </button>

      <input
        class="quantity__input"
        type="number"
        value="<?= $_ciProductQuantity ?>"
        min="1"
        max="<?= $_ciProductStock ?>"
        aria-label="<?= htmlspecialchars($_ciProductName, ENT_QUOTES, 'UTF-8') ?>の数量"
      >

      <button
        class="quantity__button quantity__plus"
        type="button"
        aria-label="<?= htmlspecialchars($_ciProductName, ENT_QUOTES, 'UTF-8') ?>の数量を1増やす"
      >
        ＋
      </button>

    </div>

    <button
      class="delete-button"
      type="button"
      aria-label="<?= htmlspecialchars($_ciProductName, ENT_QUOTES, 'UTF-8') ?>をカートから削除"
    >
      削除
    </button>

  </div>

</div>
