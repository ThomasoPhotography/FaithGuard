/**
 * Handles fetching the modal HTML from the server and showing it
 */
async function openRegisterModal() {
	try {
		const response = await fetch('/api/auth/register.php'); // Fetches the GET portion of your PHP
		const html = await response.text();

		const container = document.getElementById('modal-container');
		container.innerHTML = html;

		// The PHP script you wrote already contains the <script> to auto-show the modal,
		// but we need to initialize the class listeners:
		new RegisterModal();
	} catch (error) {
		console.error('Error loading modal:', error);
	}
}

class RegisterModal {
	constructor() {
		this.modalEl = document.getElementById('registerModal');
		this.form = document.getElementById('registerForm');
		// Your PHP didn't have a div for messages, let's look for or create one
		this.messageContainer = this.form.querySelector('.c-form__message');

		if (!this.messageContainer) {
			this.messageContainer = document.createElement('div');
			this.messageContainer.className = 'c-form__message mb-3';
			this.form.prepend(this.messageContainer);
		}

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
			// Updated path to match your folder structure
			const response = await fetch('/api/auth/register.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(data),
			});

			const result = await response.json();

			if (result.success) {
				this.showMessage('Account created! Redirecting...', 'success');
				this.form.reset();
				setTimeout(() => {
					window.location.href = '/dashboard.php';
				}, 1500);
			} else {
				this.showMessage(result.error || 'Registration failed', 'error');
			}
		} catch (error) {
			this.showMessage('An error occurred. Please try again.', 'error');
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
