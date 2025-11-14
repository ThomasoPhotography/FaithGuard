// #region ***  DOM references                           ***********
let htmlQuizModal, htmlQuizForm, htmlSignupUsername, htmlSignupPassword, htmlDashboardLink, htmlLoginDropdown;
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showQuizModal = function () {
	const modalHtml = new bootstrap.Modal(htmlQuizModal);
	modalHtml.show();
};

const hideQuizModal = function () {
	const modalHtml = bootstrap.Modal.getInstance(htmlQuizModal);
	modalHtml.hide();
};

const showLoginStatus = function () {
	const loggedIn = getLoginStatus();
	if (loggedIn && htmlDashboardLink) {
		htmlDashboardLink.style.display = 'block';
	} else {
		htmlDashboardLink.style.display = 'none';
	}
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackSignUpLogIn = function () {
	const username = htmlSignupUsername.value.trim();
	const password = htmlSignupPassword.value.trim();

	if (!username || !password) {
		alert('Please enter both username and password.');
		return;
	}
	const storedUser = getStoredUser();
	if (!storedUser) {
		// Sign Up
		storeUser({ username, password });
		alert('Sign Up successful! You can now log in.');
	} else {
		// Login: check credentials
		if (storedUser.username === username && storedUser.password === password) {
			setLoginStatus(true);
			alert('Log In successful!');
			showLoginStatus();
		} else {
			alert('Invalid username or password.');
		}
	}
};

const callbackSubmitQuiz = function () {
	const formData = new FormData(htmlQuizForm);
	let results = {};

	for (let [key, value] of formData.entries()) {
		if (results[key]) {
			if (Array.isArray(results[key])) {
				results[key].push(value);
			} else {
				results[key] = [results[key], value];
			}
		}
	}

	// Validate: Ensure at least one option per question
	if (!results['question1'] || !results['question2'] || !results['question3']) {
		alert('Please answer all questions before submitting the quiz.');
		return;
	}

	localStorage.setItem('quizResults', JSON.stringify(results));
	alert('Quiz submitted! Results saved locally. Personalized guidance coming soon.');

	// Close modal
	hideQuizModal();

	// Reset form
	htmlQuizForm.reset();
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getStoredUser = function () {
	return JSON.parse(localStorage.getItem('user') || 'null');
};

const getLoginStatus = function () {
	return localStorage.getItem('loggedIn') === 'true';
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToSignUpLogIn = function () {
	const button = document.querySelector('.js-log');
	if (button) {
		button.addEventListener('click', callbackSignUpLogIn());
	}
};

const listenToQuizModal = function () {
	const openButton = document.querySelectorAll('.js-modal');
	if (openButton) {
		openButton.addEventListener('click', showQuizModal());
	}
};

const listenToQuizForm = function () {
	const submitButton = document.querySelector('#quizModal . btn[onclick]');
	if (submitButton) {
		submitButton.addEventListener('click', callbackSubmitQuiz());
	}
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const init = function () {
	console.log('Pagge loaded and DOM fully parsed');
	// DOM references
	htmlQuizModal = document.getElementById('.js-modal');
	htmlQuizForm = document.getElementById('quizForm');
	htmlSignupUsername = document.getElementById('signupUsername');
	htmlSignupPassword = document.getElementById('signupPassword');
	htmlLoginDropdown = document.getElementById('.c-dropdown');
	htmlDashboardLink = document.getElementById('dashboardLink');
	// Event listeners
	listenToSignUpLogIn();
	listenToQuizModal();
	listenToQuizForm();
	// Show initial UI state
	showLoginStatus();
};

document.addEventListener('DOMContentLoaded', init);
// #endregion
