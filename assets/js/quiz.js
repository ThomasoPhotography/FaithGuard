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

	const inputs = Array.from(question.querySelectorAll('input'));
	if (!inputs.length) return true;

	const type = inputs[0].type;

	if (type === 'checkbox') {
		return inputs.some((i) => i.checked);
	}

	if (type === 'radio') {
		return inputs.some((i) => i.checked);
	}

	return true;
};

const showValidationError = (stepIndex, message) => {
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
		showValidationError(currentStep, 'Please answer this question before continuing.');
		return;
	}

	clearValidationError(currentStep);
	currentStep++;
	renderQuestion(currentStep);
};

const prevStep = () => {
	clearValidationError(currentStep);
	currentStep--;
	renderQuestion(currentStep);
};
// #endregion

// #region *** Submission ******************************************
const submitQuiz = async (event) => {
	event.preventDefault();
	if (isSubmitting) return;

	isSubmitting = true;
	submitButton.disabled = true;

	const formData = new FormData(quizForm);
	const payload = {
		addiction_types: [],
		answers: {},
	};

	for (const [key, value] of formData.entries()) {
		if (key === 'addiction_types[]') {
			payload.addiction_types.push(value);
		}

		if (key.startsWith('answers[')) {
			const id = key.match(/\[(\d+)\]/)?.[1];
			if (id) payload.answers[id] = Number(value);
		}
	}

	try {
		const response = await fetch('/api/quiz/submit.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(payload),
		});

		const result = await response.json();
		if (!result.success) throw new Error(result.error || 'Submission failed');

		window.location.href = result.role === 'admin' ? '/admin/profile.php' : '/users/profile.php';
	} catch (err) {
		alert(err.message || 'Network error');
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
document.addEventListener('DOMContentLoaded', () => {
	if (!quizForm || !questions.length) return;

	renderQuestion(0);

	nextButton.addEventListener('click', nextStep);
	prevButton.addEventListener('click', prevStep);
	quizForm.addEventListener('submit', submitQuiz);

	questions.forEach((q, i) => {
		q.querySelectorAll('input').forEach((input) => {
			input.addEventListener('change', () => clearValidationError(i));
		});
	});
});
// #endregion
