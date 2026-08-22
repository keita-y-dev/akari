document.addEventListener(
  "DOMContentLoaded",
  () => {

    /* =========================
       ELEMENTS
    ========================== */

    const cardForm =
      document.querySelector(
        "#cardForm"
      );

    const paymentInputs =
      document.querySelectorAll(
        'input[name="payment"]'
      );

    const postal =
      document.querySelector(
        "#postal"
      );

    const postalButton =
      document.querySelector(
        "#postalButton"
      );

    const cardNumber =
      document.querySelector(
        "#cardNumber"
      );


    /* =========================
       PAYMENT CHANGE
    ========================== */

    function updatePaymentForm() {

      if (!cardForm) {
        return;
      }


      const selectedPayment =
        document.querySelector(
          'input[name="payment"]:checked'
        );


      if (!selectedPayment) {
        return;
      }


      if (
        selectedPayment.value ===
        "card"
      ) {

        cardForm.classList.remove(
          "is-hidden"
        );

      } else {

        cardForm.classList.add(
          "is-hidden"
        );

      }

    }


    paymentInputs.forEach(
      (input) => {

        input.addEventListener(
          "change",
          updatePaymentForm
        );

      }
    );


    /* =========================
       CARD NUMBER
    ========================== */

    cardNumber?.addEventListener(
      "input",
      () => {

        let value =
          cardNumber.value.replace(
            /\D/g,
            ""
          );


        value =
          value.substring(
            0,
            16
          );


        value =
          value.replace(
            /(\d{4})(?=\d)/g,
            "$1 "
          );


        cardNumber.value =
          value;

      }
    );


    /* =========================
       POSTAL CODE
    ========================== */

    postal?.addEventListener(
      "input",
      () => {

        let value =
          postal.value.replace(
            /\D/g,
            ""
          );


        value =
          value.substring(
            0,
            7
          );


        if (
          value.length > 3
        ) {

          value =
            value.substring(
              0,
              3
            )
            +
            "-"
            +
            value.substring(
              3
            );

        }


        postal.value =
          value;

      }
    );


    /* =========================
       POSTAL SEARCH
       デモ用
    ========================== */

    postalButton?.addEventListener(
      "click",
      () => {

        const postalValue =
          postal.value.replace(
            /\D/g,
            ""
          );


        if (
          postalValue.length !== 7
        ) {

          alert(
            "郵便番号を7桁で入力してください。"
          );

          postal.focus();

          return;

        }


        const prefecture =
          document.querySelector(
            "#prefecture"
          );

        const address =
          document.querySelector(
            "#address"
          );


        if (prefecture) {

          prefecture.value =
            "東京都";

        }


        if (address) {

          address.value =
            "杉並区x-x-x";

        }


        alert(
          "住所を入力しました。"
        );

      }
    );


    /* =========================
       INITIAL
    ========================== */

    updatePaymentForm();

  }
);