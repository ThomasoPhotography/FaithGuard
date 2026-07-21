async function openRegisterModal() {
	const container = document.getElementById('modal-container');

	if (!container) {
		console.error('modal-container missing');
		return;
	}

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
}

class RegisterModal {
	constructor(modal) {
		this.modal = modal;

		this.form = modal.querySelector('#registerForm');

		this.message = modal.querySelector('.c-form__message');

		this.form.addEventListener('submit', this.submit.bind(this));

		bootstrap.Modal.getOrCreateInstance(modal).show();
	}

	async submit(e) {
		e.preventDefault();

		const data = Object.fromEntries(new FormData(this.form));

		try {
			const response = await fetch('/api/auth/register.php', {
				method: 'POST',

				headers: {
					'Content-Type': 'application/json',
				},

				credentials: 'same-origin',

				body: JSON.stringify(data),
			});

			const result = await response.json();

			if (result.success) {
				this.message.className = 'alert alert-success';

				this.message.textContent = 'Account created';

				setTimeout(() => {
					window.location.href = '/dashboard.php';
				}, 1000);
			} else {
				throw new Error(result.error || 'Registration failed');
			}
		} catch (error) {
			this.message.className = 'alert alert-danger';

			this.message.textContent = error.message;
		}
	}
}

window.openRegisterModal = openRegisterModal;
