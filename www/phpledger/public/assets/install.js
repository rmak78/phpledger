/* Each request applies one versioned migration; the server stops on any error. */
'use strict';
const installationForm = document.querySelector('form[data-install-continue]');
if (installationForm instanceof HTMLFormElement) {
  window.setTimeout(() => installationForm.requestSubmit(), 250);
}
