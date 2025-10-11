/**
 * Simple Form Validator
 * Provides real-time client-side validation with visual feedback.
 */

const rules = new Map();

// Built-in validators
rules.set('required', (v) => (v ?? '').toString().trim().length > 0 || 'This field is required.');
rules.set('email', (v) => (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Please enter a valid email.'));
rules.set('url', (v) => {
  if (!v) return true; try { new URL(v); return true; } catch { return 'Please enter a valid URL.'; }
});
rules.set('number', (v) => (v === '' || !isNaN(Number(v)) || 'Please enter a valid number.'));

function minLength(n) { return (v) => ((v ?? '').length >= n || `Must be at least ${n} characters.`); }
function maxLength(n) { return (v) => ((v ?? '').length <= n || `Must be at most ${n} characters.`); }
function pattern(re) { return (v) => (re.test(v) || 'Invalid format.'); }
function match(selector) { return (v, field) => {
  const other = document.querySelector(selector);
  return (other && v === other.value) || 'Values do not match.';
}; }

function getFieldRules(field) {
  const result = [];
  if (field.dataset.required !== undefined || field.dataset.validate?.includes('required')) result.push(rules.get('required'));
  if (field.dataset.validate?.includes('email')) result.push(rules.get('email'));
  if (field.dataset.validate?.includes('url')) result.push(rules.get('url'));
  if (field.dataset.validate?.includes('number')) result.push(rules.get('number'));
  if (field.dataset.minLength) result.push(minLength(Number(field.dataset.minLength)));
  if (field.dataset.maxLength) result.push(maxLength(Number(field.dataset.maxLength)));
  if (field.dataset.pattern) result.push(pattern(new RegExp(field.dataset.pattern)));
  if (field.dataset.match) result.push(match(field.dataset.match));
  return result;
}

function ensureErrorSpan(field) {
  let span = field.nextElementSibling;
  if (!span || !span.classList.contains('text-red-500')) {
    span = document.createElement('span');
    span.className = 'text-red-500 text-sm mt-1';
    field.insertAdjacentElement('afterend', span);
  }
  return span;
}

function setValid(field) {
  field.classList.remove('form-input-error');
  field.classList.add('form-input-success');
  const span = ensureErrorSpan(field);
  span.textContent = '';
}

function setInvalid(field, message) {
  field.classList.add('form-input-error');
  field.classList.remove('form-input-success');
  const span = ensureErrorSpan(field);
  span.textContent = field.dataset.errorMessage || message || 'Invalid value.';
  // Shake animation for feedback
  field.classList.add('animate-shake');
  field.addEventListener('animationend', () => field.classList.remove('animate-shake'), { once: true });
}

function validateField(field) {
  const validators = getFieldRules(field);
  for (const validator of validators) {
    const result = validator(field.value, field);
    if (result !== true) { setInvalid(field, result); return false; }
  }
  setValid(field);
  return true;
}

function validateForm(form) {
  const fields = Array.from(form.querySelectorAll('[data-validate], [data-required], [data-min-length], [data-max-length], [data-pattern], [data-match]'));
  let firstInvalid = null;
  let ok = true;
  for (const f of fields) {
    if (!validateField(f)) { ok = false; if (!firstInvalid) firstInvalid = f; }
  }
  if (!ok) { firstInvalid?.focus(); }
  return ok;
}

function attachCounters(form) {
  form.querySelectorAll('textarea[maxlength], input[maxlength]').forEach(el => {
    const max = Number(el.getAttribute('maxlength'));
    if (!max) return;
    let counter = el.parentElement?.querySelector('.char-counter');
    if (!counter) {
      counter = document.createElement('div');
      counter.className = 'char-counter';
      el.insertAdjacentElement('afterend', counter);
    }
    const update = () => {
      const len = el.value.length;
      counter.textContent = `${len} / ${max} characters`;
      counter.classList.remove('warning', 'error');
      if (len >= max) counter.classList.add('error');
      else if (len >= Math.floor(max * 0.9)) counter.classList.add('warning');
    };
    el.addEventListener('input', update);
    update();
  });
}

function attachPasswordStrength(form) {
  form.querySelectorAll('input[type="password"]').forEach(el => {
    let bar = el.parentElement?.querySelector('.password-strength');
    if (!bar) {
      bar = document.createElement('div');
      bar.className = 'password-strength';
      el.insertAdjacentElement('afterend', bar);
    }
    const score = (value) => {
      let s = 0; if (!value) return 0;
      if (value.length >= 8) s++;
      if (/[A-Z]/.test(value)) s++;
      if (/[a-z]/.test(value)) s++;
      if (/[0-9]/.test(value)) s++;
      if (/[^A-Za-z0-9]/.test(value)) s++;
      return Math.min(s, 4);
    };
    const update = () => {
      const s = score(el.value);
      const pct = (s / 4) * 100;
      bar.style.width = pct + '%';
      bar.title = ['Weak', 'Fair', 'Good', 'Strong', 'Strong'][s];
    };
    el.addEventListener('input', update);
    update();
  });
}

function init() {
  document.querySelectorAll('form[data-validate-form]')
    .forEach(form => {
      form.addEventListener('submit', (e) => { if (!validateForm(form)) e.preventDefault(); });
      form.querySelectorAll('[data-validate], [data-required], [data-min-length], [data-max-length], [data-pattern], [data-match]')
        .forEach(field => {
          field.addEventListener('input', () => validateField(field));
          field.addEventListener('blur', () => validateField(field));
        });
      attachCounters(form);
      attachPasswordStrength(form);
    });
}

export default { init, validateField, validateForm, addRule: (name, fn) => rules.set(name, fn) };
