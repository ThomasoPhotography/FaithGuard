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
			const modalInstance = new bootstrap.Modal(modalEl);
			modalInstance.show();

			// Clean up the DOM when closed so stale forms don't linger
			modalEl.addEventListener('hidden.bs.modal', () => {
				container.innerHTML = '';
			});
		}
	} catch (error) {
		console.error('Error opening register modal:', error);
	}
}

window.openRegisterModal = openRegisterModal;