// Axios setup for Laravel CSRF-protected requests
import axios from 'axios';

// Make axios globally available
window.axios = axios;

// Set default headers
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set CSRF token from meta tag (Laravel default)
const token = document.querySelector('meta[name="csrf-token"]')?.content;
if (token) {
	window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
} else {
	console.warn('CSRF token not found: ensure a <meta name="csrf-token" content="..."> tag is present in your layout.');
}

// Optional: add interceptors for global error handling
window.axios.interceptors.response.use(
	(response) => response,
	(error) => {
		// You can surface errors to the user via a global toast if available
		try { window.toast?.error(error?.response?.data?.message || 'An error occurred'); } catch {}
		return Promise.reject(error);
	}
);

// This bootstrap file configures axios and CSRF for Laravel.
