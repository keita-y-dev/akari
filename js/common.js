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
   FAVORITES
   product-card.php / recomend-card.php / product-detail.php 共通
===================================================== */

document.addEventListener("DOMContentLoaded", () => {

  const favoritesStore =
    window.AKARI_FAVORITES;

  if (!favoritesStore) {
    return;
  }

  function getProductName(button) {
    return (
      button
        .closest(".product-card, .recommend-card, .product-info")
        ?.querySelector(
          ".product-card__name, .recommend-card__name, .product-info__title"
        )
        ?.textContent
        ?.trim()
      || "商品"
    );
  }

  function updateFavoriteButton(button, isFavorite) {
    button.classList.toggle("is-active", isFavorite);
    button.setAttribute("aria-pressed", String(isFavorite));

    const singleIcon = button.querySelector(
      "img:not(.favorite-button__icon)"
    );

    if (singleIcon) {
      singleIcon.src = isFavorite
        ? "images/icons/heart-solid.svg"
        : "images/icons/heart-outline.svg";
    }

    const productName = getProductName(button);

    button.setAttribute(
      "aria-label",
      isFavorite
        ? `${productName}をお気に入りから削除`
        : `${productName}をお気に入りに追加`
    );
  }

  function initializeFavoriteButtons() {
    const favorites = favoritesStore.get();

    document
      .querySelectorAll("[data-favorite-product-id]")
      .forEach((button) => {
        const productId = String(
          button.dataset.favoriteProductId || ""
        );

        updateFavoriteButton(
          button,
          favorites.includes(productId)
        );
      });
  }

  document.addEventListener("click", (event) => {
    const button = event.target.closest(
      "[data-favorite-product-id]"
    );

    if (!button) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    const productId = String(
      button.dataset.favoriteProductId || ""
    );

    if (!productId) {
      return;
    }

    const isFavorite =
      favoritesStore.toggle(productId);

    updateFavoriteButton(button, isFavorite);
  });

  initializeFavoriteButtons();
});
