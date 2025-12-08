// #region ***  Quiz Data Structures and DOM References             ***********
const quizContent = document.querySelector('.c-quiz__content');
const quizProgress = document.querySelector('.c-quiz__progress');
const nextButton = document.getElementById('nextButton');
const prevButton = document.getElementById('prevButton');
const submitButton = document.getElementById('submitButton');
const quizForm = document.getElementById('quizForm');

// Scoring scale: 1 (Never) to 5 (Very Often)
const likertScale = [
	{ text: 'Never', value: 1 },
	{ text: 'Rarely', value: 2 },
	{ text: 'Sometimes', value: 3 },
	{ text: 'Often', value: 4 },
	{ text: 'Very Often', value: 5 },
];

// Conditional Weighting based on Q1 Addiction Type (used in calculateScore)
const weightingScheme = {
	// QUESTION GROUP WEIGHTS
	spiritual_health: { sexual: 1.3, pornography: 1.3, alcohol: 1.0, substance: 1.0, gambling: 1.0, digital_media: 1.0, food_binge: 1.0, smoking: 1.0, other: 1.0 },
	emotional_control: { sexual: 1.2, pornography: 1.2, alcohol: 1.1, substance: 1.2, gambling: 1.2, digital_media: 1.0, food_binge: 1.1, smoking: 1.1, other: 1.0 },
	behavioural_impact: { sexual: 1.2, pornography: 1.2, alcohol: 1.3, substance: 1.3, gambling: 1.2, digital_media: 1.0, food_binge: 1.1, smoking: 1.2, other: 1.0 },
	relational_impact: { sexual: 1.2, pornography: 1.2, alcohol: 1.2, substance: 1.1, gambling: 1.2, digital_media: 1.1, food_binge: 1.0, smoking: 1.1, other: 1.0 },
	daily_functioning: { sexual: 1.0, pornography: 1.0, alcohol: 1.2, substance: 1.4, gambling: 1.3, digital_media: 1.2, food_binge: 1.3, smoking: 1.3, other: 1.0 },
	faith_reflection: { sexual: 1.3, pornography: 1.3, alcohol: 1.0, substance: 1.0, gambling: 1.0, digital_media: 1.0, food_binge: 1.0, smoking: 1.0, other: 1.0 },
	self_awareness: { sexual: 1.0, pornography: 1.0, alcohol: 1.2, substance: 1.2, gambling: 1.1, digital_media: 1.0, food_binge: 1.0, smoking: 1.1, other: 1.0 },

	// Top-level multiplier from Q1 for overall risk (This is the old, simpler structure, kept here for reference)
	// overall_risk: { substance: 1.4, alcohol: 1.3, sexual: 1.5, pornography: 1.5, gambling: 1.3, digital_media: 1.1, food_binge: 1.1, smoking: 1.2, other: 1.0 }
};
const quizData = [
	{
		id: 1,
		question: 'What type of addiction are you currently struggling with?',
		type: 'radio',
		name: 'addiction_type', // CRITICAL: This answer determines the WEIGHTS
		options: [
			{ text: 'Substance', value: 'substance' },
			{ text: 'Alcohol', value: 'alcohol' },
			{ text: 'Sexual', value: 'sexual' },
			{ text: 'Pornography', value: 'pornography' },
			{ text: 'Gambling', value: 'gambling' },
			{ text: 'Digital/Social Media', value: 'digital_media' },
			{ text: 'Food/Binge Eating', value: 'food_binge' },
			{ text: 'Smoking', value: 'smoking' },
			{ text: 'Other', value: 'other' },
		],
	},
	{
		id: 2,
		question: 'How often do you pray or engage in spiritual reflection?',
		type: 'radio',
		name: 'spiritual_frequency',
		options: [
			// Question 2 is now a simple score additive, not a multiplier base.
			{ text: 'Daily', value: 5, score: 5 },
			{ text: 'Weekly', value: 3, score: 3 },
			{ text: 'Rarely', value: 1, score: 1 },
			{ text: 'Never', value: -5, score: -5 }, // Negative score for 'Never'
		],
	},
	// SECTION 2 — 34 Likert-Scale Questions

	// SPIRITUAL HEALTH & ALIGNMENT (Q3-Q7) - Group: spiritual_health
	{ id: 3, question: 'I turn to my addiction instead of prayer when I feel stressed.', type: 'radio', name: 'q3_spiritual_stress', group: 'spiritual_health', options: likertScale },
	{ id: 4, question: 'I feel distant from God because of my addictive habits.', type: 'radio', name: 'q4_spiritual_distant', group: 'spiritual_health', options: likertScale },
	{ id: 5, question: 'My addiction makes me avoid church, fellowship, or spiritual practices.', type: 'radio', name: 'q5_spiritual_avoid', group: 'spiritual_health', options: likertScale },
	{ id: 6, question: 'I feel convicted by the Holy Spirit when engaging in my addiction.', type: 'radio', name: 'q6_spiritual_convicted', group: 'spiritual_health', options: likertScale },
	{ id: 7, question: 'I rely more on my addiction than on God for comfort or relief.', type: 'radio', name: 'q7_spiritual_comfort', group: 'spiritual_health', options: likertScale },

	// EMOTIONAL CONTROL & COPING (Q8-Q12) - Group: emotional_control
	{ id: 8, question: 'I use my addiction to cope with difficult emotions.', type: 'radio', name: 'q8_emotional_cope', group: 'emotional_control', options: likertScale },
	{ id: 9, question: 'I feel anxious or irritable when I can’t engage in my addiction.', type: 'radio', name: 'q9_emotional_anxious', group: 'emotional_control', options: likertScale },
	{ id: 10, question: 'I feel guilt or shame after giving in to my addiction.', type: 'radio', name: 'q10_emotional_guilt', group: 'emotional_control', options: likertScale },
	{ id: 11, question: 'My addiction feels like something I cannot control.', type: 'radio', name: 'q11_emotional_control', group: 'emotional_control', options: likertScale },
	{ id: 12, question: 'I hide my struggles because I fear judgment from others.', type: 'radio', name: 'q12_emotional_hide', group: 'emotional_control', options: likertScale },

	// BEHAVIOURAL IMPACT (Q13-Q17) - Group: behavioural_impact
	{ id: 13, question: 'I have tried to cut down but failed.', type: 'radio', name: 'q13_behaviour_failed', group: 'behavioural_impact', options: likertScale },
	{ id: 14, question: 'I spend more time than intended engaging with my addiction.', type: 'radio', name: 'q14_behaviour_time', group: 'behavioural_impact', options: likertScale },
	{ id: 15, question: 'I neglect responsibilities (school, work, home) because of my addiction.', type: 'radio', name: 'q15_behaviour_neglect', group: 'behavioural_impact', options: likertScale },
	{ id: 16, question: 'I’ve taken risks or made unwise decisions because of my addiction.', type: 'radio', name: 'q16_behaviour_risk', group: 'behavioural_impact', options: likertScale },
	{ id: 17, question: 'I experience cravings that feel overwhelming.', type: 'radio', name: 'q17_behaviour_cravings', group: 'behavioural_impact', options: likertScale },

	// RELATIONAL IMPACT (Q18-Q22) - Group: relational_impact
	{ id: 18, question: 'My relationships have suffered because of my addiction.', type: 'radio', name: 'q18_relational_suffered', group: 'relational_impact', options: likertScale },
	{ id: 19, question: 'I become defensive or irritated when someone confronts me about my habits.', type: 'radio', name: 'q19_relational_defensive', group: 'relational_impact', options: likertScale },
	{ id: 20, question: 'I isolate myself to hide my addictive behaviour.', type: 'radio', name: 'q20_relational_isolate', group: 'relational_impact', options: likertScale },
	{ id: 21, question: 'I avoid accountability from friends, family, or church.', type: 'radio', name: 'q21_relational_avoid', group: 'relational_impact', options: likertScale },
	{ id: 22, question: 'My addiction causes conflict with people close to me.', type: 'radio', name: 'q22_relational_conflict', group: 'relational_impact', options: likertScale },

	// DAILY LIFE & FUNCTIONING (Q23-Q27) - Group: daily_functioning
	{ id: 23, question: 'I delay tasks or responsibilities because of my addiction.', type: 'radio', name: 'q23_daily_delay', group: 'daily_functioning', options: likertScale },
	{ id: 24, question: 'I prioritize my addiction over healthier activities.', type: 'radio', name: 'q24_daily_prioritize', group: 'daily_functioning', options: likertScale },
	{ id: 25, question: 'My sleep schedule is negatively affected by my addiction.', type: 'radio', name: 'q25_daily_sleep', group: 'daily_functioning', options: likertScale },
	{ id: 26, question: 'My finances are impacted because of my addiction.', type: 'radio', name: 'q26_daily_finance', group: 'daily_functioning', options: likertScale },
	{ id: 27, question: 'My physical health has worsened due to addictive behaviours.', type: 'radio', name: 'q27_daily_health', group: 'daily_functioning', options: likertScale },

	// FAITH-BASED REFLECTION & ACCOUNTABILITY (Q28-Q32) - Group: faith_reflection
	{ id: 28, question: 'I feel that my addiction is harming my relationship with God.', type: 'radio', name: 'q28_faith_harming', group: 'faith_reflection', options: likertScale },
	{ id: 29, question: 'I avoid reading Scripture or praying when I slip into addiction.', type: 'radio', name: 'q29_faith_avoid_scripture', group: 'faith_reflection', options: likertScale },
	{ id: 30, question: 'I feel unworthy of God’s forgiveness because of my addiction.', type: 'radio', name: 'q30_faith_unworthy', group: 'faith_reflection', options: likertScale },
	{ id: 31, question: 'I struggle to trust God with my healing and recovery.', type: 'radio', name: 'q31_faith_struggle_trust', group: 'faith_reflection', options: likertScale },
	{ id: 32, question: 'I believe my addiction is becoming an idol in my life.', type: 'radio', name: 'q32_faith_idol', group: 'faith_reflection', options: likertScale },

	// SELF-AWARENESS & DESIRE FOR CHANGE (Q33-Q36) - Group: self_awareness
	{ id: 33, question: 'I want to stop, but I feel unable to do so.', type: 'radio', name: 'q33_self_unable', group: 'self_awareness', options: likertScale },
	{ id: 34, question: 'I recognize patterns in my life that lead me toward my addiction.', type: 'radio', name: 'q34_self_recognize_patterns', group: 'self_awareness', options: likertScale },
	{ id: 35, question: 'I worry about long-term consequences of my addictive behaviours.', type: 'radio', name: 'q35_self_worry', group: 'self_awareness', options: likertScale },
	{ id: 36, question: 'I desire accountability or support to overcome my struggles.', type: 'radio', name: 'q36_self_desire_support', group: 'self_awareness', options: likertScale },
];

let currentStep = 0;
let userAnswers = {}; // Stores user answers and calculated scores/weights

// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const renderQuestion = (stepIndex) => {
	if (!quizContent || stepIndex >= quizData.length || stepIndex < 0) {
		quizContent.innerHTML = '<p class="text-center text-success fw-bold">Quiz Complete. Please submit your answers.</p>';
		return;
	}

	const questionData = quizData[stepIndex];
	let html = `<p class="c-quiz__question mb-3 fw-bold">${stepIndex + 1}. ${questionData.question}</p>`;

	// Determine which set of options to use (Q1, Q2, or Likert Scale)
	let optionsToRender = questionData.options;

	// For questions 3 and above, the score value is the Likert scale value itself (1-5)
	// We only need the multiplier weight attribute for Q1 and the score for Q2.
	if (stepIndex > 1) {
		optionsToRender = LIKERT_SCALE.map((opt) => ({
			text: opt.text,
			value: opt.value,
			score: opt.value, // Likert score is the score
		}));
	}

	optionsToRender.forEach((option) => {
		const uniqueId = `${questionData.name}-${option.value}`;
		// Store the actual score (for Q2 and Likert) or the multiplier (for Q1)
		const scoreValue = option.score !== undefined ? option.score : 0;
		const weightValue = option.weight !== undefined ? option.weight : scoreValue;

		const isChecked = userAnswers[questionData.name] === option.value.toString();
		const checkedAttribute = isChecked ? 'checked' : '';

		html += `
            <div class="form-check c-quiz__option-container">
                <input class="form-check-input" type="${questionData.type}" 
                       name="${questionData.name}" id="${uniqueId}" value="${option.value}"
                       data-score="${scoreValue}" 
                       data-weight="${weightValue}" ${checkedAttribute}>
                <label class="form-check-label" for="${uniqueId}">
                    ${option.text}
                </label>
            </div>
        `;
	});

	quizContent.innerHTML = html;
	updateNavigationButtons(stepIndex);
	updateProgressBar(stepIndex);
};

const updateProgressBar = (stepIndex) => {
	// ... (omitted: no changes)
	const totalSteps = QUIZ_DATA.length;
	const progress = ((stepIndex + 1) / totalSteps) * 100;
	quizProgressBar.style.width = `${progress}%`;
	quizProgressBar.setAttribute('aria-valuenow', progress);
	quizProgressBar.textContent = `Question ${stepIndex + 1} of ${totalSteps}`;
};

const updateNavigationButtons = (stepIndex) => {
	// ... (omitted: no changes)
	prevButton.style.display = stepIndex > 0 ? 'inline-block' : 'none';

	if (stepIndex === QUIZ_DATA.length - 1) {
		nextButton.style.display = 'none';
		submitButton.style.display = 'inline-block';
	} else {
		nextButton.style.display = 'inline-block';
		submitButton.style.display = 'none';
	}
};
// #endregion

// #region ***  Quiz Logic                             ***********

const saveCurrentAnswer = () => {
	const questionData = QUIZ_DATA[currentStep];
	const selected = quizForm.querySelector(`input[name="${questionData.name}"]:checked`);

	if (selected) {
		// Store answer value (string)
		userAnswers[questionData.name] = selected.value;

		// Store associated score/weight. Weight is crucial for Q1, Score for all others.
		// We use data-score for Q2-Q36 score and data-weight for Q1 multiplier
		userAnswers[`${questionData.name}_score`] = parseFloat(selected.getAttribute('data-score'));
		userAnswers[`${questionData.name}_weight`] = parseFloat(selected.getAttribute('data-weight')); // Used only for Q1

		return true;
	} else {
		alert('Please select an option before proceeding.');
		return false;
	}
};

const nextStep = () => {
	// ... (omitted: no changes)
	if (saveCurrentAnswer()) {
		currentStep++;
		renderQuestion(currentStep);
	}
};

const prevStep = () => {
	// ... (omitted: no changes)
	currentStep--;
	renderQuestion(currentStep);
};

const calculateScore = () => {
	let rawScore = 0;
	let finalScore = 0;

	// --- 1. Identify Addiction Type (Q1) ---
	const addictionType = userAnswers['addiction_type'] || 'other';
	const finalRiskMultiplier = userAnswers['addiction_type_weight'] || 1.0;

	// --- 2. Calculate Raw Weighted Score from all other questions (Q2 - Q36) ---

	QUIZ_DATA.forEach((questionData) => {
		// Skip Question 1 (Addiction Type) as it only provides the overall multiplier
		if (questionData.name === 'addiction_type') {
			return;
		}

		const answerScore = userAnswers[`${questionData.name}_score`];
		const groupKey = questionData.group;

		if (answerScore !== undefined && answerScore !== null) {
			let conditionalWeight = 1.0;

			// Apply conditional weight based on the question group and addiction type
			if (groupKey && WEIGHTING_SCHEME[groupKey] && WEIGHTING_SCHEME[groupKey][addictionType] !== undefined) {
				conditionalWeight = WEIGHTING_SCHEME[groupKey][addictionType];
			} else if (questionData.name === 'spiritual_frequency') {
				// Q2 is a special case (fixed score, no conditional weight by group)
				// Score is used directly.
				rawScore += answerScore;
				return;
			}

			// Add score * conditional weight
			rawScore += answerScore * conditionalWeight;
		}
	});

	// --- 3. Apply Final Score Logic ---
	// The previous implementation used the Q1 multiplier on the final score.
	// Given the complexity of the new conditional group weights, we now use the
	// Q1 weight (1.0 - 1.5) as the final overall risk factor, combining the raw weighted score with it.

	// If the top-level Q1 weight is meant to be a final, overall multiplier:
	// finalScore = rawScore * finalRiskMultiplier;

	// For simplicity, let's return the Raw Weighted Score (which includes all conditional multipliers)
	// and let the user decide if the Q1 overall risk factor is needed.
	// For now, we will apply the *overall* Q1 risk factor to the final result.
	finalScore = rawScore * finalRiskMultiplier;

	return { totalScore: finalScore, addictionType, answers: userAnswers };
};

const submitQuiz = (event) => {
	// ... (omitted: no changes)
	event.preventDefault();
	if (saveCurrentAnswer()) {
		const result = calculateScore();

		// Final submission logic (AJAX POST to /api/quiz/submit.php)
		// This simulates the actual submission
		console.log('Quiz Results:', result);
		alert(`Quiz submitted! Your calculated score is: ${result.totalScore.toFixed(2)}. Check the console for details.`);

		// Redirect or show thank you page later
	}
};

// #endregion

// #region ***  Event Listeners - listenTo___            ***********

const listenToQuizControls = () => {
	// ... (omitted: no changes)
	nextButton.addEventListener('click', nextStep);
	prevButton.addEventListener('click', prevStep);
	quizForm.addEventListener('submit', submitQuiz);
};

// #endregion

// #region ***  Init / DOMContentLoaded                  ***********

const initQuiz = () => {
	// ... (omitted: no changes)
	if (quizContent) {
		renderQuestion(currentStep);
		listenToQuizControls();
	}
};
document.addEventListener('DOMContentLoaded', initQuiz);
// #endregion
