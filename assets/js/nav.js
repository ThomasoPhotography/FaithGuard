// #region ***  DOM references                           ***********
const navPlaceholder = document.querySelector('.c-nav--placeholder');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showNav = () => {
    if (!navPlaceholder) {
        console.error('c-nav--placeholder element not found in DOM');
        return;
    }
    // CRITICAL FIX: Fetch the dynamic PHP file
    fetch('/templates/nav.php') 
    .then((response) => {
        if (!response.ok) {
            throw new Error('Failed to load nav.php: ' + response.status);
        }
        return response.text();
    })
    .then((data) => {
        navPlaceholder.innerHTML = data;
        // After loading nav, attach auth listeners (logout logic will be attached here)
        listenToAuth();
    })
    .catch((error) => {
        console.error('Error loading nav:', error);
    });
};
// ... (rest of the file remains the same)
// ...
const listenToAuth = () => {
    const loginBtn = document.querySelector('.js-log');
    const logoutBtn = document.querySelector('.js-logout-btn'); // New logout button selector
    
    // Attach Login logic
    if (loginBtn) {
        loginBtn.addEventListener('click', async () => {
            const email = document.getElementById('signupUsername').value.trim();
            const password = document.getElementById('signupPassword').value.trim();
            if (!email || !password) {
                alert('Please enter both email and password.');
                return;
            }
            login(email, password);
        });
    }

    // Attach Logout logic
    if (logoutBtn) {
        logoutBtn.addEventListener('click', logout);
    }
};

// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const init = function () {
    console.log('Page Loaded');
    showNav();
    listenToNavToggles();
};

document.addEventListener('DOMContentLoaded', init);
// #endregion