document.addEventListener("DOMContentLoaded", () => {

  const cart = document.querySelector("#cart");
  const cartItems = document.querySelector("#cartItems");
  const emptyCart = document.querySelector("#emptyCart");

  const subtotalElement = document.querySelector("#subtotal");
  const shippingElement = document.querySelector("#shipping");
  const totalElement = document.querySelector("#total");

  const freeShippingMessage =
    document.querySelector("#freeShippingMessage");

  const progressBar =
    document.querySelector("#progressBar");

  const guideItems =
    document.querySelectorAll(".guide-item");

  const config =
    window.AKARI_CART || {};

  const csrfToken =
    config.csrfToken || "";

  const freeShippingThreshold =
    Number(config.freeShippingThreshold) || 5500;


  /* ==============================
     金額フォーマット
  ============================== */

  function formatPrice(price) {

    return Number(price).toLocaleString("ja-JP");

  }


  /* ==============================
     カート商品取得
  ============================== */

  function getCartItem(target) {

    return target.closest("[data-cart-item]");

  }


  /* ==============================
     操作中のボタン無効化
  ============================== */

  function setControlsDisabled(
    cartItem,
    disabled
  ) {

    cartItem
      .querySelectorAll("button, input")
      .forEach((control) => {

        control.disabled = disabled;

      });

  }


  /* ==============================
     金額表示更新
  ============================== */

  function updateSummary(data) {

    if (subtotalElement) {

      subtotalElement.textContent =
        formatPrice(data.subtotal);

    }


    if (shippingElement) {

      shippingElement.textContent =
        formatPrice(data.shipping);

    }


    if (totalElement) {

      totalElement.textContent =
        formatPrice(data.total);

    }


    if (freeShippingMessage) {

      const remaining =
        Math.max(
          freeShippingThreshold -
          Number(data.subtotal),
          0
        );


      freeShippingMessage.textContent =
        remaining === 0
          ? "送料無料です"
          : `あと￥${formatPrice(remaining)}で送料無料`;

    }


    if (progressBar) {

      const percentage =
        Math.min(
          Number(data.subtotal) /
          freeShippingThreshold *
          100,
          100
        );


      progressBar.style.width =
        `${percentage}%`;

    }

  }


  /* ==============================
     空カート表示
  ============================== */

  function showEmptyCart() {

    if (cart) {
      cart.hidden = true;
    }


    if (emptyCart) {
      emptyCart.hidden = false;
    }

  }


  /* ==============================
     PHPへカート更新リクエスト
  ============================== */

  async function sendCartRequest(
    action,
    productId,
    quantity = null
  ) {

    const formData =
      new FormData();


    formData.append(
      "action",
      action
    );


    formData.append(
      "product_id",
      productId
    );


    formData.append(
      "csrf_token",
      csrfToken
    );


    if (quantity !== null) {

      formData.append(
        "quantity",
        quantity
      );

    }


    const response =
      await fetch(
        "cart",
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
        "カートを更新できませんでした。"
      );

    }


    return data;

  }


  /* ==============================
     数量更新
  ============================== */

  async function updateQuantity(
    cartItem,
    newQuantity
  ) {

    const input =
      cartItem.querySelector(
        ".quantity__input"
      );


    if (!input) {
      return;
    }


    const min =
      Number(input.min) || 1;


    const max =
      Number(input.max) || 99;


    const quantity =
      Math.min(
        max,
        Math.max(
          min,
          Number(newQuantity) || min
        )
      );


    const productId =
      cartItem.dataset.productId;


    input.value =
      quantity;


    setControlsDisabled(
      cartItem,
      true
    );


    try {

      const data =
        await sendCartRequest(
          "update",
          productId,
          quantity
        );


      updateSummary(data);

    } catch (error) {

      alert(error.message);

      window.location.reload();

    } finally {

      if (
        document.body.contains(
          cartItem
        )
      ) {

        setControlsDisabled(
          cartItem,
          false
        );

      }

    }

  }


  /* ==============================
     ＋ − 削除ボタン
  ============================== */

  cartItems?.addEventListener(
    "click",
    async (event) => {

      const cartItem =
        getCartItem(event.target);


      if (!cartItem) {
        return;
      }


      const input =
        cartItem.querySelector(
          ".quantity__input"
        );


      /*
       * 数量を減らす
       */
      if (
        event.target.closest(
          ".quantity__minus"
        )
      ) {

        await updateQuantity(
          cartItem,
          Number(input.value) - 1
        );

        return;

      }


      /*
       * 数量を増やす
       */
      if (
        event.target.closest(
          ".quantity__plus"
        )
      ) {

        await updateQuantity(
          cartItem,
          Number(input.value) + 1
        );

        return;

      }


      /*
       * 商品削除
       */
      if (
        event.target.closest(
          ".delete-button"
        )
      ) {

        const productId =
          cartItem.dataset.productId;


        setControlsDisabled(
          cartItem,
          true
        );


        try {

          const data =
            await sendCartRequest(
              "remove",
              productId
            );


          cartItem.remove();


          updateSummary(data);


          if (
            Number(data.cartCount) === 0
          ) {

            showEmptyCart();

          }

        } catch (error) {

          alert(error.message);


          setControlsDisabled(
            cartItem,
            false
          );

        }

      }

    }
  );


  /* ==============================
     数量を直接入力
  ============================== */

  cartItems?.addEventListener(
    "change",
    async (event) => {

      if (
        !event.target.matches(
          ".quantity__input"
        )
      ) {
        return;
      }


      const cartItem =
        getCartItem(
          event.target
        );


      if (!cartItem) {
        return;
      }


      await updateQuantity(
        cartItem,
        event.target.value
      );

    }
  );


  /* ==============================
     ガイドアコーディオン
  ============================== */

  guideItems.forEach((guideItem) => {

    const button =
      guideItem.querySelector(".guide-button");

    const arrow =
      guideItem.querySelector(".guide-button__arrow");

    button?.addEventListener("click", () => {

      const isOpen =
        guideItem.classList.toggle("is-open");

      button.setAttribute(
        "aria-expanded",
        String(isOpen)
      );

      if (arrow) {
        arrow.textContent =
          isOpen ? "▼" : "▶";
      }

    });

  });

});