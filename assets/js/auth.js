(function () {
	// #region *** DOM references                           ***********
	// Static forms are removed from global variables to support dynamic modal injection.
	// #endregion

	// #region *** Callback-Visualisation - show___         ***********
	const showAuthMessage = function (formElement, message, type = 'error') {
		if (!formElement) {
			window.alert(message);
			return;
		}

		const container = formElement.querySelector('.c-form__message');
		if (!container) {
			window.alert(message);
			return;
		}

		container.innerHTML = `<div class="alert alert--${type}">${message}</div>`;
	};
	// #endregion

	// #region *** Callback-No Visualisation - callback___  ***********
	const callbackLogin = function (data, formElement = null) {
		if (data.success) {
			window.location.href = '/dashboard.php';
		} else {
			const message = data.error || 'Login failed';
			showAuthMessage(formElement, message);
		}
	};

	const callbackRegister = function (data, formElement) {
		if (data.success) {
			window.location.href = '/dashboard.php';
		} else {
			showAuthMessage(formElement, data.error || 'Registration failed');
		}
	};

	const callbackLogout = function (data) {
		if (data.success) {
			window.location.href = '/';
		} else {
			console.log('Logout failed:', data.error);
		}
	};
	// #endregion

	// #region *** Data Access - get___                     ***********
	const postAuthData = async function (url, payload) {
		try {
			const response = await fetch(url, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				credentials: 'same-origin',
				body: JSON.stringify(payload),
			});

			return await response.json();
		} catch (error) {
			console.error('Auth request failed:', error);
			return { success: false, error: 'Network error' };
		}
	};
	// #endregion

	// #region *** Event Listeners - listenTo___            ***********
	const listenToLogin = function () {
		// Handle navbar dropdown login (no form wrapper)
		document.addEventListener('click', async function (e) {
			const loginBtn = e.target.closest('.js-log');
			if (loginBtn) {
				e.preventDefault();
				const emailInput = document.getElementById('signupUsername');
				const passwordInput = document.getElementById('signupPassword');

				// Grab any CSRF token present in the DOM
				const csrfInput = document.getElementById('loginCsrfToken');

				const email = emailInput ? emailInput.value.trim() : '';
				const password = passwordInput ? passwordInput.value : '';
				const csrf_token = csrfInput ? csrfInput.value.trim() : '';

				if (!csrf_token) {
					console.error('Login aborted: CSRF token is missing.');
					window.alert('Your security session could not be verified. Please refresh the page and try again.');
					return;
				}

				// Send the token alongside the credentials
				const data = await postAuthData('/api/auth/login.php', {
					email,
					password,
					csrf_token,
				});

				callbackLogin(data, null);
			}
		});

		// Handle dedicated login forms (standalone page or future modals)
		document.addEventListener('submit', async function (e) {
			if (e.target && e.target.classList.contains('c-form__login')) {
				e.preventDefault();
				const form = e.target;
				const formData = new FormData(form);
				const payload = Object.fromEntries(formData);

				const data = await postAuthData('/api/auth/login.php', payload);
				callbackLogin(data, form);
			}
		});
	};

	const listenToRegister = function () {
		document.addEventListener('submit', async function (e) {
			if (e.target && e.target.id === 'registerForm') {
				e.preventDefault();
				const form = e.target;
				const submitBtn = form.querySelector('button[type="submit"]');

				// 1. Disable the button to prevent double-clicks
				if (submitBtn) {
					submitBtn.disabled = true;
					submitBtn.textContent = 'Creating account...';
				}

				const formData = new FormData(form);
				const payload = Object.fromEntries(formData);

				const data = await postAuthData('/api/auth/register.php', payload);

				if (data.success) {
					window.location.href = '/dashboard.php';
				} else {
					showAuthMessage(form, data.error || 'Registration failed');

					// 2. Re-enable the button if registration fails
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.textContent = 'Create account';
					}
				}
			}
		});
	};

	const listenToLogout = function () {
		document.addEventListener('click', async function (e) {
			const logoutBtn = e.target.closest('.js-logout-btn, [data-action="logout"]');
			if (logoutBtn) {
				e.preventDefault();
				const data = await postAuthData('/api/auth/logout.php', {});
				callbackLogout(data);
			}
		});
	};

	// Expose logout globally for inline onclick handlers if needed
	window.logout = async function () {
		const data = await postAuthData('/api/auth/logout.php', {});
		callbackLogout(data);
	};
	// #endregion

	// #region *** Init / DOMContentLoaded                  ***********
	const init = function () {
		listenToLogin();
		listenToRegister();
		listenToLogout();
	};

	document.addEventListener('DOMContentLoaded', init);
	// #endregion
})();
