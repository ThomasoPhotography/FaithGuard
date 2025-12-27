// #region ***  Quiz Data Structures and DOM References ***********
const quizForm = document.querySelector('.c-quiz__form');
const quizProgressBar = document.querySelector('.c-progress__bar');

const nextButton = document.getElementById('nextButton');
const prevButton = document.getElementById('prevButton');
const submitButton = document.getElementById('submitButton');

const questions = Array.from(document.querySelectorAll('.c-quiz__question'));

let currentStep = 0;
let isSubmitting = false;
// #endregion

// #region *** Utilities ********************************************
const isQuestionAnswered = (stepIndex) => {
	const question = questions[stepIndex];
	if (!question) return false;

	// Addiction selection (checkboxes)
	if (stepIndex === 0) {
		return Array.from(question.querySelectorAll('input[type="checkbox"]')).some((cb) => cb.checked);
	}

	// Normal quiz questions (radio buttons)
	return Array.from(question.querySelectorAll('input[type="radio"]')).some((r) => r.checked);
};

const showValidationError = (stepIndex, message = 'Please select an answer before continuing.') => {
	const question = questions[stepIndex];
	if (!question) return;

	let warning = question.querySelector('.quiz-warning');
	if (!warning) {
		warning = document.createElement('div');
		warning.className = 'quiz-warning text-danger mt-2';
		question.appendChild(warning);
	}

	warning.textContent = message;
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

	const totalSteps = questions.length;
	const progress = Math.round(((stepIndex + 1) / totalSteps) * 100);

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
		const message = currentStep === 0 ? 'Please select at least one struggle to continue.' : 'Please select an answer before continuing.';

		showValidationError(currentStep, message);
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
		clearValidationError(currentStep);
		currentStep--;
		renderQuestion(currentStep);
	}
};
// #endregion

// #region *** Submission ******************************************
const submitQuiz = async (event) => {
	event.preventDefault();
	if (isSubmitting) return;

	// Final validation safeguard
	if (!isQuestionAnswered(0)) {
		renderQuestion(0);
		showValidationError(0, 'Please select at least one struggle.');
		return;
	}
	isSubmitting = true;
	submitButton.disabled = true;
	const formData = new FormData(quizForm);
	const addictionTypes = [];
	const answers = {};
	for (const [key, value] of formData.entries()) {
		if (key === 'addiction_types[]') {
			addictionTypes.push(value);
		}

		if (key.startsWith('answers[')) {
			const match = key.match(/\[(\d+)\]/);
			if (match) {
				answers[match[1]] = Number(value);
			}
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
		// ROLE-BASED REDIRECT
		const role = result.role || 'user';
		if (role === 'admin') {
			window.location.href = '/admin/profile.php';
		} else if (role === 'user') {
			window.location.href = '/users/profile.php';
		} else {
			window.location.href = '/index.php';
		}
	} catch (error) {
		console.error(error);
		alert('Network error. Please try again.');
		isSubmitting = false;
		submitButton.disabled = false;
	}
};
// #endregion

// #region *** Event Listeners *************************************
const listenToQuizControls = () => {
	if (!quizForm) return;
	nextButton.addEventListener('click', nextStep);
	prevButton.addEventListener('click', prevStep);
	quizForm.addEventListener('submit', submitQuiz);
	questions.forEach((question, index) => {
		question.querySelectorAll('input').forEach((input) => {
			input.addEventListener('change', () => clearValidationError(index));
		});
	});
};
// #endregion

// #region *** Init *************************************************
const initQuiz = () => {
	if (!quizForm || !questions.length) return;
	currentStep = 0;
	renderQuestion(currentStep);
	listenToQuizControls();
};

document.addEventListener('DOMContentLoaded', initQuiz);
// #endregion
