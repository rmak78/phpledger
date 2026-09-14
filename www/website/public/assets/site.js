'use strict';

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

document.querySelectorAll('[role="tablist"]').forEach(tablist => {
  const stepTabs = [...tablist.querySelectorAll('[role="tab"][data-step]')];
  function selectStep(tab, focus = false) {
    stepTabs.forEach(item => {
      const selected = item === tab;
      item.setAttribute('aria-selected', String(selected));
      item.tabIndex = selected ? 0 : -1;
      const panel = document.getElementById(item.getAttribute('aria-controls'));
      if (panel) panel.hidden = !selected;
    });
    if (focus) tab.focus();
  }
  stepTabs.forEach((tab, index) => {
    tab.addEventListener('click', () => selectStep(tab));
    tab.addEventListener('keydown', event => {
      let nextIndex;
      if (event.key === 'ArrowRight') nextIndex = (index + 1) % stepTabs.length;
      if (event.key === 'ArrowLeft') nextIndex = (index - 1 + stepTabs.length) % stepTabs.length;
      if (event.key === 'Home') nextIndex = 0;
      if (event.key === 'End') nextIndex = stepTabs.length - 1;
      if (nextIndex !== undefined) {
        event.preventDefault();
        selectStep(stepTabs[nextIndex], true);
      }
    });
  });
});

const screenDialog = document.querySelector('#screen-dialog');
let screenOpener;

if (screenDialog) {
  document.querySelectorAll('[data-enlarge]').forEach(button => {
    button.addEventListener('click', () => {
      const source = button.querySelector('img');
      const target = document.querySelector('#dialog-image');
      const title = document.querySelector('#screen-dialog-title');
      if (!source || !target || !title) return;
      screenOpener = button;
      target.src = source.currentSrc || source.src;
      target.alt = source.alt;
      title.textContent = button.dataset.title || 'Product screen';
      const caption = document.querySelector('#screenshot-status');
      if (caption) screenDialog.querySelector('p').textContent = caption.textContent;
      screenDialog.showModal();
      document.body.classList.add('dialog-open');
      screenDialog.querySelector('.dialog-close').focus();
    });
  });
  screenDialog.querySelector('.dialog-close').addEventListener('click', () => screenDialog.close());
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

const pilotForm = document.querySelector('#pilot-form');

if (pilotForm) {
  const receiver = 'rmak78@gmail.com';
  const subject = 'PHP Ledger pilot interest';
  const fieldNames = ['role', 'country', 'business', 'challenge'];
  const fieldLabels = {
    role: 'Choose your role.',
    country: 'Enter your country.',
    business: 'Describe your business type.',
    challenge: 'Tell us what you would like help with.'
  };
  const status = document.querySelector('#form-status');
  const errors = document.querySelector('#form-errors');
  const preview = document.querySelector('#message-preview');
  const previewDetails = document.querySelector('#draft-preview');

  function prepareMessage() {
    const values = {};
    const invalid = [];
    errors.replaceChildren();
    status.textContent = '';
    fieldNames.forEach(name => {
      const field = pilotForm.elements.namedItem(name);
      values[name] = field.value.trim();
      const error = document.querySelector(`#error-${name}`);
      error.textContent = '';
      field.removeAttribute('aria-invalid');
      field.removeAttribute('aria-describedby');
      if (!values[name]) {
        invalid.push({ name, field });
        error.textContent = fieldLabels[name];
        field.setAttribute('aria-invalid', 'true');
        field.setAttribute('aria-describedby', error.id);
      }
    });
    errors.hidden = invalid.length === 0;
    if (invalid.length) {
      const message = document.createElement('p');
      message.textContent = 'Please complete these details before preparing your message:';
      const list = document.createElement('ul');
      invalid.forEach(({ name, field }) => {
        const item = document.createElement('li');
        const link = document.createElement('a');
        link.href = `#${field.id}`;
        link.textContent = fieldLabels[name];
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
    const body = `Hello PHP Ledger team,\n\nI would like to discuss the PHP Ledger pilot.\n\nRole: ${values.role}\nCountry: ${values.country}\nBusiness type: ${values.business}\n\nWhat I would like help with:\n${values.challenge}\n\nPlease let me know whether the pilot would be a suitable fit.\n`;
    preview.value = `To: ${receiver}\nSubject: ${subject}\n\n${body}`;
    previewDetails.open = true;
    return { body, text: preview.value };
  }

  pilotForm.addEventListener('submit', event => {
    event.preventDefault();
    const message = prepareMessage();
    if (!message) return;
    status.textContent = 'Opening your email application. Review and send the draft there. If no draft opens, use Copy message or email us directly.';
    window.location.href = `mailto:${receiver}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(message.body)}`;
  });

  document.querySelector('#copy-message').addEventListener('click', async () => {
    const message = prepareMessage();
    if (!message) return;
    try {
      if (!navigator.clipboard?.writeText) throw new Error('Clipboard unavailable');
      await navigator.clipboard.writeText(message.text);
      status.textContent = 'Message copied. Paste it into your email application, review it, and send it to rmak78@gmail.com.';
    } catch {
      preview.focus();
      preview.select();
      status.textContent = 'Automatic copying is unavailable. Your message is selected below; copy it and send it using your email application.';
    }
  });
}
