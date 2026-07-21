/**
 * Register Modal Handler
 * Loads registration form and handles account creation
 */
async function openRegisterModal() {
	try {
		const container = document.getElementById('modal-container');
		if (!container) {
			console.error('Modal container not found.');
			return;
		}
		const existingModal = container.querySelector('#registerModal');
		if (existingModal && existingModal.dataset.fgInit === '1') {
			if (typeof bootstrap !== 'undefined') {
				const modal = bootstrap.Modal.getOrCreateInstance(existingModal);
				modal.show();
			}
			return;
		}
		const response = await fetch('/api/auth/register.php', {
			method: 'GET',
			credentials: 'same-origin',
		});
		const html = await response.text();
		if (!response.ok) {
			console.error('Register modal error:', html);
			alert('Could not load registration form.');
			return;
		}
		container.innerHTML = html;
		const modalElement = container.querySelector('#registerModal');
		if (!modalElement) {
			console.error('Register modal HTML missing.');
			return;
		}
		new RegisterModal(modalElement);
	} catch (error) {
		console.error('Modal loading failed:', error);
		alert('Could not open registration form.');
	}
}

class RegisterModal {
	constructor(modalElement) {
		this.modalEl = modalElement;
		if (!this.modalEl) {
			return;
		}
		if (this.modalEl.dataset.fgInit === '1') {
			return;
		}
		this.modalEl.dataset.fgInit = '1';
		this.form = this.modalEl.querySelector('#registerForm');
		if (!this.form) {
			console.error('Register form not found.');
			return;
		}
		this.submitBtn = this.form.querySelector('button[type="submit"]');
		this.messageContainer = this.form.querySelector('.c-form__message');
		if (!this.messageContainer) {
			this.messageContainer = document.createElement('div');
			this.messageContainer.className = 'c-form__message mb-3';
			this.form.prepend(this.messageContainer);
		}
		this.MIN_PASSWORD = 8;
		this.MAX_PASSWORD = 72;
		this.form.addEventListener('submit', this.handleSubmit.bind(this));
		this.show();
	}

	show() {
		if (typeof bootstrap !== 'undefined') {
			const modal = bootstrap.Modal.getOrCreateInstance(this.modalEl);
			modal.show();
		}
	}

	validate(data) {
		if (!data.first_name || data.first_name.trim().length < 2 || data.first_name.trim().length > 50) {
			return 'First name must be between 2 and 50 characters.';
		}
		if (!data.last_name || data.last_name.trim().length < 2 || data.last_name.trim().length > 50) {
			return 'Last name must be between 2 and 50 characters.';
		}
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!data.email || !emailRegex.test(data.email)) {
			return 'Please enter a valid email address.';
		}
		if (!data.password) {
			return 'Please enter a password.';
		}
		if (data.password.length < this.MIN_PASSWORD) {
			return `Password must be at least ${this.MIN_PASSWORD} characters.`;
		}
		if (data.password.length > this.MAX_PASSWORD) {
			return `Password must be no more than ${this.MAX_PASSWORD} characters.`;
		}
		return null;
	}

	async handleSubmit(event) {
		event.preventDefault();
		this.clearMessage();
		const formData = new FormData(this.form);
		const data = Object.fromEntries(formData.entries());
		const validationError = this.validate(data);
		if (validationError) {
			this.showMessage(validationError, 'error');
			return;
		}
		if (this.submitBtn) {
			this.submitBtn.disabled = true;
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
			} catch {
				const text = await response.text();
				throw new Error(text || 'Invalid server response');
			}
			if (response.ok && result.success) {
				this.showMessage('Account created. Redirecting...', 'success');
				this.form.reset();
				setTimeout(() => {
					window.location.href = '/dashboard.php';
				}, 1200);
			} else {
				this.showMessage(result.error || 'Registration failed.', 'error');
			}
		} catch (error) {
			console.error('Registration error:', error);
			this.showMessage(error.message || 'Network error. Please try again.', 'error');
		} finally {
			if (this.submitBtn) {
				this.submitBtn.disabled = false;
				this.submitBtn.innerHTML = 'Create account';
			}
		}
	}

	showMessage(message, type) {
		this.messageContainer.textContent = message;
		this.messageContainer.className = `c-form__message mb-3 alert ${type === 'success' ? 'alert-success' : 'alert-danger'}`;
	}

	clearMessage() {
		this.messageContainer.textContent = '';
		this.messageContainer.className = 'c-form__message mb-3';
	}
}
window.openRegisterModal = openRegisterModal;
