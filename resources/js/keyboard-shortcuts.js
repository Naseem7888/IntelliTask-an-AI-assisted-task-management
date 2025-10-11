// Keyboard Shortcuts Manager

const isMac = navigator.platform.toUpperCase().includes('MAC');
const registry = new Map();
const contexts = new Set();
let initialized = false;
let disabled = localStorage.getItem('shortcuts.disabled') === '1';

function norm(keyCombo) {
  return keyCombo
    .toLowerCase()
    .replace('cmd', 'meta')
    .split('+')
    .map(k => k.trim())
    .sort()
    .join('+');
}

function comboFromEvent(e) {
  const mods = [];
  if (e.ctrlKey) mods.push('ctrl');
  if (e.shiftKey) mods.push('shift');
  if (e.altKey) mods.push('alt');
  if (e.metaKey) mods.push('meta');
  const key = e.key.toLowerCase();
  if (!['control','shift','alt','meta'].includes(key)) mods.push(key);
  return mods.sort().join('+');
}

function inEditableTarget(e) {
  const t = e.target;
  const isInput = t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.isContentEditable;
  return isInput;
}

function onKeyDown(e) {
  if (disabled) return; // allow user to disable shortcuts
  const combo = comboFromEvent(e);
  const entry = registry.get(combo);
  if (!entry) return;

  // Skip when in editable unless explicitly allowed
  if (inEditableTarget(e) && !entry.allowInInputs) return;

  e.preventDefault();
  entry.callback(e);
}

function init() {
  if (initialized) return; initialized = true;
  document.addEventListener('keydown', onKeyDown);
  // flag for other modules to detect active keyboard manager
  window.__keyboardManagerActive = true;

  // Pre-register common shortcuts
  register(isMac ? 'meta+k' : 'ctrl+k', () => {
    const el = document.querySelector('[data-search-input], input[type="search"], input[name*="search" i]');
    el?.focus();
  }, 'Focus search');

  register(isMac ? 'meta+n' : 'ctrl+n', () => {
    try { window.Livewire?.dispatch('showCreateForm'); } catch {}
  }, 'New task');

  register(isMac ? 'meta+/' : 'ctrl+/', () => showHelp(), 'Show keyboard shortcuts');

  register('escape', () => {
    document.querySelectorAll('.modal-backdrop').forEach(n => n.click());
  }, 'Close modals');

  register(isMac ? 'meta+enter' : 'ctrl+enter', () => {
    const active = document.activeElement;
    const form = active?.closest?.('form');
    form?.requestSubmit();
  }, 'Submit form');

  register('alt+1', () => document.querySelector('[data-filter="all"]')?.click(), 'Filter: All');
  register('alt+2', () => document.querySelector('[data-filter="pending"]')?.click(), 'Filter: Pending');
  register('alt+3', () => document.querySelector('[data-filter="completed"]')?.click(), 'Filter: Completed');

  register(isMac ? 'meta+s' : 'ctrl+s', () => {
    // Attempt to save current task if an explicit save button exists
    document.querySelector('[data-action="save-task"]')?.click();
  }, 'Save task');
}

function register(key, callback, description, { allowInInputs = false } = {}) {
  registry.set(norm(key), { callback, description, allowInInputs });
}

function unregister(key) { registry.delete(norm(key)); }

function showHelp() {
  // Build a simple help modal
  const overlay = document.createElement('div');
  overlay.className = 'modal-backdrop';
  const card = document.createElement('div');
  card.className = 'card card-elevated';
  card.style.position = 'fixed';
  card.style.top = '50%';
  card.style.left = '50%';
  card.style.transform = 'translate(-50%, -50%)';
  card.style.minWidth = '360px';
  card.style.maxWidth = '90vw';
  card.style.padding = '1rem';

  const title = document.createElement('h3');
  title.className = 'card-title';
  title.textContent = 'Keyboard Shortcuts';
  card.appendChild(title);

  const table = document.createElement('table');
  table.style.width = '100%';
  table.style.borderCollapse = 'collapse';

  for (const [combo, { description }] of registry.entries()) {
    const tr = document.createElement('tr');
    const tdKey = document.createElement('td');
    tdKey.textContent = combo.toUpperCase();
    tdKey.style.padding = '0.25rem 0.5rem';
    tdKey.style.fontWeight = '600';
    const tdDesc = document.createElement('td');
    tdDesc.textContent = description || '';
    tdDesc.style.padding = '0.25rem 0.5rem';
    tr.appendChild(tdKey);
    tr.appendChild(tdDesc);
    table.appendChild(tr);
  }

  card.appendChild(table);
  overlay.appendChild(card);
  overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.remove(); });
  document.addEventListener('keydown', function esc(ev) { if (ev.key === 'Escape') { overlay.remove(); document.removeEventListener('keydown', esc); } });
  document.body.appendChild(overlay);
}

function getShortcuts() { return Array.from(registry.entries()); }
function enableContext(name) { contexts.add(name); }
function disableContext(name) { contexts.delete(name); }

export default { init, register, unregister, showHelp, getShortcuts, enableContext, disableContext, setDisabled };

// Allow user to toggle shortcuts preference
export function setDisabled(v) {
  disabled = !!v; localStorage.setItem('shortcuts.disabled', disabled ? '1' : '0');
}
