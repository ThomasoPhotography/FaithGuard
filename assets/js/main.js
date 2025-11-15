/**
 * FaithGuard Main JS
 * Version: 0.0.4
 * Author: Thomas Deseure
 * Release: 2025-11-15
 */

// #region ***  DOM references                           ***********
let quizButtons;
let quizModal;
let submitQuizBtn;
let quizForm;
let loginBtn;
let signupUsernameInput;
let signupPasswordInput;
let dropdownBtn;
let dropdownMenu;
let searchForm;
let navbar;
let navLinks;
let cards;
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showQuizModal = () => {
	quizModal.show();
};

const showLoginState = (username) => {
	if (dropdownBtn && dropdownMenu) {
		dropdownBtn.innerHTML = `<i class="bi bi-person-circle"></i> ${username}`;

		dropdownMenu.innerHTML = `
            <li><a class="dropdown-item c-dropdown__item" href="dashboard.html">Profile</a></li>
            <li><a class="dropdown-item c-dropdown__item" href="dashboard.html#settings">Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item c-dropdown__item" href="#" id="logoutBtn">Logout</a></li>
        `;

		// Add event listener for logout
		listentoLogout();
	}
};

const showSuccessMessage = (message) => {
	alert(message);
};

const showErrorMessage = (message) => {
	alert(`Error: ${message}`);
};

// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackQuizSubmit = () => {
	if (quizForm.checkValidity()) {
		// Collect form data
		const formData = new FormData(quizForm);
		const quizData = {
			duration: formData.get('duration'),
			accountability: formData.get('accountability'),
			resouces: formData.getAll('resources'),
			spiritual: formData.get('spiritual'),
			goal: formData.get('goal'),
			timestamp: new Date().toISOString(),
		};

		// Store in localStorage
		localStorage.setItem('faithGuard_quiz_' + Date.now(), JSON.stringify(quizData));
		showSuccessMessage('Thank you for completing the quiz! Based on your answers, we recommend exploring ouy resources page for personalized guidance.');

		// Reset form and hide modal
		quizForm.reset();
		quizModal.hide();

		// Optionally, redirect to resources page
		setTimeout(() => {
			window.location.href = 'resources.html';
		}, 1500);
	} else {
		quizForm.reportValidity();
	}
};

const callbackLogin = (e) => {
	e.preventDefault();
	const username = signupUsernameInput.value.trim();
	const password = signupPasswordInput.value.trim();

	if (!username || !password) {
		showErrorMessage('Please enter both username and password.');
		return;
	}
	// Check localStorage for existing user
	const existingUser = localStorage.getItem('faithGuard_user_' + username);
	if (existingUser) {
		const userData = JSON.parse(existingUser);
		if (userData.password === password) {
			sessionStorage.setItem('faithGuard_loggedInUser', username);
			showSuccessMessage(`Welcome back, ${username}!`);
			showLoginState(username);
			// Clear inputs
			signupUsernameInput.value = '';
			signupPasswordInput.value = '';
		} else {
			showErrorMessage('Incorrect password. Please try again.');
		}
	} else {
		// Register new user
		const newUser = {
			username: username,
			password: password,
			created: new Date().toISOString(),
		};
		localStorage.setItem('faithGuard_user_' + username, JSON.stringify(newUser));
		sessionStorage.setItem('faithGuard_loggedInUser', username);
		showSuccessMessage(`Account created! Welcome, ${username}!`);
		showLoginState(username);
		// Clear inputs
		signupUsernameInput.value = '';
		signupPasswordInput.value = '';
	}
};

const callbackLogout = () => {
	sessionStorage.removeItem('faithGuard_loggedInUser');
	location.reload();
};

const callbackSearch = (e) => {
	e.preventDefault();

	const searchInput = searchForm.querySelector('input[name="search"]');
	const searchTerm = searchInput.value.trim();

	if (searchTerm) {
		// Simple client-side search simulation
		sessionStorage.setItem('faithGuard_Search', searchTerm);
		window.location.href = 'resources.html';
	} else {
		showErrorMessage('Please enter a search term.');
	}
};

const callbackSmoothScroll = (e) => {
	const href = e.currentTarget.getAttribute('href');
	if (href !== '#' && href !== '') {
		e.preventDefault();
		const target = document.querySelector(href);
		if (target) {
			target.scrollIntoView({
				behavior: 'smooth',
				block: 'start',
			});
		}
	}
};

const callbackFormValidation = (event) => {
	if (!event.target.checkValidity()) {
		event.preventDefault();
		event.stopPropagation();
	}
	event.target.classList.add('was-validated');
};

const callbackNavbarScroll = () => {
	const currentScroll = window.pageYOffset;
	if (currentScroll > 100) {
		navbar.classList.add('scrolled');
	} else {
		navbar.classList.remove('scrolled');
	}
};

const callbackCardHoverEnter = (e) => {
	e.currentTarget.style.transform = 'translateY(-0.3rem)';
	e.currentTarget.style.transition = 'transform 0.3s ease';
};

const callbackCardHoverLeave = (e) => {
	e.currentTarget.style.transform = 'translateY(0)';
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getCurrentUser = () => {
	return sessionStorage.getItem('faithGuard_current_user');
};

const getCurrentPage = () => {
	return window.location.pathname.split('/').pop();
};

const createQuizModal = () => {
	const modalHTML = `
    <div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizModalLabel">FaithGuard Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">This confidential quiz will help us provide personalized resources for your journey. All responses are private.</p>
                    
                    <form id="quizForm">
                        <!-- Question 1 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">1. How long have you been struggling with this challenge?</label>
                            <select class="form-select" name="duration" required>
                                <option value="" disabled selected>Select an option....</option>
                                <option value="less_than_6_months">Less than 6 months</option>
                                <option value="6_months_to_1_year">6 months to 1 year</option>
                                <option value="1_to_3_years">1 to 3 years</option>
                                <option value="more_than_3_years">More than 3 years</option>
                            </select>
                        </div>

                        <!-- Question 2 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">2. Do you currently have an accountability partner?</label>
                            <div class="form-check" name="accountability" required>
                                <input class="form-check-input" type="radio" name="accountability" id="acc-yes" value="yes" required>
                                <label class="form-check-label" for="acc-yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="accountability" id="acc-no" value="no" required>
                                <label class="form-check-label" for="acc-no">No</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="accountability" id="acc-looking" value="looking" required>
                                <label class="form-check-label" for="acc-looking">Looking for one</label>
                            </div>
                        </div>

                        <!-- Question 3 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">3. What type of resources are you most interested in? (Select all that apply)</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resources" id="res-dev" value="devotionals">
                                <label class="form-check-label" for="res-dev">Devotionals</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resources" id="res-pray" value="prayers">
                                <label class="form-check-label" for="res-pray">Prayers</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resources" id="res-scrip" value="scripture">
                                <label class="form-check-label" for="res-scrip">Scripture</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resources" id="res-comm" value="community">
                                <label class="form-check-label" for="res-comm">Community Support</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resources" id="res-tools" value="tools">
                                <label class="form-check-label" for="res-tools">Digital Detox Tools</label>
                            </div>
                        </div>

                        <!-- Question 4 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">4. How would you rate your current spiritual connection?</label>
                            <select class="form-select" name="spiritual" required>
                                <option value="" disabled selected>Select an option....</option>
                                <option value="strong">Strong</option>
                                <option value="moderate">Moderate</option>
                                <option value="weak">Weak</option>
                                <option value="disconnected">Disconnected</option>
                                <option value="seeking">Seeking to improve</option>
                                <option value="new">New to faith</option>
                            </select>
                        </div>

                        <!-- Question 5 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">5. What is your primary goal in overcoming this challenge?</label>
                            <select class="form-select" name="goal" required>
                                <option value="" disabled selected>Select an option....</option>
                                <option value="spiritual_growth">Spiritual Growth</option>
                                <option value="better_relationships">Better Relationships</option>
                                <option value="improved_focus">Improved Focus</option>
                                <option value="emotional_wellbeing">Emotional Well-being</option>
                                <option value="all">All of the above</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submitQuizBtn">Submit Quiz</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    `;
	document.body.insertAdjacentHTML('beforeend', modalHTML);
};

const formatDate = (dateString) => {
	const options = { year: 'numeric', month: 'long', day: 'numeric' };
	return new Date(dateString).toLocaleDateString('en-US', options);
};

const validateEmail = (email) => {
	const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return re.test(email);
};

const sanitizeInput = (input) => {
	const div = document.createElement('div');
	div.textContent = input;
	return div.innerHTML;
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToQuizButtons = () => {
	quizButtons.forEach((button) => {
		button.addEventListener('click', (e) => {
			e.preventDefault();
			showQuizModal();
		});
	});
};

const listenToQuizSubmit = () => {
	submitQuizBtn.addEventListener('click', callbackQuizSubmit());
};

const listenToLogin = () => {
	if (loginBtn) {
		loginBtn.addEventListener('click', callbackLogin);
	}
};

const listentoLogout = () => {
	const logoutBtn = document.querySelector(`.js-logout`);
	if (logoutBtn) {
		logoutBtn.addEventListener('click', callbackLogout);
	}
};

const listenToSearch = () => {
	if (searchForm) {
		searchForm.addEventListener('submit', callbackSearch);
	}
};

const listenToSmoothScroll = () => {
	document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
		anchor.addEventListener('click', callbackSmoothScroll);
	});
};

const listenToNavLinks = () => {
	const currentPage = getCurrentPage();
	navLinks.forEach((link) => {
		const linkPage = link.getAttribute('href');
		if (linkPage === currentPage || (currentPage === '' && linkPage === 'index.html')) {
			link.classList.add('active', 'c-nav__link--active');
		} else {
			link.classList.remove('active', 'c-nav__link--active');
		}
	});
};

const listenToFormValidation = () => {
	const forms = document.querySelectorAll('form');
	forms.forEach((form) => {
		form.addEventListener('submit', callbackFormValidation);
	});
};

const listenToNavbarScroll = () => {
	window.addEventListener('scroll', callbackNavbarScroll);
};

const listenToCardHover = () => {
	cards.forEach((card) => {
		card.addEventListener('mouseenter', callbackCardHoverEnter);
		card.addEventListener('mouseleave', callbackCardHoverLeave);
	});
};

// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const init = () => {
	// Create Quiz Modal
	createQuizModal();

	// Initialize DOM references
	quizButtons = document.querySelectorAll('.js-modal');
	quizModal = new bootstrap.Modal(document.getElementById('quizModal'));
	submitQuizBtn = document.getElementById('submitQuizBtn');
	quizForm = document.getElementById('quizForm');
	loginBtn = document.querySelector('.js-log');
	signupUsernameInput = document.getElementById('signupUsername');
	signupPasswordInput = document.getElementById('signupPassword');
	dropdownBtn = document.querySelector('.js-dropdown-btn');
	dropdownMenu = document.querySelector('.js-dropdown-menu');
	searchForm = document.querySelector('.form.d-flex');
	navbar = document.getElementById('navbar');
	navLinks = document.querySelectorAll('.c-nav__link');
	cards = document.querySelectorAll('.c-card');

	// Set up event listeners
	listenToQuizButtons();
	listenToQuizSubmit();
	listenToLogin();
	listenToSearch();
	listenToSmoothScroll();
	listenToNavLinks();
	listenToFormValidation();
	listenToNavbarScroll();
	listenToCardHover();

	// Check if user is already logged in
	const currentUser = getCurrentUser();
	if (currentUser) {
		showLoginState(currentUser);
	}

	// Console welcome message
	console.log('%cWelcome to FaithGuard! Empowering your journey to digital purity and spiritual growth.', 'color: #F5F5DC; font-size: 1.25rem; font-weight: bold;');
	console.log('%cProtecting Your Faith, One Click at a Time.', 'color: #F5F5DC; font-size: 0.875rem; font-style: italic;');
	console.log('%cVersion 0.0.4 - Released on 2025-11-15', 'color: #282727; font-size: 0.75rem;');
};

document.addEventListener('DOMContentLoaded', init);

if (typeof module !== 'undefined' && module.exports) {
	module.exports = {
		formatDate,
		validateEmail,
		sanitizeInput,
	};
}

// #endregion
