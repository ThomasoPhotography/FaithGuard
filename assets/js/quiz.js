// #region ***  DOM references                           ***********
const quizForm = document.querySelector('.c-quiz__form');
const progressBar = document.querySelector('.c-quiz__progress');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showStep = (step) => {
	document.querySelectorAll('.step').forEach((el) => (el.style.display = 'none'));
	document.getElementById(`step-${step}`).style.display = 'block';
	progressBar.style.width = `${(step / 3) * 100}%`; // Assume 3 steps
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackSubmitQuiz = (data) => {
	if (data.success) alert('Quiz submitted! Tags: ' + data.tags.join(', '));
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getQuizData = () => {
	const formData = new FormData(quizForm);
	return Object.fromEntries(formData);
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToNextStep = () => {
	// Inline in HTML: onclick="nextStep(1)"
};
const listenToSubmit = () => {
	quizForm.addEventListener('submit', (e) => {
		e.preventDefault();
		const data = getQuizData();
		fetch('/api/quiz/submit.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(data),
		})
			.then((response) => response.json())
			.then(callbackSubmitQuiz);
	});
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initQuiz = () => {
	showStep(1);
	listenToSubmit();
};
document.addEventListener('DOMContentLoaded', initQuiz);
// #endregion
