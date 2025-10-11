import './bootstrap';
import Alpine from 'alpinejs';
import toast from './toast-manager';
import formValidator from './form-validator';
import keyboard from './keyboard-shortcuts';
import themeManager from './theme-manager';
import './ai-features';

// Expose toast globally for Livewire and inline scripts
window.toast = toast;

// Initialize theme early to reduce FOUC between dark/light
try { themeManager?.init?.(); } catch {}

// Smooth scrolling (respect reduced motion)
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (!prefersReduced) {
	document.documentElement.style.scrollBehavior = 'smooth';
	document.addEventListener('click', (e) => {
		const a = e.target.closest('a[href^="#"]');
		if (!a) return;
		const id = a.getAttribute('href').slice(1);
		const target = document.getElementById(id);
		if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
	});
}

// Page transition overlay for Livewire navigation: reuse static #global-loading-overlay
const loadingOverlay = document.getElementById('global-loading-overlay');
function showOverlay() {
	if (!loadingOverlay) return;
	loadingOverlay.classList.remove('hidden');
	loadingOverlay.classList.add('flex');
}
function hideOverlay() {
	if (!loadingOverlay) return;
	loadingOverlay.classList.remove('flex');
	loadingOverlay.classList.add('hidden');
}

// Use correct Livewire navigation events
document.addEventListener('livewire:navigating', showOverlay);
document.addEventListener('livewire:navigated', () => { hideOverlay(); initDynamic(); });

// Livewire toasts
document.addEventListener('toast:success', (e) => toast.success(e.detail?.message || 'Success'));
document.addEventListener('toast:error', (e) => toast.error(e.detail?.message || 'Error'));
document.addEventListener('toast:warning', (e) => toast.warning(e.detail?.message || 'Warning'));
document.addEventListener('toast:info', (e) => toast.info(e.detail?.message || 'Info'));
document.addEventListener('tasks-created', () => toast.success('Tasks generated successfully'));

// Global loading progress bar for Livewire
let topProgress;
function ensureTopProgress() {
	if (topProgress) return topProgress;
	topProgress = document.createElement('div');
	topProgress.className = 'progress';
	topProgress.style.position = 'fixed';
	topProgress.style.left = '0';
	topProgress.style.top = '0';
	topProgress.style.width = '100%';
	topProgress.style.zIndex = '100';
	const bar = document.createElement('div');
	bar.className = 'progress-bar';
	bar.style.width = '0%';
	topProgress.appendChild(bar);
	document.body.appendChild(topProgress);
	return topProgress;
}

// Replace non-standard events with Livewire hooks or DOM events
if (window.Livewire?.hook) {
	window.Livewire.hook('message.sent', () => {
		const wrap = ensureTopProgress();
		const bar = wrap.querySelector('.progress-bar');
		bar.style.width = '30%';
	});
	window.Livewire.hook('message.processed', () => {
		const wrap = ensureTopProgress();
		const bar = wrap.querySelector('.progress-bar');
		bar.style.width = '100%';
		setTimeout(() => { wrap.remove(); topProgress = null; }, 300);
	});
} else {
	// Fallback for versions emitting DOM events
	document.addEventListener('livewire:request-sent', () => {
		const wrap = ensureTopProgress();
		const bar = wrap.querySelector('.progress-bar');
		bar.style.width = '30%';
	});
	document.addEventListener('livewire:request-finished', () => {
		const wrap = ensureTopProgress();
		const bar = wrap.querySelector('.progress-bar');
		bar.style.width = '100%';
		setTimeout(() => { wrap.remove(); topProgress = null; }, 300);
	});
}

let io; // hoisted IntersectionObserver

function initDynamic() {
	// Form validator init
	try { formValidator.init(); } catch {}

	// Scroll reveal
	try {
		const els = document.querySelectorAll('.scroll-reveal');
		if (io) io.disconnect();
		io = new IntersectionObserver((entries) => {
			entries.forEach((entry) => { if (entry.isIntersecting) entry.target.classList.add('in'); });
		}, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });
		els.forEach(el => io.observe(el));
	} catch {}

	// Keyboard shortcuts
	// Initialized once on DOMContentLoaded; do not re-initialize here
}

document.addEventListener('DOMContentLoaded', () => {
	try { keyboard.init(); } catch {}
	initDynamic();
});

// Global error handler
window.addEventListener('error', (e) => { try { toast.error('An unexpected error occurred'); } catch {} console.error(e.error || e.message || e); });
window.addEventListener('unhandledrejection', (e) => { try { toast.error('A request failed'); } catch {} console.error(e.reason || e); });

// Alpine startup
window.Alpine = Alpine;
Alpine.start();