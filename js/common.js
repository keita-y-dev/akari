document.addEventListener("DOMContentLoaded", () => {
  const menuButton = document.querySelector(".header__menu");
  const globalNav = document.querySelector(".global-nav");

  if (!menuButton || !globalNav) return;

  menuButton.addEventListener("click", () => {
    const isOpen = menuButton.classList.toggle("is-open");
    globalNav.classList.toggle("is-open", isOpen);
    menuButton.setAttribute("aria-expanded", String(isOpen));
    menuButton.setAttribute(
      "aria-label",
      isOpen ? "メニューを閉じる" : "メニューを開く"
    );
    document.body.classList.toggle("is-menu-open", isOpen);
  });

  globalNav.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      menuButton.classList.remove("is-open");
      globalNav.classList.remove("is-open");
      menuButton.setAttribute("aria-expanded", "false");
      menuButton.setAttribute("aria-label", "メニューを開く");
      document.body.classList.remove("is-menu-open");
    });
  });
});

/* =====================================================
   PRODUCT CARD FAVORITES
   product-card.php / recomend-card.php 共通
===================================================== */

document.addEventListener("DOMContentLoaded", () => {

  const FAVORITES_KEY = "akariFavorites";


  function getFavorites() {

    try {

      const favorites =
        JSON.parse(
          localStorage.getItem(
            FAVORITES_KEY
          )
        );

      return Array.isArray(favorites)
        ? favorites.map(String)
        : [];

    } catch (error) {

      return [];

    }

  }


  function saveFavorites(favorites) {

    localStorage.setItem(
      FAVORITES_KEY,
      JSON.stringify(favorites)
    );

  }


  function updateFavoriteButton(
    button,
    isFavorite
  ) {

    button.classList.toggle(
      "is-active",
      isFavorite
    );

    button.setAttribute(
      "aria-pressed",
      String(isFavorite)
    );


    const productCard =
      button.closest(
        ".product-card, .recommend-card"
      );


    const productName =
      productCard
        ?.querySelector(
          ".product-card__name, .recommend-card__name"
        )
        ?.textContent
        ?.trim()
        || "商品";


    button.setAttribute(
      "aria-label",
      isFavorite
        ? `${productName}をお気に入りから削除`
        : `${productName}をお気に入りに追加`
    );

  }


  function initializeFavoriteButtons() {

    const favorites =
      getFavorites();


    document
      .querySelectorAll(
        "[data-favorite-product-id]"
      )
      .forEach((button) => {

        const productId =
          String(
            button.dataset
              .favoriteProductId
            || ""
          );


        updateFavoriteButton(
          button,
          favorites.includes(
            productId
          )
        );

      });

  }


  document.addEventListener(
    "click",
    (event) => {

      const button =
        event.target.closest(
          "[data-favorite-product-id]"
        );


      if (!button) {
        return;
      }


      event.preventDefault();
      event.stopPropagation();


      const productId =
        String(
          button.dataset
            .favoriteProductId
          || ""
        );


      if (!productId) {
        return;
      }


      const favorites =
        getFavorites();


      const index =
        favorites.indexOf(
          productId
        );


      if (index === -1) {

        favorites.push(
          productId
        );


        updateFavoriteButton(
          button,
          true
        );

      } else {

        favorites.splice(
          index,
          1
        );


        updateFavoriteButton(
          button,
          false
        );

      }


      saveFavorites(
        favorites
      );

    }
  );


  initializeFavoriteButtons();

});