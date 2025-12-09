// #region ***  DOM references                           ***********
const authBtn = document.querySelector('.js-log');
const emailInput = document.getElementById('signupUsername');
const passwordInput = document.getElementById('signupPassword');
const nameInput = document.getElementById('signupName');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showLoginSuccess = () => {
	alert('Login successful! Welcome back.');
	location.reload(); // Reload to show logged-in nav
};

const showRegisterSuccess = () => {
	alert('Registration successful! You are now logged in.');
	location.reload();
};

const showError = (message) => {
	alert(message);
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
// #endregion

// #region ***  Data Access - get___                     ***********
const attemptLogin = async (email, password) => {
	const response = await fetch('/api/auth/login.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email, password }),
	});
	return await response.json();
};

const attemptRegister = async (email, password, name) => {
	const response = await fetch('/api/auth/register.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email, password, name }),
	});
	return await response.json();
};

const performLogout = async () => {
	const response = await fetch('/api/auth/logout.php', { method: 'POST' });
	return await response.json();
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToAuth = () => {
	if (!authBtn) return; // Skip if logged in (button won't exist)

	authBtn.addEventListener('click', async () => {
		const email = emailInput.value.trim();
		const password = passwordInput.value.trim();

		if (!email || !password) {
			showError('Please enter both email and password.');
			return;
		}

		try {
			// 1. Try to Login first
			const loginData = await attemptLogin(email, password);

			if (loginData.success) {
				showLoginSuccess();
				return;
			} else if (loginData.error === 'Invalid credentials') {
				// If invalid credentials, it means user exists but wrong password.
				// Do NOT register.
				showError('Login failed: Invalid credentials');
			} else if (loginData.error === 'User not found') {
				// 2. If user does not exist, attempt Register automatically
				// We don't have a name field in the dropdown, so we let the backend default it or send a placeholder
				const registerData = await attemptRegister(email, password);

				if (registerData.success) {
					showRegisterSuccess();
				} else {
					showError('Registration failed: ' + (registerData.error || 'Unknown error'));
				}
			} else {
				// Other errors (database, server, etc.)
				showError('Login failed: ' + loginData.error);
			}
		} catch (error) {
			console.error('Auth error:', error);
			showError('An error occurred. Please try again.');
		}
	});
};

const listenToLogout = () => {
	window.logout = async () => {
		try {
			const data = await performLogout();
			if (data.success) {
				alert('Logged out successfully.');
				location.reload();
			} else {
				alert('Logout failed.');
			}
		} catch (error) {
			console.error('Logout error:', error);
			alert('An error occurred during logout.');
		}
	};
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const init = function () {
	console.log('Page loaded with Auth');
	listenToAuth();
	listenToLogout();
};

document.addEventListener('DOMContentLoaded', init);
// #endregion
