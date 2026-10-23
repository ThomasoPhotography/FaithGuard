(function () {
	// #region *** DOM references                           ***********
	const dom = {
		verseTriggers: null,
		modalContainer: null,
		modalInstance: null,
	};
	// #endregion

	// #region *** Callback-Visualisation - show___         ***********
	const showVerseModal = function (title, content) {
		if (!dom.modalContainer) return;

		// Inject the modal markup into the container
		dom.modalContainer.innerHTML = `
            <div class="modal fade c-modal" id="verseModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered c-modal__dialog">
                    <div class="modal-content c-modal__content">
                        <div class="modal-header c-modal__header">
                            <h5 class="modal-title c-modal__title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body c-modal__body">
                            <p>${content}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

		// Select the newly injected element and initialize Bootstrap modal
		const modalEl = document.getElementById('verseModal');
		if (modalEl && typeof bootstrap !== 'undefined') {
			dom.modalInstance = new bootstrap.Modal(modalEl);
			dom.modalInstance.show();

			// Clean up the DOM when the modal is fully hidden
			modalEl.addEventListener('hidden.bs.modal', () => {
				dom.modalContainer.innerHTML = '';
				dom.modalInstance = null;
			});
		}
	};
	// #endregion

	// #region *** Callback-No Visualisation - callback___  ***********
	// #endregion

	// #region *** Data Access - get___                     ***********
	const getVerseData = async function (reference) {
		try {
			// Calls your L-API endpoint, safely encoding the Scripture reference (e.g., "2 Corinthians 5:7")
			const response = await fetch(`/api/scripture/verse.php?ref=${encodeURIComponent(reference)}`);

			if (!response.ok) {
				throw new Error(`HTTP error! Status: ${response.status}`);
			}

			return await response.json();
		} catch (error) {
			console.error('Failed to fetch Scripture from L-API:', error);
			return null;
		}
	};
	// #endregion

	// #region *** Event Listeners - listenTo___            ***********
	const listenToVerseClicks = function () {
		if (!dom.verseTriggers) return;

		dom.verseTriggers.forEach((trigger) => {
			trigger.addEventListener('click', async function (e) {
				e.preventDefault();
				const reference = this.textContent.trim();

				// Fetch the verse data dynamically
				const data = await getVerseData(reference);

				if (data && data.text) {
					showVerseModal(reference, data.text);
				} else {
					// Fallback if the API fails or returns empty
					showVerseModal(reference, 'Could not load the verse at this time. Please try again later.');
				}
			});
		});
	};
	// #endregion

	// #region *** Init / DOMContentLoaded                  ***********
	const init = function () {
		// Target your existing <div id="modal-container"></div>
		dom.modalContainer = document.getElementById('modal-container');
		dom.verseTriggers = document.querySelectorAll('[data-action="open-verse-modal"]');

		listenToVerseClicks();
	};

	document.addEventListener('DOMContentLoaded', init);
	// #endregion
})();
