// #region ***  Quiz Data Structures and DOM References ***********
const quizForm = document.querySelector('.c-quiz__form');
const quizContent = document.querySelector('.c-quiz__content');
const quizProgressBar = document.querySelector('.c-progress__bar');

const nextButton = document.getElementById('nextButton');
const prevButton = document.getElementById('prevButton');
const submitButton = document.getElementById('submitButton');

const questions = document.querySelectorAll('.c-quiz__question');

let currentStep = 0;
let isSubmitting = false;
// #endregion

// #region *** Utilities ********************************************
const isQuestionAnswered = (stepIndex) => {
	const question = questions[stepIndex];
	if (!question) return false;

	if (stepIndex === 0) {
		return [...question.querySelectorAll('input[type="checkbox"]')].some(cb => cb.checked);
	}

	return [...question.querySelectorAll('input[type="radio"]')].some(r => r.checked);
};

const showValidationError = (stepIndex) => {
	const question = questions[stepIndex];
	if (!question || question.querySelector('.quiz-warning')) return;

	const warning = document.createElement('div');
	warning.className = 'quiz-warning text-danger mt-2';
	warning.textContent = 'Please select an answer before continuing.';
	question.appendChild(warning);
};

const clearValidationError = (stepIndex) => {
	const warning = questions[stepIndex]?.querySelector('.quiz-warning');
	if (warning) warning.remove();
};
// #endregion

// #region *** Rendering *******************************************
const renderQuestion = (stepIndex) => {
	questions.forEach((q, i) => {
		q.style.display = i === stepIndex ? 'block' : 'none';
	});

	updateProgressBar(stepIndex);
	updateNavigation(stepIndex);
};

const updateProgressBar = (stepIndex) => {
	if (!quizProgressBar) return;

	const progress = Math.round(((stepIndex + 1) / questions.length) * 100);
	quizProgressBar.style.width = `${progress}%`;
	quizProgressBar.setAttribute('aria-valuenow', progress);
};

const updateNavigation = (stepIndex) => {
	prevButton.style.display = stepIndex > 0 ? 'inline-block' : 'none';

	if (stepIndex === questions.length - 1) {
		nextButton.style.display = 'none';
		submitButton.hidden = false;
	} else {
		nextButton.style.display = 'inline-block';
		submitButton.hidden = true;
	}
};
// #endregion

// #region *** Navigation Logic ************************************
const nextStep = () => {
	if (!isQuestionAnswered(currentStep)) {
		showValidationError(currentStep);
		return;
	}

	clearValidationError(currentStep);

	if (currentStep < questions.length - 1) {
		currentStep++;
		renderQuestion(currentStep);
	}
};

const prevStep = () => {
	if (currentStep > 0) {
		currentStep--;
		renderQuestion(currentStep);
	}
};
// #endregion

// #region *** Submission ******************************************
const submitQuiz = async (event) => {
	event.preventDefault();
	if (isSubmitting) return;

	isSubmitting = true;
	submitButton.disabled = true;

	const formData = new FormData(quizForm);
	const addictionTypes = [];
	const answers = {};

	for (const [key, value] of formData.entries()) {
		if (key === 'addiction_type[]') {
			addictionTypes.push(value);
		}

		if (key.startsWith('answers[')) {
			const id = key.match(/\[(\d+)\]/)[1];
			answers[id] = Number(value);
		}
	}

	if (!addictionTypes.length) {
		alert('Please select at least one struggle.');
		isSubmitting = false;
		submitButton.disabled = false;
		return;
	}

	try {
		const response = await fetch('/api/quiz/submit.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				addiction_types: addictionTypes,
				answers: answers,
			}),
		});

		const result = await response.json();

		if (!result.success) {
			alert(result.error || 'Submission failed.');
			isSubmitting = false;
			submitButton.disabled = false;
			return;
		}

		// ✅ ROLE-BASED REDIRECT
		const role = result.role || 'user';

		if (role === 'admin') {
			window.location.href = '/admin/profile.php';
		} else if (role === 'user') {
			window.location.href = '/users/profile.php';
		} else {
            console.warn('Unknown user role:', role);
            window.location.href = '/index.php';
        }

	} catch (err) {
		console.error(err);
		alert('Network error. Please try again.');
		isSubmitting = false;
		submitButton.disabled = false;
	}
};
// #endregion

// #region *** Event Listeners *************************************
const listenToQuizControls = () => {
	if (!quizForm || !nextButton || !prevButton || !submitButton) return;

	nextButton.addEventListener('click', nextStep);
	prevButton.addEventListener('click', prevStep);
	quizForm.addEventListener('submit', submitQuiz);

	questions.forEach((question, index) => {
		question.querySelectorAll('input').forEach(input => {
			input.addEventListener('change', () => clearValidationError(index));
		});
	});
};
// #endregion

// #region *** Init *************************************************
const initQuiz = () => {
	if (!quizForm || !questions.length) return;

	renderQuestion(currentStep);
	listenToQuizControls();
};

document.addEventListener('DOMContentLoaded', initQuiz);
// #endregion
