document.addEventListener("DOMContentLoaded", () => {

  /* =========================
     BEST SELLER FILTER
  ========================= */

  const rankingSelect =
    document.querySelector("#rankingType");

  const rankingForm =
    document.querySelector("#rankingFilterForm");


  if (!rankingSelect || !rankingForm) {
    return;
  }


  rankingSelect.addEventListener("change", () => {

    rankingForm.submit();

  });

});