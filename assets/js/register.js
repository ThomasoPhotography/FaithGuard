async function openRegisterModal() {
	const container = document.getElementById('modal-container');

	if (!container) {
		console.error('modal-container missing');
		return;
	}

	try {
		const response = await fetch('/api/auth/register.php', {
			method: 'GET',
			credentials: 'same-origin',
		});

		const html = await response.text();

		if (!response.ok) {
			console.error(html);

			return;
		}

		container.innerHTML = html;

		const modal = document.getElementById('registerModal');

		new RegisterModal(modal);
	} catch (error) {
		console.error('Modal error:', error);
	}
}

class RegisterModal {
	constructor(modal) {
		this.modalEl = modal;

		this.form = modal.querySelector('#registerForm');

		this.message = this.form.querySelector('.c-form__message');

		this.form.addEventListener('submit', this.handleSubmit.bind(this));

		if (typeof bootstrap !== 'undefined') {
			bootstrap.Modal.getOrCreateInstance(modal).show();
		}
	}

	async handleSubmit(e) {
		e.preventDefault();

		console.log('REGISTER SUBMIT');

		const formData = new FormData(this.form);

		const data = Object.fromEntries(formData.entries());

		try {
			const response = await fetch('/api/auth/register.php', {
				method: 'POST',

				credentials: 'same-origin',

				headers: {
					'Content-Type': 'application/json',

					'X-CSRF-Token': data.csrf_token,
				},

				body: JSON.stringify(data),
			});

			const result = await response.json();

			if (result.success) {
				this.message.innerHTML = 'Account created';

				setTimeout(() => {
					window.location.href = '/dashboard.php';
				}, 1000);
			} else {
				this.message.innerHTML = result.error || 'Registration failed';
			}
		} catch (error) {
			console.error(error);

			this.message.innerHTML = 'Server connection failed';
		}
	}
}

window.openRegisterModal = openRegisterModal;
