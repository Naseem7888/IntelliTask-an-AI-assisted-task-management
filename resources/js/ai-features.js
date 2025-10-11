/**
 * AI Features Module
 * This file contains helper functions for AI-related UI interactions.
 */

/**
 * Debounces a function to limit the rate at which it gets called.
 * @param {Function} func The function to debounce.
 * @param {number} delay The delay in milliseconds.
 * @returns {Function} The debounced function.
 */
function debounce(func, delay = 300) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Auto-resizes a textarea to fit its content.
 * @param {HTMLElement} element The textarea element.
 */
function autoResizeTextarea(element) {
    element.style.height = 'auto';
    element.style.height = (element.scrollHeight) + 'px';
}

/**
 * Copies text to the clipboard.
 * @param {string} text The text to copy.
 * @returns {Promise<void>}
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        window.toast?.success('Copied to clipboard');
    } catch (err) {
        window.toast?.error('Failed to copy');
    }
}

// Deprecated keyboard listener was removed to avoid double triggering.

/**
 * Shows a loading state on a button or element with a spinner.
 * @param {HTMLElement} el
 * @param {string} [message]
 */
function showLoadingState(el, message = '') {
    if (!el) return;
    el.dataset.originalContent = el.innerHTML;
    el.classList.add('btn-loading');
    el.setAttribute('disabled', 'true');
    const spinner = document.createElement('span');
    spinner.className = 'spinner';
    el.innerHTML = `${message ? `<span class="mr-2">${message}</span>` : ''}`;
    el.appendChild(spinner);
}

/**
 * Hides the loading state from an element previously passed to showLoadingState.
 * @param {HTMLElement} el
 */
function hideLoadingState(el) {
    if (!el) return;
    el.classList.remove('btn-loading');
    el.removeAttribute('disabled');
    if (el.dataset.originalContent !== undefined) {
        el.innerHTML = el.dataset.originalContent;
        delete el.dataset.originalContent;
    }
}

/**
 * Create a progress bar element.
 * @param {HTMLElement} container
 * @param {{indeterminate?: boolean}} options
 */
function createProgressBar(container, options = {}) {
    const wrap = document.createElement('div');
    wrap.className = 'progress';
    const bar = document.createElement('div');
    bar.className = 'progress-bar';
    wrap.appendChild(bar);
    container.appendChild(wrap);
    if (options.indeterminate) {
        bar.style.width = '50%';
        bar.style.animation = 'shimmer 1.5s infinite';
    }
    return {
        update(pct) { bar.style.width = Math.max(0, Math.min(100, pct)) + '%'; },
        complete(cb) { bar.style.width = '100%'; setTimeout(() => { wrap.remove(); cb && cb(); }, 300); },
        element: wrap,
    };
}

/**
 * Show AI thinking indicator with rotating messages and loading dots.
 * @param {HTMLElement} container
 */
function showAIThinking(container) {
    if (!container) return { stop() {} };
    const box = document.createElement('div');
    box.className = 'ai-thinking-indicator';
    const msg = document.createElement('span');
    const dots = document.createElement('span');
    dots.className = 'loading-dots';
    dots.innerHTML = '<span></span><span></span><span></span>';
    box.appendChild(msg); box.appendChild(dots);
    container.appendChild(box);
    const messages = [
        'Analyzing your task…',
        'Generating suggestions…',
        'Almost there…'
    ];
    let i = 0;
    msg.textContent = messages[i];
    const id = setInterval(() => { i = (i + 1) % messages.length; msg.textContent = messages[i]; }, 2000);
    return { stop() { clearInterval(id); box.remove(); } };
}

/**
 * Success animation on an element.
 * @param {HTMLElement} el
 */
function showSuccessAnimation(el) {
    if (!el) return;
    el.classList.add('btn-success-anim', 'is-success');
    setTimeout(() => el.classList.remove('is-success', 'btn-success-anim'), 2000);
}

/**
 * Error animation on an element.
 * @param {HTMLElement} el
 */
function showErrorAnimation(el) {
    if (!el) return;
    el.classList.add('animate-shake', 'is-error');
    el.addEventListener('animationend', () => el.classList.remove('animate-shake', 'is-error'), { once: true });
}

// Enhanced autoresize with min/max heights and smooth transition
function autoResizeTextareaEnhanced(el, { minHeight = 48, maxHeight = 300 } = {}) {
    el.style.overflow = 'hidden';
    el.style.transition = 'height 120ms ease';
    el.style.minHeight = minHeight + 'px';
    el.style.maxHeight = maxHeight + 'px';
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, maxHeight) + 'px';
}

function addCharacterCounter(textarea, maxLength) {
    let counter = textarea.parentElement?.querySelector('[data-counter]') || textarea.parentElement?.querySelector('.char-counter');
    if (!counter) {
        counter = document.createElement('div');
        counter.className = 'char-counter';
        textarea.insertAdjacentElement('afterend', counter);
    }
    // Ensure styling classes are present when reusing existing counter
    if (counter && !counter.classList.contains('char-counter')) {
        counter.classList.add('char-counter');
    }
    const update = () => {
        const len = textarea.value.length;
        const max = maxLength || Number(textarea.getAttribute('maxlength')) || 0;
    counter.textContent = max ? `${len} / ${max} characters` : `${len} characters`;
        counter.classList.remove('warning', 'error');
        if (max) {
            if (len >= max) counter.classList.add('error');
            else if (len >= Math.floor(max * 0.9)) counter.classList.add('warning');
        }
    };
    textarea.addEventListener('input', update);
    update();
}

document.addEventListener('DOMContentLoaded', () => {
    // Enhanced autoresize only; initialize once per element
    document.querySelectorAll('.js-autoresize-textarea').forEach(el => {
        if (el.dataset.autoresizeEnhanced) return;
        el.dataset.autoresizeEnhanced = '1';
        el.addEventListener('input', () => autoResizeTextareaEnhanced(el));
        autoResizeTextareaEnhanced(el);
    });

    // Character counters
    document.querySelectorAll('textarea[data-max-length], textarea[maxlength]').forEach(el => {
        addCharacterCounter(el);
    });

    // console.debug('AI features initialized.');
});

// Expose functions to the global window object if needed, for example, to be called from Livewire hooks.
window.aiFeatures = {
    debounce,
    autoResizeTextarea,
    copyToClipboard,
    showLoadingState,
    hideLoadingState,
    createProgressBar,
    showAIThinking,
    showSuccessAnimation,
    showErrorAnimation,
    addCharacterCounter,
};

document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('.js-autoresize-textarea').forEach(el => {
        if (!el.dataset.autoresizeEnhanced) {
            el.dataset.autoresizeEnhanced = '1';
            el.addEventListener('input', () => autoResizeTextareaEnhanced(el));
        }
        autoResizeTextareaEnhanced(el);
    });
});