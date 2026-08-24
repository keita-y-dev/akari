document.addEventListener("DOMContentLoaded", () => {

  const grid =
    document.querySelector("#favoritesGrid");

  const empty =
    document.querySelector("#favoritesEmpty");

  const count =
    document.querySelector("#favoriteCount");


  function refreshFavoritesPage() {

    const favorites =
      window.AKARI_FAVORITES?.get() || [];

    let visibleCount = 0;


    grid
      ?.querySelectorAll(
        ".favorite-product-card[data-product-id]"
      )
      .forEach((card) => {

        const productId =
          String(
            card.dataset.productId
            || ""
          );

        const isFavorite =
          favorites.includes(productId);

        card.classList.toggle(
          "is-favorite-hidden",
          !isFavorite
        );

        if (isFavorite) {
          visibleCount++;
        }

      });


    if (count) {
      count.textContent =
        String(visibleCount);
    }


    if (grid) {
      grid.hidden =
        visibleCount === 0;
    }


    if (empty) {
      empty.hidden =
        visibleCount !== 0;
    }

  }


  /*
   * common.js がハートの状態と localStorage を更新した後、
   * お気に入りページの表示も更新します。
   */
  document.addEventListener(
    "click",
    (event) => {

      const favoriteButton =
        event.target.closest(
          "[data-favorite-product-id]"
        );

      if (!favoriteButton) {
        return;
      }

      /*
       * common.js のクリック処理と確実に同期するため、
       * 現在のイベント処理が終わった直後に再描画します。
       */
      setTimeout(
        refreshFavoritesPage,
        0
      );

    }
  );


  window.addEventListener(
    "storage",
    (event) => {

      if (
        event.key === window.AKARI_FAVORITES?.storageKey
      ) {
        refreshFavoritesPage();
      }

    }
  );


  refreshFavoritesPage();

});
