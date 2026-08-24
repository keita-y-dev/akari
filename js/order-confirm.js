document.addEventListener("DOMContentLoaded", () => {

  /* =========================
     ELEMENTS
  ========================== */

  const changeButtons =
    document.querySelectorAll(".change-button");

  const orderButton =
    document.querySelector("#orderButton");

  const backButton =
    document.querySelector("#backButton");

  const confirmation =
    document.querySelector(".confirmation");

  const complete =
    document.querySelector("#complete");


  /* =========================
     CHANGE BUTTON
  ========================== */

  changeButtons.forEach((button) => {

    button.addEventListener("click", () => {

      /*
       * 注文入力ページへ戻る
       *
       * 実際のサイトでは、
       * sessionStorageなどを使って
       * 入力内容を保持した状態で戻す。
       */

      window.location.href =
        "checkout";

    });

  });


  /* =========================
     BACK BUTTON
  ========================== */

  backButton.addEventListener("click", () => {

    window.location.href =
      "checkout";

  });


  /* =========================
     ORDER COMPLETE
  ========================== */

  orderButton.addEventListener("click", () => {

    const result =
      window.confirm(
        "この内容で注文を確定しますか？"
      );


    if (!result) {
      return;
    }


    /*
     * 確認画面を非表示
     */

    confirmation.style.display =
      "none";


    /*
     * 完了画面を表示
     */

    complete.classList.add(
      "is-visible"
    );

    complete.setAttribute(
      "aria-hidden",
      "false"
    );


    /*
     * ページ上部へ移動
     */

    window.scrollTo({
      top: 0,
      behavior: "smooth"
    });

  });


});