'use strict';

document.documentElement.classList.add('js-ready');

/* Mobile navigation */
const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#navigation');

function closeMenu() {
  if (!menuButton || !navigation) return;
  menuButton.setAttribute('aria-expanded', 'false');
  navigation.classList.remove('is-open');
}

if (menuButton && navigation) {
  menuButton.addEventListener('click', () => {
    const opening = menuButton.getAttribute('aria-expanded') !== 'true';
    menuButton.setAttribute('aria-expanded', String(opening));
    navigation.classList.toggle('is-open', opening);
  });
  navigation.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && menuButton.getAttribute('aria-expanded') === 'true') {
      closeMenu();
      menuButton.focus();
    }
  });
  window.matchMedia('(min-width: 1001px)').addEventListener('change', event => {
    if (event.matches) closeMenu();
  });
}

/* Enlarged product screens */
const screenDialog = document.querySelector('#screen-dialog');
let screenOpener;

if (screenDialog) {
  const target = screenDialog.querySelector('#dialog-image');
  const title = screenDialog.querySelector('#screen-dialog-title');
  const note = screenDialog.querySelector('#screen-dialog-note');
  const closeButton = screenDialog.querySelector('.dialog-close');
  document.querySelectorAll('[data-enlarge]').forEach(button => {
    button.addEventListener('click', () => {
      const source = button.querySelector('img');
      if (!source || !target || !title) return;
      screenOpener = button;
      target.src = button.dataset.full || source.currentSrc || source.src;
      target.alt = source.alt;
      title.textContent = button.dataset.title || 'Product screen';
      const status = document.querySelector('[data-capture-status]');
      if (note && status) note.textContent = status.textContent;
      screenDialog.showModal();
      document.body.classList.add('dialog-open');
      closeButton.focus();
    });
  });
  closeButton.addEventListener('click', () => screenDialog.close());
  screenDialog.addEventListener('click', event => {
    if (event.target !== screenDialog) return;
    const bounds = screenDialog.getBoundingClientRect();
    if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) screenDialog.close();
  });
  screenDialog.addEventListener('close', () => {
    document.body.classList.remove('dialog-open');
    if (screenOpener?.isConnected) screenOpener.focus();
  });
}

/* Email-draft enquiry form: prepares a message on the visitor's device, stores nothing, sends nothing. */
const enquiryForm = document.querySelector('form[data-mail-to]');

if (enquiryForm) {
  enquiryForm.hidden = false;
  const receiver = enquiryForm.dataset.mailTo;
  const subject = enquiryForm.dataset.mailSubject || 'PHP Ledger enquiry';
  const intro = enquiryForm.dataset.mailIntro || 'Hello PHP Ledger team,';
  const fields = [...enquiryForm.querySelectorAll('[data-label]')];
  const status = enquiryForm.querySelector('.form-status');
  const errors = enquiryForm.querySelector('.form-errors');
  const preview = enquiryForm.querySelector('#message-preview');
  const previewDetails = enquiryForm.querySelector('#draft-preview');
  const topic = enquiryForm.querySelector('[data-topic]');

  function selectEnquiryTopic() {
    if (topic && window.location.hash === '#enquiry-pilot') {
      const pilot = [...topic.options].find(option => /pilot/i.test(option.textContent));
      if (pilot) topic.value = pilot.value;
    }
  }
  selectEnquiryTopic();
  window.addEventListener('hashchange', selectEnquiryTopic);

  function prepareMessage() {
    const invalid = [];
    const lines = [];
    errors.replaceChildren();
    status.textContent = '';
    fields.forEach(field => {
      const value = field.value.trim();
      const error = enquiryForm.querySelector(`#error-${field.name}`);
      if (error) error.textContent = '';
      field.removeAttribute('aria-invalid');
      field.removeAttribute('aria-describedby');
      if ((field.required && !value) || (value && !field.checkValidity())) {
        invalid.push(field);
        if (error) {
          error.textContent = field.validity.typeMismatch ? 'Enter a valid email address, for example name@example.com.' : (field.dataset.error || `Please complete ${field.dataset.label.toLowerCase()}.`);
          field.setAttribute('aria-invalid', 'true');
          field.setAttribute('aria-describedby', error.id);
        }
        return;
      }
      if (value) lines.push(field.tagName === 'TEXTAREA' ? `${field.dataset.label}:\n${value}` : `${field.dataset.label}: ${value}`);
    });
    errors.hidden = invalid.length === 0;
    if (invalid.length) {
      const message = document.createElement('p');
      message.textContent = 'Please complete these details before preparing your message:';
      const list = document.createElement('ul');
      invalid.forEach(field => {
        const item = document.createElement('li');
        const link = document.createElement('a');
        link.href = `#${field.id}`;
        link.textContent = field.dataset.label;
        link.addEventListener('click', event => {
          event.preventDefault();
          field.focus();
        });
        item.append(link);
        list.append(item);
      });
      errors.append(message, list);
      errors.focus();
      return null;
    }
    const body = `${intro}\n\n${lines.join('\n\n')}\n\nPlease let me know how to proceed.\n`;
    preview.value = `To: ${receiver}\nSubject: ${subject}\n\n${body}`;
    previewDetails.open = true;
    return { body, text: preview.value };
  }

  enquiryForm.addEventListener('submit', event => {
    event.preventDefault();
    const message = prepareMessage();
    if (!message) return;
    status.textContent = 'Opening your email application. Review and send the draft there. If no draft opens, use Copy message or email us directly.';
    window.location.href = `mailto:${receiver}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(message.body)}`;
  });

  enquiryForm.querySelector('#copy-message').addEventListener('click', async () => {
    const message = prepareMessage();
    if (!message) return;
    try {
      if (!navigator.clipboard?.writeText) throw new Error('Clipboard unavailable');
      await navigator.clipboard.writeText(message.text);
      status.textContent = `Message copied. Paste it into your email application, review it, and send it to ${receiver}.`;
    } catch {
      preview.focus();
      preview.select();
      status.textContent = 'Automatic copying is unavailable. Your message is selected below; copy it and send it using your email application.';
    }
  });
}
