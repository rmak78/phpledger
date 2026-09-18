// Extra attribute-driven behaviour for the reports screens (keep small).
// data-copy="<text>": copies <text> to the clipboard and briefly shows a status message in a
// sibling [data-copy-status] element (falls back to the next element if none is found nearby).
// Used on Connections for the MCP endpoint and the show-once personal token.
document.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-copy]');
  if (!btn) return;
  const text = btn.getAttribute('data-copy') || '';
  const status = btn.parentElement?.querySelector('[data-copy-status]') || btn.nextElementSibling;
  const announce = (ok) => {
    if (!status) return;
    status.textContent = ok ? 'Copied' : 'Copy failed';
    window.clearTimeout(status._plTimer);
    status._plTimer = window.setTimeout(() => { status.textContent = ''; }, 1600);
  };
  if (navigator.clipboard?.writeText) {
    navigator.clipboard.writeText(text).then(() => announce(true), () => announce(false));
  } else {
    announce(false);
  }
});
