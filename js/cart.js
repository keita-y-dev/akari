document.addEventListener("DOMContentLoaded", () => {

  const cart = document.querySelector("#cart");
  const cartItem = document.querySelector("#cartItem");
  const emptyCart = document.querySelector("#emptyCart");

  const quantityMinus = document.querySelector(".quantity__minus");
  const quantityPlus = document.querySelector(".quantity__plus");
  const quantityInput = document.querySelector(".quantity__input");

  const deleteButton = document.querySelector(".delete-button");

  const subtotalElement = document.querySelector("#subtotal");
  const shippingElement = document.querySelector("#shipping");
  const totalElement = document.querySelector("#total");

  const freeShippingMessage = document.querySelector("#freeShippingMessage");
  const progressBar = document.querySelector("#progressBar");

  const checkoutButton = document.querySelector("#checkoutButton");

  const guideItems = document.querySelectorAll(".guide-item");

  const FREE_SHIPPING_THRESHOLD = 5500;
  const SHIPPING_FEE = 550;


  /* ==============================
     初期値
  ============================== */

  if (!cartItem || !quantityInput) {
    return;
  }

  const unitPrice =
    Number(cartItem.dataset.price) || 0;


  /* ==============================
     金額表示
  ============================== */

  function formatPrice(price) {

    return price.toLocaleString("ja-JP");

  }


  /* ==============================
     カート更新
  ============================== */

  function updateCart() {

    let quantity =
      Number(quantityInput.value) || 1;

    const maxQuantity =
      Number(quantityInput.max) || 99;


    if (quantity < 1) {
      quantity = 1;
    }


    if (quantity > maxQuantity) {
      quantity = maxQuantity;
    }


    quantityInput.value =
      quantity;


    const subtotal =
      unitPrice * quantity;


    const shipping =
      subtotal >= FREE_SHIPPING_THRESHOLD
        ? 0
        : SHIPPING_FEE;


    const total =
      subtotal + shipping;


    if (subtotalElement) {

      subtotalElement.textContent =
        formatPrice(subtotal);

    }


    if (shippingElement) {

      shippingElement.textContent =
        formatPrice(shipping);

    }


    if (totalElement) {

      totalElement.textContent =
        formatPrice(total);

    }


    updateFreeShipping(subtotal);

  }


  /* ==============================
     送料無料表示
  ============================== */

  function updateFreeShipping(subtotal) {

    const remaining =
      Math.max(
        FREE_SHIPPING_THRESHOLD - subtotal,
        0
      );


    if (freeShippingMessage) {

      if (remaining === 0) {

        freeShippingMessage.textContent =
          "送料無料です";

      } else {

        freeShippingMessage.textContent =
          `あと￥${formatPrice(remaining)}で送料無料`;

      }

    }


    if (progressBar) {

      const percentage =
        Math.min(
          subtotal / FREE_SHIPPING_THRESHOLD * 100,
          100
        );


      progressBar.style.width =
        `${percentage}%`;

    }

  }


  /* ==============================
     数量を減らす
  ============================== */

  quantityMinus?.addEventListener(
    "click",
    () => {

      let quantity =
        Number(quantityInput.value) || 1;


      if (quantity > 1) {

        quantity--;

        quantityInput.value =
          quantity;

        updateCart();

      }

    }
  );


  /* ==============================
     数量を増やす
  ============================== */

  quantityPlus?.addEventListener(
    "click",
    () => {

      let quantity =
        Number(quantityInput.value) || 1;


      const maxQuantity =
        Number(quantityInput.max) || 99;


      if (quantity < maxQuantity) {

        quantity++;

        quantityInput.value =
          quantity;

        updateCart();

      }

    }
  );


  /* ==============================
     数量を直接入力
  ============================== */

  quantityInput?.addEventListener(
    "change",
    () => {

      let quantity =
        Number(quantityInput.value) || 1;


      const maxQuantity =
        Number(quantityInput.max) || 99;


      if (quantity < 1) {
        quantity = 1;
      }


      if (quantity > maxQuantity) {
        quantity = maxQuantity;
      }


      quantityInput.value =
        quantity;


      updateCart();

    }
  );


  /* ==============================
     商品削除
  ============================== */

  deleteButton?.addEventListener(
    "click",
    () => {

      cartItem.remove();


      if (cart) {

        cart.style.display =
          "none";

      }


      if (emptyCart) {

        emptyCart.style.display =
          "block";

      }


      if (checkoutButton) {

        checkoutButton.setAttribute(
          "aria-disabled",
          "true"
        );

      }

    }
  );


  /* ==============================
     ガイドアコーディオン
  ============================== */

  guideItems.forEach(
    (guideItem) => {

      const button =
        guideItem.querySelector(
          ".guide-button"
        );

      const content =
        guideItem.querySelector(
          ".guide-content"
        );

      const arrow =
        guideItem.querySelector(
          ".guide-button__arrow"
        );


      button?.addEventListener(
        "click",
        () => {

          const isOpen =
            guideItem.classList.toggle(
              "is-open"
            );


          button.setAttribute(
            "aria-expanded",
            String(isOpen)
          );


          if (content) {

            content.style.maxHeight =
              isOpen
                ? `${content.scrollHeight}px`
                : "0px";

          }


          if (arrow) {

            arrow.textContent =
              isOpen
                ? "▼"
                : "▶";

          }

        }
      );

    }
  );


  /* ==============================
     初期表示
  ============================== */

  if (emptyCart) {

    emptyCart.style.display =
      "none";

  }


  updateCart();

});