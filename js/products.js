document.addEventListener("DOMContentLoaded", () => {

  const tabs =
    document.querySelectorAll(".category-tab");

  const products =
    document.querySelectorAll(".product-card");

  const sortSelect =
    document.querySelector("#sortSelect");

  const productGrid =
    document.querySelector("#productGrid");

  const itemCount =
    document.querySelector("#itemCount");

  const noResult =
    document.querySelector("#noResult");

  const colorButtons =
    document.querySelectorAll(".color-button");

  const moreButton =
    document.querySelector(".more-button");


  const DISPLAY_STEP = 6;

  let currentCategory = "all";
  let visibleCount = DISPLAY_STEP;


  /* ==============================
     IMAGE / COLOR
  ============================== */

  colorButtons.forEach((button) => {

    button.addEventListener("click", () => {

      const card =
        button.closest(".product-card");

      const image =
        card?.querySelector(
          ".product-card__image img"
        );

      const imagePath =
        button.dataset.image;


      if (!image || !imagePath) {
        return;
      }


      image.src = imagePath;


      card
        .querySelectorAll(".color-button")
        .forEach((item) => {

          const isActive =
            item === button;


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
     MATCHED PRODUCTS
  ============================== */

  function getMatchedProducts() {

    return [...products].filter((product) => {

      const category =
        product.dataset.category;


      return (
        currentCategory === "all" ||
        category === currentCategory
      );

    });

  }


  /* ==============================
     UPDATE PRODUCTS
  ============================== */

  function updateProducts() {

    const matchedProducts =
      getMatchedProducts();


    /* 商品件数 */

    if (itemCount) {

      itemCount.textContent =
        matchedProducts.length;

    }


    /* 該当商品なし */

    if (noResult) {

      noResult.classList.toggle(
        "is-visible",
        matchedProducts.length === 0
      );

    }


    /* 一度すべての商品を非表示 */

    products.forEach((product) => {

      product.classList.add(
        "is-hidden"
      );

    });


    /* 表示件数まで商品を表示 */

    matchedProducts.forEach(
      (product, index) => {

        if (index < visibleCount) {

          product.classList.remove(
            "is-hidden"
          );

        }

      }
    );


    /* ==========================
       MORE BUTTON
    ========================== */

    if (!moreButton) {
      return;
    }


    /*
     * まだ表示していない商品が
     * 存在する場合だけ表示
     *
     * 6商品以下
     * → 非表示
     *
     * 7商品以上
     * → 表示
     */

    const hasMore =
      matchedProducts.length >
      visibleCount;


    moreButton.classList.toggle(
      "is-hidden",
      !hasMore
    );

  }


  /* ==============================
     CATEGORY
  ============================== */

  tabs.forEach((tab) => {

    tab.addEventListener("click", () => {

      currentCategory =
        tab.dataset.category;


      /*
       * カテゴリー切替時は
       * 表示件数を6件に戻す
       */

      visibleCount =
        DISPLAY_STEP;


      tabs.forEach((item) => {

        item.classList.remove(
          "is-active"
        );

      });


      tab.classList.add(
        "is-active"
      );


      updateProducts();

    });

  });


  /* ==============================
     SORT
  ============================== */

  sortSelect?.addEventListener(
    "change",
    () => {

      const sortType =
        sortSelect.value;


      const productArray =
        [...products];


      productArray.sort((a, b) => {

        const priceA =
          Number(a.dataset.price);

        const priceB =
          Number(b.dataset.price);

        const orderA =
          Number(a.dataset.order);

        const orderB =
          Number(b.dataset.order);


        /* 価格の安い順 */

        if (sortType === "price-low") {

          return priceA - priceB;

        }


        /* 価格の高い順 */

        if (sortType === "price-high") {

          return priceB - priceA;

        }


        /* おすすめ順 */

        return orderA - orderB;

      });


      productArray.forEach((product) => {

        productGrid.appendChild(
          product
        );

      });


      updateProducts();

    }
  );


  /* ==============================
     MORE
  ============================== */

  moreButton?.addEventListener(
    "click",
    () => {

      /*
       * 6商品ずつ追加表示
       */

      visibleCount +=
        DISPLAY_STEP;


      updateProducts();

    }
  );


  /* ==============================
     INITIAL CATEGORY FROM URL
  ============================== */

  const requestedCategory =
    new URLSearchParams(
      window.location.search
    ).get("category");


  if (requestedCategory) {

    const requestedTab =
      [...tabs].find((tab) => {

        return (
          tab.dataset.category ===
          requestedCategory
        );

      });


    if (requestedTab) {

      currentCategory =
        requestedCategory;


      tabs.forEach((tab) => {

        tab.classList.toggle(
          "is-active",
          tab === requestedTab
        );

      });

    }

  }


  /* ==============================
     INITIAL
  ============================== */

  updateProducts();

});