(() => {
  const STORAGE_KEY = "akariFavorites";

  function get() {
    try {
      const favorites = JSON.parse(
        localStorage.getItem(STORAGE_KEY)
      );

      return Array.isArray(favorites)
        ? favorites.map(String)
        : [];
    } catch (error) {
      return [];
    }
  }

  function save(favorites) {
    const normalized = [...new Set(
      favorites.map(String)
    )];

    localStorage.setItem(
      STORAGE_KEY,
      JSON.stringify(normalized)
    );
  }

  function has(productId) {
    return get().includes(String(productId));
  }

  function toggle(productId) {
    const id = String(productId);
    const favorites = get();
    const index = favorites.indexOf(id);

    if (index === -1) {
      favorites.push(id);
      save(favorites);
      return true;
    }

    favorites.splice(index, 1);
    save(favorites);
    return false;
  }

  window.AKARI_FAVORITES = {
    storageKey: STORAGE_KEY,
    get,
    save,
    has,
    toggle,
  };
})();
