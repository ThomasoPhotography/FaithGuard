// #region ***  DOM references                           ***********
const authBtn = document.querySelector('.js-log');
const emailInput = document.getElementById('signupUsername');
const passwordInput = document.getElementById('signupPassword');
const nameInput = document.getElementById('signupName');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showLoginSuccess = () => {
	console.log('Login successful! Welcome back.');
	location.reload(); // Reload to show logged-in nav
};

const showRegisterSuccess = () => {
	console.log('Registration successful! You are now logged in.');
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
	// Note: Role is set to 'user' by default server-side; only admins can change it
	const response = await fetch('/api/auth/register.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email, password }),
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
			const loginData = await attemptLogin(email, password);
			if (loginData.success) {
				showLoginSuccess();
				return;
			} else if (loginData.redirect) {
				// Redirect to register page
				window.location.href = loginData.redirect;
				return;
			} else if (loginData.error === 'Invalid credentials') {
				// Try register (fallback, though redirect should handle it)
				const registerData = await attemptRegister(email, password);
				if (registerData.success) {
					showRegisterSuccess();
				} else {
					showError('Registration failed: ' + (registerData.error || 'Unknown error'));
				}
			} else {
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
				console.error('Logged out successfully.');
				location.reload();
			} else {
				console.error('Logout failed.');
			}
		} catch (error) {
			console.error('Logout error:', error);
			console.error('An error occurred during logout.');
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
