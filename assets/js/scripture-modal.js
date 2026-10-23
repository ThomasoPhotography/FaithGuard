(function () {
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

	// #region *** Callback-No Visualisation - callback___  ***********
	// #endregion

	// #region *** Data Access - get___                     ***********
	const getVerseData = async function (reference) {
		// Placeholder for your planned local API (L-API) fetch
		/*
        try {
            const response = await fetch(`/api/scripture.php?ref=${encodeURIComponent(reference)}`);
            return await response.json();
        } catch (error) {
            console.error("Failed to fetch Scripture:", error);
        }
        */
	};
	// #endregion

	// #region *** Event Listeners - listenTo___            ***********
	const listenToVerseClicks = function () {
		if (!dom.verseTriggers) return;

		dom.verseTriggers.forEach((trigger) => {
			trigger.addEventListener('click', function (e) {
				e.preventDefault();
				const reference = this.textContent.trim();

				// Hardcoded fallback for current static implementation
				const fallbackText = 'For we walk by faith, not by sight.';
				showVerseModal(reference, fallbackText);
			});
		});
	};
	// #endregion

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
