<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>ABOUT | 灯々</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&family=Noto+Serif+JP:wght@400;500&display=swap"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/about.css">
</head>

<body>

  <!-- header -->
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main>

    <?php
      $breadcrumbs = [
        ['label' => 'TOP', 'url' => 'index.php'],
        ['label' => 'ABOUT', 'url' => null],
      ];
      include __DIR__ . '/includes/breadcrumb.php';
    ?>

    <!-- page title -->
    <section class="about-heading">
      <p class="section-label">ABOUT</p>
      <h1>灯々について</h1>
    </section>


    <!-- about -->
    <section class="about-intro">

      <div class="about-image">
        <img
          src="images/about/about-main.png"
          alt="木のテーブルにマグカップや花瓶が置かれた暮らしの風景"
        >
      </div>

      <div class="about-intro__text">
        <h2>暮らしに、小さな灯りを。</h2>

        <p>
          何気ない毎日に、そっと心がほどける瞬間を。<br>
          灯々は、長く使うものほど愛着が増す、暮らしの道具を
          集めたお店です。
        </p>
      </div>

    </section>


    <!-- story -->
    <section class="about-story">

      <div class="section-heading">
        <p class="section-label">OUR STORY</p>
        <h2>灯々のはじまり</h2>
      </div>

      <div class="story-image">
        <img
          src="images/about/about-story.png"
          alt="器と布が置かれたテーブル"
        >
      </div>

      <div class="story-text">
        <p>
          忙しい日々の中で、お気に入りの器でお茶を飲む。
          季節の花を小さな花瓶に飾る。
        </p>

        <p>
          そんなささやかな時間を大切にしたいという想いから、
          灯々は生まれました。
        </p>
      </div>

    </section>


    <!-- values -->
    <section class="about-values">

      <div class="section-heading">
        <p class="section-label">OUR VALUES</p>
        <h2>大切にしていること</h2>
      </div>

      <div class="values-list">

        <article class="value-item">
          <p class="value-number">01</p>

          <h3>暮らしになじむこと</h3>

          <p class="value-text">
            まいにちの風景に自然と溶け込む、
            素朴で飽きのこないものを。
          </p>
        </article>

        <article class="value-item">
          <p class="value-number">02</p>

          <h3>長く使い続けられること</h3>

          <p class="value-text">
            使うほど味わいが増し、愛着を持てるものを選びます。
          </p>
        </article>

        <article class="value-item">
          <p class="value-number">03</p>

          <h3>つくり手が見えること</h3>

          <p class="value-text">
            素材や仕事を大切にした、丁寧なものづくりを届けます。
          </p>
        </article>

      </div>

    </section>


    <!-- selection -->
    <section class="about-selection">

      <div class="section-heading">
        <p class="section-label">SELECTION</p>
        <h2>商品を選ぶ基準</h2>
      </div>

      <div class="selection-image">
        <img
          src="images/about/about-selection.jpg"
          alt="器を手に取って選んでいる様子"
        >
      </div>

      <div class="selection-text">

        <p>
          素材の風合い、使いやすさ、暮らしへのなじみ方。
        </p>

        <p>
          見た目の美しさだけではなく、毎日気負わず使えるかと
          いう視点で、ひとつひとつ商品を選んでいます。
        </p>

      </div>

    </section>


    <!-- cta -->
    <section class="about-cta">

      <h2>
        暮らしに寄り添う道具を、<br>
        あなたのもとへ。
      </h2>

      <a href="products.php" class="cta-button">
        商品を見る
      </a>

      <a href="journal.php" class="feature-link">
        特集を読む<span>→</span>
      </a>

    </section>

  </main>


  <!-- footer -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="js/common.js"></script>
  
</body>

</html>