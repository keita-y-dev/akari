document.addEventListener(
  "DOMContentLoaded",
  () => {

    const mainImage =
      document.querySelector(
        "#mainImage"
      );

    const mainImageButton =
      document.querySelector(
        ".product-gallery__main"
      );

    const thumbnails =
      document.querySelectorAll(
        ".gallery-thumb"
      );

    const favoriteButton =
      document.querySelector(
        ".favorite-button"
      );

    const quantityInput =
      document.querySelector(
        "#quantityInput"
      );

    const quantityMinus =
      document.querySelector(
        "#quantityMinus"
      );

    const quantityPlus =
      document.querySelector(
        "#quantityPlus"
      );

    const cartButton =
      document.querySelector(
        ".cart-button"
      );

    const accordions =
      document.querySelectorAll(
        ".accordion"
      );

    const modal =
      document.querySelector(
        "#imageModal"
      );

    const modalImage =
      document.querySelector(
        "#modalImage"
      );

    const modalClose =
      document.querySelector(
        "#modalClose"
      );

    const modalOverlay =
      document.querySelector(
        ".image-modal__overlay"
      );


    const config =
      window.AKARI_PRODUCT || {};


    const productId =
      String(
        config.productId || ""
      );


    const csrfToken =
      config.csrfToken || "";


    const FAVORITES_KEY =
      "akariFavorites";


    /* ==============================
       favorites
    ============================== */

    function getFavorites() {

      try {

        const favorites =
          JSON.parse(
            localStorage.getItem(
              FAVORITES_KEY
            )
          );


        return Array.isArray(
          favorites
        )
          ? favorites
          : [];

      } catch (error) {

        return [];

      }

    }


    function saveFavorites(
      favorites
    ) {

      localStorage.setItem(
        FAVORITES_KEY,
        JSON.stringify(
          favorites
        )
      );

    }


    function updateFavoriteButton(
      isFavorite
    ) {

      if (!favoriteButton) {
        return;
      }


      const icon =
        favoriteButton.querySelector(
          "img"
        );


      if (icon) {

        icon.src =
          isFavorite
            ? "images/icons/heart-solid.svg"
            : "images/icons/heart-outline.svg";

      }


      favoriteButton
        .classList
        .toggle(
          "is-active",
          isFavorite
        );


      favoriteButton
        .setAttribute(
          "aria-pressed",
          String(isFavorite)
        );


      favoriteButton
        .setAttribute(
          "aria-label",
          isFavorite
            ? "お気に入りから削除"
            : "お気に入りに追加"
        );

    }


    function initializeFavorite() {

      if (
        !favoriteButton ||
        !productId
      ) {
        return;
      }


      const favorites =
        getFavorites();


      updateFavoriteButton(
        favorites.includes(
          productId
        )
      );

    }


    favoriteButton
      ?.addEventListener(
        "click",
        () => {

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
              true
            );

          } else {

            favorites.splice(
              index,
              1
            );

            updateFavoriteButton(
              false
            );

          }


          saveFavorites(
            favorites
          );

        }
      );


    /* ==============================
       gallery
    ============================== */

    function setActiveThumbnail(
      target
    ) {

      thumbnails.forEach(
        (thumbnail) => {

          thumbnail.classList.toggle(
            "is-active",
            thumbnail === target
          );

        }
      );

    }


    function showGalleryImage(
      thumbnail
    ) {

      const image =
        thumbnail.querySelector(
          "img"
        );


      if (!image) {
        return;
      }


      mainImage.src =
        image.src;


      mainImage.alt =
        thumbnail.dataset.alt ||
        image.alt ||
        "商品画像";


      setActiveThumbnail(
        thumbnail
      );

    }


    thumbnails.forEach(
      (thumbnail) => {

        thumbnail.addEventListener(
          "click",
          () => {

            showGalleryImage(
              thumbnail
            );

          }
        );

      }
    );


    /* ==============================
       quantity
    ============================== */

    function updateQuantity(
      value
    ) {

      if (!quantityInput) {
        return;
      }


      const max =
        Number(
          quantityInput.max
        ) || 99;


      const quantity =
        Math.min(
          max,
          Math.max(
            1,
            Number(value) || 1
          )
        );


      quantityInput.value =
        quantity;

    }


    quantityMinus
      ?.addEventListener(
        "click",
        () => {

          updateQuantity(
            Number(
              quantityInput.value
            ) - 1
          );

        }
      );


    quantityPlus
      ?.addEventListener(
        "click",
        () => {

          updateQuantity(
            Number(
              quantityInput.value
            ) + 1
          );

        }
      );


    quantityInput
      ?.addEventListener(
        "change",
        () => {

          updateQuantity(
            quantityInput.value
          );

        }
      );


    /* ==============================
       add to cart
    ============================== */

    async function addToCart() {

      if (
        !cartButton ||
        !quantityInput ||
        !productId
      ) {
        return;
      }


      const quantity =
        Number(
          quantityInput.value
        ) || 1;


      const originalText =
        cartButton.textContent;


      cartButton.disabled =
        true;


      cartButton.textContent =
        "追加しています…";


      const formData =
        new FormData();


      formData.append(
        "action",
        "add"
      );


      formData.append(
        "product_id",
        productId
      );


      formData.append(
        "quantity",
        quantity
      );


      formData.append(
        "csrf_token",
        csrfToken
      );


      try {

        const response =
          await fetch(
            "cart.php",
            {
              method: "POST",
              body: formData,
              headers: {
                "X-Requested-With":
                  "XMLHttpRequest"
              }
            }
          );


        const data =
          await response.json();


        if (
          !response.ok ||
          !data.success
        ) {

          throw new Error(
            data.message ||
            "カートに追加できませんでした。"
          );

        }


        cartButton.textContent =
          "カートに追加しました";


        /*
         * 少し待ってから
         * カートページへ移動
         */
        window.setTimeout(
          () => {

            window.location.href =
              "cart.php";

          },
          500
        );

      } catch (error) {

        alert(
          error.message
        );


        cartButton.disabled =
          false;


        cartButton.textContent =
          originalText;

      }

    }


    cartButton
      ?.addEventListener(
        "click",
        addToCart
      );


    /* ==============================
       accordion
    ============================== */

    accordions.forEach(
      (accordion) => {

        const button =
          accordion.querySelector(
            ".accordion__button"
          );


        button
          ?.addEventListener(
            "click",
            () => {

              const isOpen =
                accordion
                  .classList
                  .toggle(
                    "is-open"
                  );


              button.setAttribute(
                "aria-expanded",
                String(isOpen)
              );


              const icon =
                accordion.querySelector(
                  ".accordion__icon"
                );


              if (icon) {

                icon.textContent =
                  isOpen
                    ? "−"
                    : "＋";

              }

            }
          );

      }
    );


    /* ==============================
       image modal
    ============================== */

    function openModal() {

      if (
        !modal ||
        !modalImage
      ) {
        return;
      }


      modalImage.src =
        mainImage.src;


      modalImage.alt =
        mainImage.alt;


      modal.classList.add(
        "is-open"
      );


      modal.setAttribute(
        "aria-hidden",
        "false"
      );


      document.body
        .classList
        .add(
          "is-modal-open"
        );

    }


    function closeModal() {

      if (!modal) {
        return;
      }


      modal.classList.remove(
        "is-open"
      );


      modal.setAttribute(
        "aria-hidden",
        "true"
      );


      document.body
        .classList
        .remove(
          "is-modal-open"
        );

    }


    mainImageButton
      ?.addEventListener(
        "click",
        openModal
      );


    modalClose
      ?.addEventListener(
        "click",
        closeModal
      );


    modalOverlay
      ?.addEventListener(
        "click",
        closeModal
      );


    document.addEventListener(
      "keydown",
      (event) => {

        if (
          event.key ===
            "Escape" &&
          modal
            ?.classList
            .contains(
              "is-open"
            )
        ) {

          closeModal();

        }

      }
    );


    /* ==============================
       initial
    ============================== */

    initializeFavorite();


    if (
      thumbnails.length > 0
    ) {

      setActiveThumbnail(
        thumbnails[0]
      );

    }

  }
);