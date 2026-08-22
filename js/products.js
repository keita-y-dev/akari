document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".category-tab");
  const products = document.querySelectorAll(".product-card");
  const sortSelect = document.querySelector("#sortSelect");
  const productGrid = document.querySelector("#productGrid");
  const itemCount = document.querySelector("#itemCount");
  const noResult = document.querySelector("#noResult");
  const favoriteButtons = document.querySelectorAll(".favorite-button");
  const colorButtons = document.querySelectorAll(".color-button");

  const FAVORITES_KEY = "akariFavorites";

  let currentCategory = "all";


  /* ==============================
     favorites
  ============================== */

  function getFavorites() {
    try {
      return JSON.parse(localStorage.getItem(FAVORITES_KEY)) || [];
    } catch (error) {
      return [];
    }
  }


  function saveFavorites(favorites) {
    localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));
  }


  function getProductId(button) {
    return button.closest(".product-card")?.dataset.order;
  }


  function updateFavoriteButton(button, isFavorite) {
    const product = button.closest(".product-card");
    const name =
      product?.querySelector("h2")?.textContent.trim() || "商品";

    button.classList.toggle("is-active", isFavorite);

    button.setAttribute(
      "aria-pressed",
      String(isFavorite)
    );

    button.setAttribute(
      "aria-label",
      isFavorite
        ? `${name}をお気に入りから削除`
        : `${name}をお気に入りに追加`
    );
  }


  function initializeFavorites() {
    const favorites = getFavorites();

    favoriteButtons.forEach((button) => {
      const productId = getProductId(button);

      updateFavoriteButton(
        button,
        favorites.includes(productId)
      );
    });
  }


  favoriteButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const productId = getProductId(button);

      if (!productId) return;

      const favorites = getFavorites();
      const index = favorites.indexOf(productId);

      if (index === -1) {
        favorites.push(productId);
        updateFavoriteButton(button, true);
      } else {
        favorites.splice(index, 1);
        updateFavoriteButton(button, false);
      }

      saveFavorites(favorites);
    });
  });


  /* ==============================
     image gallery / color
  ============================== */

  colorButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const card = button.closest(".product-card");
      const image = card?.querySelector(
        ".product-card__image img"
      );

      const imagePath = button.dataset.image;

      if (!image || !imagePath) return;

      image.src = imagePath;

      card.querySelectorAll(".color-button").forEach((item) => {
        const isActive = item === button;

        item.classList.toggle(
          "is-active",
          isActive
        );

        item.setAttribute(
          "aria-pressed",
          String(isActive)
        );
      });
    });
  });


  /* ==============================
     category filter
  ============================== */

  function filterProducts() {
    const visibleProducts = [];

    products.forEach((product) => {
      const category = product.dataset.category;

      if (
        currentCategory === "all" ||
        category === currentCategory
      ) {
        product.classList.remove("is-hidden");
        visibleProducts.push(product);
      } else {
        product.classList.add("is-hidden");
      }
    });

    itemCount.textContent = visibleProducts.length;

    noResult.classList.toggle(
      "is-visible",
      visibleProducts.length === 0
    );
  }


  /* ==============================
     tab click
  ============================== */

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      currentCategory = tab.dataset.category;

      tabs.forEach((item) => {
        item.classList.remove("is-active");
      });

      tab.classList.add("is-active");

      filterProducts();
    });
  });


  /* ==============================
     sort
  ============================== */

  sortSelect.addEventListener("change", () => {
    const sortType = sortSelect.value;
    const productArray = [...products];

    productArray.sort((a, b) => {
      const priceA = Number(a.dataset.price);
      const priceB = Number(b.dataset.price);

      const orderA = Number(a.dataset.order);
      const orderB = Number(b.dataset.order);

      if (sortType === "price-low") {
        return priceA - priceB;
      }

      if (sortType === "price-high") {
        return priceB - priceA;
      }

      return orderA - orderB;
    });

    productArray.forEach((product) => {
      productGrid.appendChild(product);
    });

    filterProducts();
  });


  /* ==============================
     initial
  ============================== */

  initializeFavorites();
  filterProducts();
});