// #region ***  DOM references                           ***********
let verseModal;
let closeModalBtn;
let languageButtons;
let verseContent;
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showVerseModal = function () {
	verseModal.classList.add('is-active');
};

const hideVerseModal = function () {
	verseModal.classList.remove('is-active');
};

const showLoading = function () {
	verseContent.innerHTML = `
        <div class="verse-modal__loading">
            <span class="spinner"></span>
        </div>
    `;
};

const showVerse = function (text, reference) {
	verseContent.innerHTML = `
        <p class="verse-modal__text">"${text}"</p>
        <p class="verse-modal__reference">— ${reference}</p>
    `;
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackLanguageClick = function ({ e }) {
	const { lang } = e.currentTarget.dataset;

	languageButtons.forEach((btn) => btn.classList.remove('verse-modal__lang-btn--active'));

	e.currentTarget.classList.add('verse-modal__lang-btn--active');

	loadVerse(lang);
};
// #endregion

// #region ***  Data Access - get___                     ***********
const loadVerse = async function (lang) {
	showLoading();

	try {
		const response = await fetch(`/api/helper/bible.php?reference=2CO.5.7&lang=${lang}`);
		const data = await response.json();

		if (data.success && data.verse) {
			showVerse(data.verse.text, data.verse.reference);
		} else {
			verseContent.textContent = 'Unable to load scripture.';
		}
	} catch (error) {
		verseContent.textContent = 'Unable to load scripture.';
	}
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToOpenVerse = function () {
	const trigger = document.querySelector('[data-action="open-verse-modal"]');
	if (trigger) {
		trigger.addEventListener('click', function (e) {
			e.preventDefault();
			showVerseModal();
			loadVerse('en');
		});
	}
};

const listenToCloseVerse = function () {
	closeModalBtn.addEventListener('click', hideVerseModal);

	verseModal.addEventListener('click', function (e) {
		if (e.target === verseModal) {
			hideVerseModal();
		}
	});
};

const listenToLanguageButtons = function () {
	languageButtons.forEach((btn) => btn.addEventListener('click', callbackLanguageClick));
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initFooter = function () {
	verseModal = document.getElementById('verse-modal');
	closeModalBtn = document.getElementById('close-verse-modal');
	languageButtons = document.querySelectorAll('.verse-modal__lang-btn');
	verseContent = document.getElementById('verse-content');

	if (!verseModal) return;

	listenToOpenVerse();
	listenToCloseVerse();
	listenToLanguageButtons();
};

document.addEventListener('DOMContentLoaded', initFooter);
// #endregion
