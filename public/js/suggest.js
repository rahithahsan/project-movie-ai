function debounce(fn, ms = 300) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), ms);
  };
}

document.addEventListener("DOMContentLoaded", () => {
  const inp  = document.getElementById("searchBox");
  const list = document.getElementById("suggestList");

  /* navigate on click */
  list.addEventListener("click", (e) => {
    const id = e.target.closest("[data-id]")?.dataset.id;
    if (id) window.location = `?url=movies/search&id=${id}`;
  });

  /* live fetch */
  inp.addEventListener(
    "input",
    debounce(async (e) => {
      const q = e.target.value.trim();
      if (q.length < 3) {
        list.innerHTML = "";
        return;
      }

      const res = await fetch(
        `?url=movies/suggest&term=${encodeURIComponent(q)}`
      );
      const data = await res.json();

      list.innerHTML = data
        .map(
          (hit) => `
        <button class="list-group-item list-group-item-action d-flex justify-content-between"
                data-id="${hit.id}">
          <span>${hit.title} (${hit.year})</span>
          <span class="text-muted small">↵</span>
        </button>`
        )
        .join("");
    })
  );
});
