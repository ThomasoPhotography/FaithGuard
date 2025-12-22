// #region ***  Quiz Data Structures and DOM References ***********
const quizForm        = document.querySelector('.c-quiz__form');
const quizContent     = document.querySelector('.c-quiz__content');
const quizProgressBar = document.querySelector('.c-progress__bar');

const nextButton   = document.getElementById('nextButton');
const prevButton   = document.getElementById('prevButton');
const submitButton = document.getElementById('submitButton');

const questions = document.querySelectorAll('.c-quiz__question');

let currentStep = 0;
// #endregion

// #region *** Utilities ********************************************
const isQuestionAnswered = (stepIndex) => {
    const question = questions[stepIndex];
    if (!question) return false;

    // Step 0 → checkbox validation
    if (stepIndex === 0) {
        return [...question.querySelectorAll('input[type="checkbox"]')]
            .some(cb => cb.checked);
    }

    // Other steps → radio validation
    return [...question.querySelectorAll('input[type="radio"]')]
        .some(r => r.checked);
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
    const question = questions[stepIndex];
    const warning  = question?.querySelector('.quiz-warning');
    if (warning) warning.remove();
};
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
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
    const progress   = Math.round(((stepIndex + 1) / totalSteps) * 100);

    quizProgressBar.style.width = `${progress}%`;
    quizProgressBar.setAttribute('aria-valuenow', progress);
};

const updateNavigation = (stepIndex) => {
    prevButton.style.display = stepIndex > 0 ? 'inline-block' : 'none';

    if (stepIndex === questions.length - 1) {
        nextButton.style.display   = 'none';
        submitButton.hidden = false;
    } else {
        nextButton.style.display   = 'inline-block';
        submitButton.hidden = true;
    }
};
// #endregion

// #region ***  Navigation Logic                         ***********
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
        return;
    }

    try {
        const response = await fetch('/api/quiz/submit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                addiction_types: addictionTypes,
                answers: answers
            })
        });

        const result = await response.json();

        if (!result.success) {
            alert(result.error || 'Submission failed.');
            return;
        }

        window.location.href = '/quiz-result.php';

    } catch (err) {
        console.error(err);
        alert('Network error. Please try again.');
    }
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToQuizControls = () => {
    if (!nextButton || !prevButton || !quizForm) return;

    nextButton.addEventListener('click', nextStep);
    prevButton.addEventListener('click', prevStep);
    quizForm.addEventListener('submit', submitQuiz);

    // Clear validation error when user selects an option
    questions.forEach((question, index) => {
        const inputs = question.querySelectorAll('input[type="radio"]');
        inputs.forEach(input => {
            input.addEventListener('change', () => clearValidationError(index));
        });
    });
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initQuiz = () => {
    if (!quizForm || !questions.length) return;

    renderQuestion(currentStep);
    listenToQuizControls();

    // Clear validation error on input change
    questions.forEach((q, index) => {
        q.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', () => clearValidationError(index));
        });
    });
};

document.addEventListener('DOMContentLoaded', initQuiz);
// #endregion