// #region ***  DOM references                           ***********
const loginForm = document.querySelector('.c-form__login');
const registerForm = document.querySelector('.c-form__register');
const logoutButtons = document.querySelectorAll('.js-logout-btn, [data-action="logout"]');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showAuthMessage = function (form, message, type = 'error') {
	const container = form?.querySelector('.c-form__message');
	if (!container) return;

	container.innerHTML = `<div class="alert alert--${type}">${message}</div>`;
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackLogin = function (data) {
	if (data.success) {
		window.location.href = '/dashboard.php';
	} else {
		const message = data.error || 'Login failed';
		if (loginForm) {
			showAuthMessage(loginForm, message);
		} else {
			window.alert(message);
		}
	}
};

const callbackRegister = function (data) {
	if (data.success) {
		window.location.href = '/dashboard.php';
	} else {
		showAuthMessage(registerForm, data.error || 'Registration failed');
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

// #region ***  Data Access - get___                     ***********
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

// #region ***  Event Listeners - listenTo___            ***********
const listenToLogin = function () {
	if (!loginForm) {
		const loginButton = document.querySelector('.js-log');
		if (!loginButton) return;

		loginButton.addEventListener('click', async function () {
			const email = document.getElementById('signupUsername')?.value.trim();
			const password = document.getElementById('signupPassword')?.value;
			const data = await postAuthData('/api/auth/login.php', { email, password });
			callbackLogin(data);
		});
		return;
	}

	loginForm.addEventListener('submit', async function (e) {
		e.preventDefault();

		const formData = new FormData(loginForm);
		const payload = Object.fromEntries(formData);

		const data = await postAuthData('/api/auth/login.php', payload);
		callbackLogin(data);
	});
};

const listenToRegister = function () {
	if (!registerForm) return;

	registerForm.addEventListener('submit', async function (e) {
		e.preventDefault();

		const formData = new FormData(registerForm);
		const payload = Object.fromEntries(formData);

		const data = await postAuthData('/api/auth/register.php', payload);
		callbackRegister(data);
	});
};

const listenToLogout = function () {
	logoutButtons.forEach((logoutButton) => logoutButton.addEventListener('click', async function (event) {
		event.preventDefault();
		const data = await postAuthData('/api/auth/logout.php', {});
		callbackLogout(data);
	}));
};

window.logout = async function () {
	const data = await postAuthData('/api/auth/logout.php', {});
	callbackLogout(data);
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const init = function () {
	listenToLogin();
	listenToRegister();
	listenToLogout();
};

document.addEventListener('DOMContentLoaded', init);
// #endregion
