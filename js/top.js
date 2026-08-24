document.addEventListener("DOMContentLoaded", () => {

  /* =========================
     BEST SELLER FILTER
  ========================= */

  const rankingSelect =
    document.querySelector("#rankingType");

  const rankingForm =
    document.querySelector("#rankingFilterForm");


  if (rankingSelect && rankingForm) {

    rankingSelect.addEventListener("change", () => {
      rankingForm.submit();
    });

  }


  /* =========================
     NEWSLETTER
  ========================= */

  const newsletterButton =
    document.querySelector(".newsletter .button--submit");

  if (newsletterButton) {

    newsletterButton.addEventListener("click", (event) => {

      event.preventDefault();

      alert("デモサイトのため、実際の登録は行われません。");

    });

  }

});