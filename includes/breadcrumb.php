<?php
/*
 * breadcrumb.php
 *
 * 使用例:
 * $breadcrumbs = [
 *     ['label' => 'TOP', 'url' => 'index.php'],
 *     ['label' => '商品一覧', 'url' => null],
 * ];
 * include __DIR__ . '/includes/breadcrumb.php';
 */

$breadcrumbs = $breadcrumbs ?? [];

if (!empty($breadcrumbs)):
?>
<nav class="breadcrumb" aria-label="パンくずリスト">
  <ol class="breadcrumb__list">
    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
      <?php
        $label = (string)($breadcrumb['label'] ?? '');
        $url = $breadcrumb['url'] ?? null;
        $isCurrent = $index === array_key_last($breadcrumbs);
      ?>

      <li class="breadcrumb__item">
        <?php if (!$isCurrent && $url): ?>
          <a
            class="breadcrumb__link"
            href="<?= htmlspecialchars((string)$url, ENT_QUOTES, 'UTF-8') ?>"
          >
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
          </a>
        <?php else: ?>
          <span
            class="breadcrumb__current"
            <?= $isCurrent ? 'aria-current="page"' : '' ?>
          >
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
          </span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
</nav>
<?php endif; ?>
