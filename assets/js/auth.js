// #region ***  DOM references                           ***********
const authBtn = document.querySelector('.js-log');
const createBtn = document.querySelector('.js-create');
const emailInput = document.getElementById('signupUsername');
const passwordInput = document.getElementById('signupPassword');
const nameInput = document.getElementById('signupName');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showLoginSuccess = () => {
	console.log('Login successful! Welcome back.');
	location.reload(); // Reload to show logged-in nav
};

const showRegisterSuccess = () => {
	console.log('Registration successful! You are now logged in.');
	location.reload(); // Reload to update UI
};

const showError = (message) => {
	alert(message); // Could upgrade to a modal for better UX
};

const injectRegisterModal = (prefillEmail = '', prefillPassword = '') => {
    if (document.getElementById('registerModal')) return;

    const modalHTML = `
    <!-- Register Modal -->
    <div class="modal fade c-modal" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content c-modal__content">
                <div class="modal-header c-modal__header">
                    <h5 class="modal-title c-modal__title" id="registerModalLabel">Join FaithGuard</h5>
                    <button type="button" class="btn-close c-modal__btn--close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body c-modal__body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label c-modal__label">Email address</label>
                            <input type="email" class="form-control c-dropdown__info" id="registerEmail" name="email" value="${prefillEmail}" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerName" class="form-label c-modal__label">Full Name (Optional)</label>
                            <input type="text" class="form-control c-dropdown__info" id="registerName" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label c-modal__label">Password</label>
                            <input type="password" class="form-control c-dropdown__info" id="registerPassword" name="password" value="${prefillPassword}" required minlength="8">
                            <div class="form-text text-muted">Must be at least 8 characters long.</div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn c-modal__btn--submit">Create Account</button>
                        </div>
                    </form>
                    <div id="registerMessage" class="mt-3 text-center"></div>
                </div>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Attach event listener to the new form immediately after injection
    const registerForm = document.getElementById('registerForm');
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const name = document.getElementById('registerName').value || 'New User';
        
        const messageDiv = document.getElementById('registerMessage');
        messageDiv.innerHTML = '<span class="text-info">Creating account...</span>';

        try {
            const data = await attemptRegister(email, password, name);
            if (data.success) {
                messageDiv.innerHTML = '<span class="text-success">Account created! Redirecting...</span>';
                setTimeout(() => location.reload(), 1500);
            } else {
                messageDiv.innerHTML = `<span class="text-danger">${data.error}</span>`;
            }
        } catch (err) {
            console.error(err);
            messageDiv.innerHTML = '<span class="text-danger">An error occurred.</span>';
        }
    });
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
// #endregion

// #region ***  Data Access - get___                     ***********
const attemptLogin = async (email, password) => {
	const response = await fetch('/api/auth/login.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email, password }),
	});
	return await response.json();
};

const attemptRegister = async (email, password, name) => {
	const payload = { email, password };
	if (name) payload.name = name; // Include name if provided
	const response = await fetch('/api/auth/register.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ 
            email: email, 
            password: password, 
            name: name 
        }),
	});
	return await response.json();
};

const performLogout = async () => {
	const response = await fetch('/api/auth/logout.php', { method: 'POST' });
	return await response.json();
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToAuth = () => {
    // 1. Handle Login Button Click
    if (authBtn) {
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
                } 
                
                // Show errors directly (no auto-register loop)
                if (loginData.error === 'Invalid credentials') {
                    showError('Login failed: Invalid credentials');
                } 
                else if (loginData.error === 'User not found') {
                    showError('User not found. Please click "Create Account" to register.');
                } 
                else {
                    showError('Login failed: ' + loginData.error);
                }
            } catch (error) {
                console.error('Auth error:', error);
                showError('An error occurred. Please try again.');
            }
        });
    }

    // 2. Handle "Create Account" Click (Trigger Modal)
    // Note: createBtn corresponds to .js-create class
    if (createBtn) {
        createBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Stop navigation to register.php if wrapped in <a>
            e.stopPropagation(); // Prevent dropdown from closing immediately if needed

            // Inject and Show Modal
            injectRegisterModal();
            const modalElement = document.getElementById('registerModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
    }
};

const listenToLogout = () => {
	window.logout = async () => {
		try {
			const data = await performLogout();
			if (data.success) {
				console.log('Logged out successfully.');
				location.reload();
			} else {
				console.error('Logout failed.');
			}
		} catch (error) {
			console.error('Logout error:', error);
			showError('An error occurred during logout.');
		}
	};
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const init = function () {
	console.log('Page loaded with Auth');
	listenToAuth();
	listenToLogout();
};

document.addEventListener('DOMContentLoaded', init);
// #endregion
