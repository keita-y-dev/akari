document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".category-tab");
  const products = document.querySelectorAll(".product-card");
  const sortSelect = document.querySelector("#sortSelect");
  const productGrid = document.querySelector("#productGrid");
  const itemCount = document.querySelector("#itemCount");
  const noResult = document.querySelector("#noResult");
  const colorButtons = document.querySelectorAll(".color-button");

  let currentCategory = "all";



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

  filterProducts();
});