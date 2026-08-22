document.addEventListener("DOMContentLoaded", () => {
  const menuButton = document.querySelector(".header__menu");
  const globalNav = document.querySelector(".global-nav");

  if (!menuButton || !globalNav) return;

  menuButton.addEventListener("click", () => {
    const isOpen = menuButton.classList.toggle("is-open");
    globalNav.classList.toggle("is-open", isOpen);
    menuButton.setAttribute("aria-expanded", String(isOpen));
    menuButton.setAttribute(
      "aria-label",
      isOpen ? "メニューを閉じる" : "メニューを開く"
    );
    document.body.classList.toggle("is-menu-open", isOpen);
  });

  globalNav.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      menuButton.classList.remove("is-open");
      globalNav.classList.remove("is-open");
      menuButton.setAttribute("aria-expanded", "false");
      menuButton.setAttribute("aria-label", "メニューを開く");
      document.body.classList.remove("is-menu-open");
    });
  });
});
