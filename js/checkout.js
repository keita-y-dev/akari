document.addEventListener("DOMContentLoaded", () => {

  /* =========================
     ELEMENTS
  ========================== */

  const form =
    document.querySelector("#checkoutForm");

  const cardForm =
    document.querySelector("#cardForm");

  const paymentInputs =
    document.querySelectorAll(
      'input[name="payment"]'
    );

  const agreement =
    document.querySelector("#agreement");

  const postal =
    document.querySelector("#postal");

  const postalButton =
    document.querySelector("#postalButton");

  /* =========================
     PAYMENT CHANGE
  ========================== */

  paymentInputs.forEach((input) => {

    input.addEventListener("change", () => {

      if (input.value === "card" && input.checked) {

        cardForm.classList.remove("is-hidden");

      }


      if (input.value === "cod" && input.checked) {

        cardForm.classList.add("is-hidden");

      }

    });

  });


  /* =========================
     CARD NUMBER
  ========================== */

  const cardNumber =
    document.querySelector("#cardNumber");


  cardNumber.addEventListener("input", () => {

    let value =
      cardNumber.value.replace(/\D/g, "");

    value =
      value.substring(0, 16);

    value =
      value.replace(/(\d{4})(?=\d)/g, "$1 ");

    cardNumber.value =
      value;

  });


  /* =========================
     POSTAL CODE
  ========================== */

  postal.addEventListener("input", () => {

    let value =
      postal.value.replace(/\D/g, "");

    value =
      value.substring(0, 7);

    if (value.length > 3) {

      value =
        value.substring(0, 3)
        + "-"
        + value.substring(3);

    }

    postal.value =
      value;

  });


  /*
    デモ用の住所検索
  */

  postalButton.addEventListener("click", () => {

    const postalValue =
      postal.value.replace(/\D/g, "");


    if (postalValue.length !== 7) {

      alert(
        "郵便番号を7桁で入力してください。"
      );

      postal.focus();

      return;

    }


    /*
      デモなので固定値を入力
    */

    const prefecture =
      document.querySelector("#prefecture");

    const address =
      document.querySelector("#address");


    prefecture.value =
      "東京都";

    address.value =
      "杉並区x-x-x";


    alert(
      "住所を入力しました。"
    );

  });


  /* =========================
     VALIDATION
  ========================== */

  function validateForm() {

    let isValid = true;


    /*
      既存エラー削除
    */

    document
      .querySelectorAll(".error-message")
      .forEach((error) => {
        error.remove();
      });


    document
      .querySelectorAll(".form-error")
      .forEach((element) => {
        element.classList.remove("form-error");
      });


    /*
      required
    */

    const requiredFields =
      form.querySelectorAll(
        "input[required], select[required]"
      );


    requiredFields.forEach((field) => {

      /*
        非表示になっているカード入力は除外
      */

      if (
        cardForm.classList.contains("is-hidden")
        &&
        cardForm.contains(field)
      ) {

        return;

      }


      if (!field.value.trim()) {

        isValid = false;

        field.classList.add("form-error");

        showError(
          field,
          "必須項目です。"
        );

      }

    });


    /*
      email
    */

    const email =
      document.querySelector("#email");


    if (
      email.value
      &&
      !email.validity.valid
    ) {

      isValid = false;

      email.classList.add("form-error");

      showError(
        email,
        "メールアドレスの形式を確認してください。"
      );

    }


    /*
      agreement
    */

    if (!agreement.checked) {

      isValid = false;

      agreement
        .closest(".agreement")
        .classList.add("agreement-error");

    }


    return isValid;

  }


  /* =========================
     ERROR
  ========================== */

  function showError(
    element,
    message
  ) {

    const error =
      document.createElement("p");

    error.className =
      "error-message";

    error.textContent =
      message;

    element.parentElement
      .appendChild(error);

  }


  /* =========================
     FORM SUBMIT
  ========================== */

form.addEventListener("submit", (event) => {

  event.preventDefault();

  const valid = validateForm();

  if (!valid) {

    const firstError =
      document.querySelector(".form-error");

    if (firstError) {
      firstError.focus();
    }

    if (!agreement.checked) {
      agreement.focus();
    }

    return;
  }


  /* =========================
     INPUT DATA
  ========================== */

  const orderData = {

    lastName:
      document.querySelector('[name="lastName"]').value,

    firstName:
      document.querySelector('[name="firstName"]').value,

    lastNameKana:
      document.querySelector('[name="lastNameKana"]').value,

    firstNameKana:
      document.querySelector('[name="firstNameKana"]').value,

    email:
      document.querySelector('[name="email"]').value,

    phone:
      document.querySelector('[name="phone"]').value,

    postal:
      document.querySelector('[name="postal"]').value,

    prefecture:
      document.querySelector('[name="prefecture"]').value,

    address:
      document.querySelector('[name="address"]').value,

    building:
      document.querySelector('[name="building"]').value,

    deliveryDate:
      document.querySelector('[name="deliveryDate"]').value,

    deliveryTime:
      document.querySelector('[name="deliveryTime"]').value,

    payment:
      document.querySelector(
        'input[name="payment"]:checked'
      ).value,

    gift:
      document.querySelector("#gift").checked,

    note:
      document.querySelector('[name="note"]').value

  };


  /* =========================
     SAVE
  ========================== */

  sessionStorage.setItem(
    "orderData",
    JSON.stringify(orderData)
  );


  /* =========================
     CONFIRM PAGE
  ========================== */

  window.location.href =
    "order-confirm.php";

});

});