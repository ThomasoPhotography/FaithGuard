/**
 * FaithGuard Main JS
 * Version: 0.0.5
 * Author: Thomas Deseure
 * Release: 2025-11-19
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
            <li><a class="dropdown-item c-dropdown__item js-logout" href="#" id="logoutBtn">Logout</a></li>
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
const callbackQuizSubmit = function (event) {
	event.preventDefault(); // Prevent form submission and page refresh

	if (quizForm.checkValidity()) {
		// Collect form data (updated to match HTML form: frequency, triggers, readiness)
		const formData = new FormData(quizForm);
		const quizData = {
			frequency: formData.get('frequency'),
			triggers: formData.getAll('triggers'), // Array for checkboxes
			readiness: formData.get('readiness'),
			timestamp: new Date().toISOString(),
		};
		// Store in localStorage
		localStorage.setItem('faithGuard_quiz_' + Date.now(), JSON.stringify(quizData));
		showSuccessMessage('Thank you for completing the quiz! Based on your answers, we recommend exploring our resources page for personalized guidance.');
		// Reset form and hide modal
		quizForm.reset();
		quizModal.hide(); // Directly hide the modal instance
		// Redirect immediately to resources page
		window.location.href = 'resources.html';
	} else {
		alert('Please answer all questions.');
	}
};

const callbackLogin = async (e) => {
	e.preventDefault();
	const username = signupUsernameInput.value.trim();
	const password = signupPasswordInput.value.trim();

	if (!username || !password) {
		showErrorMessage('Please enter both username and password.');
		return;
	}

	try {
		const response = await fetch('auth.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({ username, password }),
		});
		const data = await response.json();

		if (data.success) {
			sessionStorage.setItem('faithGuard_loggedInUser', data.username); // Store username client-side for UI
			showSuccessMessage(data.message);
			showLoginState(data.username);
			// Clear inputs
			signupUsernameInput.value = '';
			signupPasswordInput.value = '';
		} else {
			showErrorMessage(data.message);
		}
	} catch (error) {
		showErrorMessage('Login failed. Please try again.');
		console.error('Login error:', error);
	}
};

const callbackLogout = async () => {
	try {
		const response = await fetch('../../src/helper/auth.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({ logout: true }),
		});
		const data = await response.json();

		if (data.success) {
			showSuccessMessage(data.message);
		} else {
			showErrorMessage(data.message);
		}
	} catch (error) {
		showErrorMessage('Logout failed on server, but clearing local data.');
		console.error('Logout error:', error);
	}

	// Always clear client-side data and reload
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
    <div class="modal fade c-modal" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered c-modal__dialog">
            <div class="modal-content c-modal__content">
                <div class="modal-header c-modal__header">
                    <h5 class="modal-title c-modal__title" id="quizModalLabel">FaithGuard Quiz</h5>
                    <button type="button" class="btn-close c-modal__btn c-modal__btn--close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body c-modal__body">
                    <p class="text-muted mb-4">This confidential quiz will help us provide personalized resources for your journey. All responses are private.</p>
                    
                    <form id="quizForm">
                        <!-- Question 1 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold c-modal__label">1. How long have you been struggling with this challenge?</label>
                            <select class="form-select c-modal__select" name="duration" required>
                                <option class="c-modal__option" value="" disabled selected>Select an option....</option>
                                <option class="c-modal__option" value="less_than_6_months">Less than 6 months</option>
                                <option class="c-modal__option" value="6_months_to_1_year">6 months to 1 year</option>
                                <option class="c-modal__option" value="1_to_3_years">1 to 3 years</option>
                                <option class="c-modal__option" value="more_than_3_years">More than 3 years</option>
                            </select>
                        </div>

                        <!-- Question 2 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold c-modal__label">2. Do you currently have an accountability partner?</label>
                            <div class="form-check c-modal__radiobuttons" name="accountability" required>
                                <input class="form-check-input c-modal__option" type="radio" name="accountability" id="acc-yes" value="yes" required>
                                <label class="form-check-label c-modal__label" for="acc-yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input c-modal__option" type="radio" name="accountability" id="acc-no" value="no" required>
                                <label class="form-check-label c-modal__label" for="acc-no">No</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input c-modal__option" type="radio" name="accountability" id="acc-looking" value="looking" required>
                                <label class="form-check-label c-modal__label" for="acc-looking">Looking for one</label>
                            </div>
                        </div>

                        <!-- Question 3 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold c-modal__label">3. What type of resources are you most interested in? (Select all that apply)</label>
                            <div class="form-check c-modal__checkbox-group">
                                <input class="form-check-input c-modal__option" type="checkbox" name="resources" id="res-dev" value="devotionals">
                                <label class="form-check-label c-modal__label" for="res-dev">Devotionals</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input c-modal__option" type="checkbox" name="resources" id="res-pray" value="prayers">
                                <label class="form-check-label c-modal__label" for="res-pray">Prayers</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input c-modal__option" type="checkbox" name="resources" id="res-scrip" value="scripture">
                                <label class="form-check-label c-modal__label" for="res-scrip">Scripture</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input c-modal__option" type="checkbox" name="resources" id="res-comm" value="community">
                                <label class="form-check-label c-modal__label" for="res-comm">Community Support</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input c-modal__option" type="checkbox" name="resources" id="res-tools" value="tools">
                                <label class="form-check-label c-modal__label" for="res-tools">Digital Detox Tools</label>
                            </div>
                        </div>

                        <!-- Question 4 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold c-modal__label">4. How would you rate your current spiritual connection?</label>
                            <select class="form-select c-modal__select" name="spiritual" required>
                                <option value="" disabled selected class="c-modal__option">Select an option....</option>
                                <option value="strong" class="c-modal__option">Strong</option>
                                <option value="moderate" class="c-modal__option">Moderate</option>
                                <option value="weak" class="c-modal__option">Weak</option>
                                <option value="disconnected" class="c-modal__option">Disconnected</option>
                                <option value="seeking" class="c-modal__option">Seeking to improve</option>
                                <option value="new" class="c-modal__option">New to faith</option>
                            </select>
                        </div>

                        <!-- Question 5 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold c-modal__label">5. What is your primary goal in overcoming this challenge?</label>
                            <select class="form-select c-modal__select" name="goal" required>
                                <option value="" disabled selected class="c-modal__option">Select an option....</option>
                                <option value="spiritual_growth" class="c-modal__option">Spiritual Growth</option>
                                <option value="better_relationships" class="c-modal__option">Better Relationships</option>
                                <option value="improved_focus" class="c-modal__option">Improved Focus</option>
                                <option value="emotional_wellbeing">Emotional Well-being</option>
                                <option value="all">All of the above</option>
                            </select>
                        </div>
                        <div class="modal-footer c-modal__footer">
                            <button type="button" class="btn c-modal__btn c-modal__btn--cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn c-modal__btn c-modal__btn--submit js-submit">Submit Quiz</button>
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
	// Attach to form submit event instead of button click for reliable prevention
	quizForm.addEventListener('submit', callbackQuizSubmit);
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
	submitQuizBtn = document.querySelector('.js-submit');
	quizForm = document.getElementById('quizForm');
	loginBtn = document.querySelector('.js-log');
	signupUsernameInput = document.getElementById('signupUsername');
	signupPasswordInput = document.getElementById('signupPassword');
	dropdownBtn = document.querySelector('.js-dropdown-btn');
	dropdownMenu = document.querySelector('.js-dropdown-menu');
	searchForm = document.querySelector('.form.d-flex');
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
	listenToCardHover();

	// Check if user is already logged in
	const currentUser = getCurrentUser();
	if (currentUser) {
		showLoginState(currentUser);
	}

	// Console welcome message
	console.log('%cPage loaded', 'color: #F5F5DC; font-size: 0.5rem; font-weight: bold;');
	console.log('%cWelcome to FaithGuard! Empowering your journey to digital purity and spiritual growth.', 'background-color: #2F4F4F; color: #F5F5DC; font-size: 1.25rem; font-weight: bold;');
	console.log('%cProtecting Your Faith, One Click at a Time.', 'background-color: #2F4F4F; color: #F5F5DC; font-size: 0.875rem; font-style: italic;');
	console.log('%cVersion 0.0.5 - Released on 2025-11-16', 'color: #F5F5DC; font-size: 0.75rem;');
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
