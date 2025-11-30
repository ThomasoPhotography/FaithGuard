// #region ***  DOM references                           ***********
const navPlaceholder = document.querySelector('.c-nav--placeholder');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showNav = () => {
    if (!navPlaceholder) {
        console.error('c-nav--placeholder element not found in DOM');
        return;
    }
    fetch('/templates/nav.html') // Note: Use relative path if running from root; adjust if needed (e.g., '/templates/nav.html' for absolute)
    .then((response) => {
            if (!response.ok) {
                throw new Error('Failed to load nav.html: ' + response.status);
            }
            return response.text();
    })
    .then((data) => {
            navPlaceholder.innerHTML = data;
            // After loading nav, attach auth listeners
            listenToAuth();
    })
    .catch((error) => {
            console.error('Error loading nav:', error);
    });
};

const logout = async () => {
    try {
        const response = await fetch('/api/auth/logout.php', { method: 'POST' });
        const data = await response.json();
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
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
// #endregion

// #region ***  Data Access - get___                     ***********
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToNavToggles = () => {
    // Custom toggles if needed
};

const listenToAuth = () => {
    const authBtn = document.querySelector('.js-log');
    if (!authBtn) return;

    authBtn.addEventListener('click', async () => {
        const email = document.getElementById('signupUsername').value.trim();
        const password = document.getElementById('signupPassword').value.trim();

        if (!email || !password) {
            alert('Please enter both email and password.');
            return;
        }

        // First, try login
        try {
            const loginResponse = await fetch('/api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const loginData = await loginResponse.json();

            if (loginData.success) {
                alert('Login successful! Welcome back.');
                location.reload(); // Reload to reflect logged-in state (e.g., update nav)
                return;
            } else if (loginData.error === 'Invalid credentials') {
                // Login failed, try register
                const registerResponse = await fetch('/api/auth/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const registerData = await registerResponse.json();

                if (registerData.success) {
                    alert('Registration successful! You are now logged in.');
                    location.reload(); // Reload to reflect logged-in state
                } else {
                    alert('Registration failed: ' + (registerData.error || 'Unknown error'));
                }
            } else {
                alert('Login failed: ' + loginData.error);
            }
        } catch (error) {
            console.error('Auth error:', error);
            alert('An error occurred. Please try again.');
        }
    });
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const init = function () {
    console.log('Page Loaded');
    showNav();
    listenToNavToggles();
};

document.addEventListener('DOMContentLoaded', init);
// #endregion