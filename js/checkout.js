document.addEventListener("DOMContentLoaded", () => {

  /* =========================
     ELEMENTS
  ========================== */

  const cardForm =
    document.querySelector("#cardForm");

  const paymentInputs =
    document.querySelectorAll(
      'input[name="payment"]'
    );

  const postal =
    document.querySelector("#postal");

  const postalButton =
    document.querySelector("#postalButton");

  const prefecture =
    document.querySelector("#prefecture");

  const address =
    document.querySelector("#address");

  const phone =
    document.querySelector("#phone");

  const cardNumber =
    document.querySelector("#cardNumber");

  const securityCode =
    document.querySelector("#securityCode");


  /* =========================
     PAYMENT
  ========================== */

  function updatePaymentForm() {

    if (!cardForm) {
      return;
    }

    const selectedPayment =
      document.querySelector(
        'input[name="payment"]:checked'
      );

    const showCardForm =
      selectedPayment?.value === "card";


    cardForm.classList.toggle(
      "is-hidden",
      !showCardForm
    );


    cardForm.setAttribute(
      "aria-hidden",
      String(!showCardForm)
    );

  }


  paymentInputs.forEach((input) => {

    input.addEventListener(
      "change",
      updatePaymentForm
    );

  });


  /* =========================
     PHONE
  ========================== */

  phone?.addEventListener(
    "input",
    () => {

      const digits =
        phone.value
          .replace(/\D/g, "")
          .slice(0, 11);


      phone.value =
        digits;

    }
  );


  /* =========================
     POSTAL CODE FORMAT
  ========================== */

  postal?.addEventListener(
    "input",
    () => {

      const digits =
        postal.value
          .replace(/\D/g, "")
          .slice(0, 7);


      postal.value =
        digits.length > 3
          ? `${digits.slice(0, 3)}-${digits.slice(3)}`
          : digits;

    }
  );


  /* =========================
     POSTAL ADDRESS SEARCH
  ========================== */

  postalButton?.addEventListener(
    "click",
    async () => {

      const postalValue =
        postal.value.replace(
          /\D/g,
          ""
        );


      /* -------------------------
         郵便番号チェック
      -------------------------- */

      if (postalValue.length !== 7) {

        alert(
          "郵便番号を7桁で入力してください。"
        );

        postal.focus();

        return;

      }


      /* -------------------------
         検索中
      -------------------------- */

      postalButton.disabled = true;

      const originalText =
        postalButton.textContent;

      postalButton.textContent =
        "検索中...";


      try {

        /* -------------------------
           zipcloud API
        -------------------------- */

        const response =
          await fetch(
            `https://zipcloud.ibsnet.co.jp/api/search?zipcode=${postalValue}`
          );


        if (!response.ok) {

          throw new Error(
            `HTTP Error: ${response.status}`
          );

        }


        const data =
          await response.json();


        /* -------------------------
           該当住所なし
        -------------------------- */

        if (
          data.status !== 200 ||
          !data.results ||
          data.results.length === 0
        ) {

          alert(
            "該当する住所が見つかりませんでした。"
          );

          return;

        }


        /* -------------------------
           住所取得
        -------------------------- */

        const result =
          data.results[0];


        /*
         * address1
         * → 都道府県
         *
         * address2
         * → 市区町村
         *
         * address3
         * → 町域
         */

        if (prefecture) {

          prefecture.value =
            result.address1;

        }


        if (address) {

          address.value =
            result.address2 +
            result.address3;


          /*
           * 自動入力後、
           * 番地を入力しやすいように
           * addressへフォーカス
           */

          address.focus();

        }

      } catch (error) {

        console.error(
          "住所検索エラー:",
          error
        );


        alert(
          "住所検索に失敗しました。時間をおいてもう一度お試しください。"
        );

      } finally {

        /* -------------------------
           ボタンを元に戻す
        -------------------------- */

        postalButton.disabled = false;

        postalButton.textContent =
          originalText;

      }

    }
  );


  /* =========================
     CARD NUMBER
     デモ用
  ========================== */

  cardNumber?.addEventListener(
    "input",
    () => {

      const digits =
        cardNumber.value
          .replace(/\D/g, "")
          .slice(0, 16);


      cardNumber.value =
        digits.replace(
          /(\d{4})(?=\d)/g,
          "$1 "
        );

    }
  );


  /* =========================
     SECURITY CODE
     デモ用
  ========================== */

  securityCode?.addEventListener(
    "input",
    () => {

      securityCode.value =
        securityCode.value
          .replace(/\D/g, "")
          .slice(0, 4);

    }
  );


  /* =========================
     INITIAL
  ========================== */

  updatePaymentForm();

});