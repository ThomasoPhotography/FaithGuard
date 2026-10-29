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

		if (!response.ok) {
			console.error('Failed to load modal HTML');
			return;
		}

		const html = await response.text();
		container.innerHTML = html;

		const modalEl = document.getElementById('registerModal');

		if (modalEl && typeof bootstrap !== 'undefined') {
			// Initialize Bootstrap modal manually AFTER it exists in the DOM
			const modalInstance = new bootstrap.Modal(modalEl);
			modalInstance.show();

			// Clean up the DOM when closed
			modalEl.addEventListener('hidden.bs.modal', () => {
				container.innerHTML = '';
			});
		}
	} catch (error) {
		console.error('Error opening register modal:', error);
	}
}

window.openRegisterModal = openRegisterModal;
