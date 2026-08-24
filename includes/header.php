<header class="header">
    <div class="header__inner">
      <button class="header__menu" type="button" aria-label="メニューを開く" aria-expanded="false" aria-controls="global-nav">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <a class="header__logo" href="index">灯々</a>

      <a class="header__cart" href="cart" aria-label="ショッピングカートを見る">
        <img src="images/icons/shopping-bag.svg" alt="">
      </a>
    </div>

    <nav class="global-nav" id="global-nav" aria-label="メインナビゲーション">
      <div class="global-nav__inner">
        <form class="global-nav__search" action="products" method="get">
          <label class="visually-hidden" for="global-search">商品を検索</label>
          <input id="global-search" name="q" type="search" placeholder="商品を検索" autocomplete="off">
          <button type="submit" aria-label="検索">
            <img src="images/icons/search.svg" alt="">
          </button>
        </form>

        <ul class="global-nav__list">
          <li><a href="index">HOME</a></li>
          <li><a href="products">商品一覧</a></li>
          <li><a href="favorites">お気に入り</a></li>
          <li><a href="journal">特集</a></li>
          <li><a href="about">灯々について</a></li>
        </ul>
      </div>
    </nav>
  </header>
