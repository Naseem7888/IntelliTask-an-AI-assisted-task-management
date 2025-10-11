// Toast Notification Manager
// Provides success, error, warning, info toasts with auto-dismiss and progress bar.

const DEFAULTS = {
  duration: 4000,
  position: 'top-right', // 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left'
  closable: true,
  icon: true,
};

const POSITIONS = {
  'top-right': { top: '1rem', right: '1rem' },
  'top-left': { top: '1rem', left: '1rem' },
  'bottom-right': { bottom: '1rem', right: '1rem' },
  'bottom-left': { bottom: '1rem', left: '1rem' },
};

function ensureContainer(position) {
  const id = `toast-container-${position}`;
  let container = document.getElementById(id);
  if (!container) {
    container = document.createElement('div');
    container.id = id;
    container.style.position = 'fixed';
    container.style.zIndex = getComputedStyle(document.documentElement).getPropertyValue('--z-toast')?.trim() || '90';
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '0.5rem';
    container.style.pointerEvents = 'none';
    const pos = POSITIONS[position] || POSITIONS['top-right'];
    Object.assign(container.style, pos);
    document.body.appendChild(container);
  }
  return container;
}

function createToastElement(id, message, type, options) {
  const toast = document.createElement('div');
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'polite');
  toast.dataset.toastId = id;
  toast.className = `toast-bounce-in alert alert-${type}`; // relies on components.css + micro-interactions.css
  toast.style.pointerEvents = 'auto';
  toast.style.minWidth = '260px';

  const inner = document.createElement('div');
  inner.style.display = 'flex';
  inner.style.alignItems = 'start';
  inner.style.gap = '0.5rem';

  if (options.icon !== false) {
    const icon = document.createElement('span');
    icon.setAttribute('aria-hidden', 'true');
    icon.style.marginTop = '0.15rem';
    icon.innerHTML = {
      success: '✔️',
      error: '❌',
      warning: '⚠️',
      info: 'ℹ️',
    }[type] || 'ℹ️';
    inner.appendChild(icon);
  }

  const content = document.createElement('div');
  content.style.flex = '1';
  content.textContent = message;
  inner.appendChild(content);

  if (options.closable) {
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'close';
    close.setAttribute('aria-label', 'Close');
    close.textContent = '×';
    close.addEventListener('click', () => removeToast(toast));
    inner.appendChild(close);
  }

  toast.appendChild(inner);

  // Progress bar
  const progressWrap = document.createElement('div');
  progressWrap.className = 'progress';
  const progress = document.createElement('div');
  progress.className = 'toast-progress';
  progressWrap.appendChild(progress);
  toast.appendChild(progressWrap);

  return toast;
}

function removeToast(toast) {
  if (!toast) return;
  toast.classList.add('toast-leave');
  toast.classList.remove('toast-bounce-in');
  // allow CSS transition to run before removal
  setTimeout(() => toast.remove(), 300);
}

function show(message, type = 'info', opts = {}) {
  const options = { ...DEFAULTS, ...opts };
  const id = `toast-${Date.now()}-${Math.random().toString(36).slice(2)}`;
  const container = ensureContainer(options.position);
  const toast = createToastElement(id, message, type, options);
  container.appendChild(toast);

  // progress countdown
  const progress = toast.querySelector('.toast-progress');
  if (options.duration === 0) {
    progress.style.display = 'none';
  } else {
    // animate linearly to 100%
    progress.style.transition = `width ${options.duration}ms linear`;
    requestAnimationFrame(() => { progress.style.width = '100%'; });
  }

  let timeoutId;
  if (options.duration && options.duration > 0) {
    timeoutId = setTimeout(() => removeToast(toast), options.duration);
  }

  // Allow keyboard dismissal
  toast.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      removeToast(toast);
    }
  });

  return {
    id,
    dismiss: () => {
      clearTimeout(timeoutId);
      removeToast(toast);
    },
    element: toast,
  };
}

export default {
  show,
  success: (m, o) => show(m, 'success', o),
  error: (m, o) => show(m, 'error', o),
  warning: (m, o) => show(m, 'warning', o),
  info: (m, o) => show(m, 'info', o),
};
