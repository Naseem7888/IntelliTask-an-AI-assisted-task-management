import Sortable from 'sortablejs';

/**
 * Initialize Sortable.js on a container, integrated with Livewire.
 * @param {HTMLElement} container The list container element
 * @param {any} component Livewire component reference
 */
export function initializeSortable(container, component) {
    if (!container) return;

    // ARIA roles for accessibility
    container.setAttribute('role', 'listbox');
    Array.from(container.children).forEach(item => {
        item.setAttribute('role', 'option');
        item.setAttribute('tabindex', '0');
        item.setAttribute('aria-grabbed', 'false');
    });

    const sortable = new Sortable(container, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        handle: '.drag-handle',
        forceFallback: true,
        fallbackTolerance: 3,
        scroll: true,
        scrollSensitivity: 100,
        bubbleScroll: true,
        onStart(evt) {
            document.body.classList.add('is-dragging');
            const item = evt.item;
            item?.setAttribute('aria-grabbed', 'true');
        },
        async onEnd(evt) {
            document.body.classList.remove('is-dragging');
            const item = evt.item;
            item?.setAttribute('aria-grabbed', 'false');
            const parentEl = evt.to;

            const taskIds = Array.from(parentEl.children).map(child => child.getAttribute('wire:sortable.item'));

            // Disable sorting while saving
            sortable.option('disabled', true);
            container.classList.add('opacity-50');
            let revertNeeded = false;
            try {
                await component.call('updateTaskOrder', taskIds);
                window.toast?.success('Task order updated');
            } catch (e) {
                revertNeeded = true;
                window.toast?.error('Failed to update task order');
            } finally {
                sortable.option('disabled', false);
                container.classList.remove('opacity-50');
            }

            if (revertNeeded) {
                // Optional: re-render via Livewire refresh if needed
                try { window.Livewire?.dispatch('refreshTasks'); } catch {}
            }
        },
    });
}