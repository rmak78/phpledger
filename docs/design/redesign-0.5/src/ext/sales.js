// Extra attribute-driven behaviour for the sales screens (keep small).
(() => {
  document.addEventListener('click', (e) => {
    // data-fill="<inputId>" data-fill-value="<value>": quick-fill an allocation/amount input
    // (e.g. "Full" beside an allocation cell, or "Use remaining balance").
    const fillBtn = e.target.closest('[data-fill]');
    if (fillBtn) {
      const target = document.getElementById(fillBtn.dataset.fill);
      if (target) { target.value = fillBtn.dataset.fillValue ?? ''; target.focus(); }
    }
  });
})();
