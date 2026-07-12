/**
 * Handles fetching the modal HTML from the server and showing it
 */
async function openRegisterModal() {
	// Idempotent: if modal exists and is initialized, just show it
	try {
		const container = document.getElementById('modal-container');
		if (!container) {
			console.error('Modal container (#modal-container) not found.');
			return;
		}

		const existing = container.querySelector('#registerModal');
		if (existing && existing.dataset.fgInit === '1') {
			if (typeof bootstrap !== 'undefined') new bootstrap.Modal(existing).show();
			return;
		}

		const response = await fetch('/api/auth/register.php', { credentials: 'same-origin' });
		if (!response.ok) {
			console.error('Failed to load register modal:', response.status, response.statusText);
			return;
		}

		const html = await response.text();
		container.innerHTML = html;

		// Initialize RegisterModal for the injected element
		const modalEl = container.querySelector('#registerModal');
		if (modalEl) new RegisterModal(modalEl);
	} catch (err) {
		console.error('Error loading modal:', err);
	}
}

class RegisterModal {
	constructor(modalEl) {
		this.modalEl = modalEl || document.getElementById('registerModal');
		if (!this.modalEl) return;

		// Prevent double initialization
		if (this.modalEl.dataset.fgInit === '1') return;
		this.modalEl.dataset.fgInit = '1';

		this.form = this.modalEl.querySelector('#registerForm');
		this.submitBtn = this.form ? this.form.querySelector('button[type="submit"]') : null;

		this.messageContainer = this.form.querySelector('.c-form__message');
		if (!this.messageContainer) {
			this.messageContainer = document.createElement('div');
			this.messageContainer.className = 'c-form__message mb-3';
			this.form.prepend(this.messageContainer);
		}

		// Client-side constraints (keep in sync with server)
		this.MIN_PASSWORD = 8;
		this.MAX_PASSWORD = 72;

		this._boundSubmit = this.handleSubmit.bind(this);
		this.init();
	}

	init() {
		if (!this.form) return;
		// Remove any previous listener and add ours
		this.form.removeEventListener('submit', this._boundSubmit);
		this.form.addEventListener('submit', this._boundSubmit);

		// Show modal if bootstrap is available
		if (typeof bootstrap !== 'undefined') {
			try {
				this.bsModal = new bootstrap.Modal(this.modalEl);
				this.bsModal.show();
			} catch (e) {
				// ignore
			}
		}
	}

	validate(data) {
		const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!data.email || !emailRe.test(data.email)) {
			return 'Please enter a valid email address.';
		}
		if (!data.password) return 'Please enter a password.';
		if (data.password.length < this.MIN_PASSWORD) return `Password must be at least ${this.MIN_PASSWORD} characters.`;
		if (data.password.length > this.MAX_PASSWORD) return `Password must be no more than ${this.MAX_PASSWORD} characters.`;
		return null;
	}

	async handleSubmit(e) {
		e.preventDefault();
		this.clearMessage();

		const formData = new FormData(this.form);
		const data = Object.fromEntries(formData.entries());
		const csrfToken = this.form.querySelector('input[name="csrf_token"]')?.value || '';
		if (csrfToken) {
			data.csrf_token = csrfToken;
		}

		const clientErr = this.validate(data);
		if (clientErr) {
			this.showMessage(clientErr, 'error');
			return;
		}

		if (this.submitBtn) {
			this.submitBtn.disabled = true;
			this.prevBtnText = this.submitBtn.innerHTML;
			this.submitBtn.innerHTML = 'Creating...';
		}

		try {
			const response = await fetch('/api/auth/register.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-Token': data.csrf_token || '',
				},
				credentials: 'same-origin',
				body: JSON.stringify(data),
			});

			let result;
			try {
				result = await response.json();
			} catch (e) {
				throw new Error('Invalid server response');
			}

			if (response.ok && result.success) {
				this.showMessage('Account created — redirecting...', 'success');
				this.form.reset();
				setTimeout(() => {
					window.location.href = '/dashboard.php';
				}, 1200);
			} else {
				const errMsg = result && result.error ? result.error : result.message || 'Registration failed';
				this.showMessage(errMsg, 'error');
			}
		} catch (err) {
			console.error('Register error:', err);
			this.showMessage('Network error — please try again.', 'error');
		} finally {
			if (this.submitBtn) {
				this.submitBtn.disabled = false;
				this.submitBtn.innerHTML = this.prevBtnText || 'Create account';
			}
		}
	}

	showMessage(message, type) {
		this.messageContainer.textContent = message;
		this.messageContainer.className = `c-form__message mb-3 alert alert-${type === 'success' ? 'success' : 'danger'}`;
	}

	clearMessage() {
		this.messageContainer.textContent = '';
		this.messageContainer.className = 'c-form__message mb-3';
	}
}

// Expose globally for inline onclick handlers
window.openRegisterModal = openRegisterModal;
