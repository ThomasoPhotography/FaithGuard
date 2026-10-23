(() => {
	// #region *** DOM references                           ***********
	const dom = {
		verseTriggers: null,
		modalEl: null,
		modalInstance: null,
		modalTitle: null,
		modalBody: null,
	};
	// #endregion

	// #region *** Callback-Visualisation - show___         ***********
	const showVerseModal = function (title, content) {
		if (dom.modalTitle && title) dom.modalTitle.textContent = title;
		if (dom.modalBody && content) dom.modalBody.innerHTML = `<p>${content}</p>`;

		if (dom.modalInstance) {
			dom.modalInstance.show();
		} else if (dom.modalEl && typeof bootstrap !== 'undefined') {
			dom.modalInstance = new bootstrap.Modal(dom.modalEl);
			dom.modalInstance.show();
		}
	};
	// #endregion

	// ... (Keep the rest of your getVerseData and listenToVerseClicks functions here exactly as before) ...

	// #region *** Init / DOMContentLoaded                  ***********
	const init = function () {
		dom.verseTriggers = document.querySelectorAll('[data-action="open-verse-modal"]');
		dom.modalEl = document.getElementById('verseModal');

		if (dom.modalEl) {
			dom.modalTitle = dom.modalEl.querySelector('.c-modal__title');
			dom.modalBody = dom.modalEl.querySelector('.c-modal__body');
		}

		listenToVerseClicks();
	};

	document.addEventListener('DOMContentLoaded', init);
	// #endregion
})();
