/**
 * Register Modal
 * Handles the registration form modal functionality
 */

class RegisterModal {
	constructor() {
		this.modal = document.getElementById('registerModal');
		this.form = document.querySelector('.c-form__register');
		this.messageContainer = document.querySelector('.c-form__message');
		this.init();
	}

	init() {
		if (this.form) {
			this.form.addEventListener('submit', (e) => this.handleSubmit(e));
		}
	}

	async handleSubmit(e) {
		e.preventDefault();
		this.clearMessage();

		const formData = new FormData(this.form);
		const data = Object.fromEntries(formData);

		try {
			const response = await fetch('/api/register', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(data),
			});

			const result = await response.json();

			if (response.ok) {
				this.showMessage('Account created successfully!', 'success');
				this.form.reset();
				setTimeout(() => {
					const bootstrapModal = bootstrap.Modal.getInstance(this.modal);
					bootstrapModal?.hide();
				}, 1500);
			} else {
				this.showMessage(result.message || 'Registration failed', 'error');
			}
		} catch (error) {
			this.showMessage('An error occurred. Please try again.', 'error');
			console.error('Registration error:', error);
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

document.addEventListener('DOMContentLoaded', () => {
	new RegisterModal();
});
