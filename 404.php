<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>ページが見つかりません | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/404.css">
</head>

<body>

  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="not-found">

    <section class="not-found__inner">

      <p class="not-found__number" aria-hidden="true">404</p>

      <p class="not-found__label">PAGE NOT FOUND</p>

      <h1>ページが見つかりません</h1>

      <p class="not-found__text">
        お探しのページは移動、または削除された可能性があります。<br>
        URLをご確認いただくか、トップページからお探しください。
      </p>

      <a class="not-found__button" href="index">
        TOPへ戻る
      </a>

      <a class="not-found__shop-link" href="products">
        商品を見る →
      </a>

    </section>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="js/common.js"></script>

</body>

</html>
