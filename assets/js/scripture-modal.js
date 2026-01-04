// #region *** DOM references ***
let scriptureTrigger = null;
let scriptureModalEl = null;
let translationSelect = null;
// #endregion

// #region *** Show Scripture Modal ***
const showScriptureModal = async function () {
	if (!scriptureModalEl) {
		buildScriptureModal();
	}

	const modalBody = scriptureModalEl.querySelector('.modal-body');
	modalBody.innerHTML = '<div class="text-center py-5 text-muted">Loading Scripture…</div>';

	await loadScripture();

	const modalInstance = new bootstrap.Modal(scriptureModalEl);
	modalInstance.show();
};
// #endregion

// #region *** Load Scripture ***
const loadScripture = async function () {
	const endpoint = scriptureTrigger.dataset.endpoint;
	const verse = scriptureTrigger.dataset.verse;
	const bibleId = translationSelect.value;
	const translation = translationSelect.selectedOptions[0].dataset.translation;

	try {
		const response = await fetch(`${endpoint}?verse=${verse}&bibleId=${bibleId}&translation=${translation}`);

		if (!response.ok) throw new Error();

		scriptureModalEl.querySelector('.modal-body').innerHTML = await response.text();
	} catch {
		scriptureModalEl.querySelector('.modal-body').innerHTML = `
            <div class="alert alert-warning">
                Unable to load Scripture at this time.
            </div>
        `;
	}
};
// #endregion

// #region *** Build Scripture Modal ***
const buildScriptureModal = function () {
    const siteLang = scriptureTrigger.dataset.siteLang || 'en';
    const translations = siteLang === 'nl' ? [
        { id: '7b0e2b6e4c0a1d2b-01', label: 'NBV21' },
        { id: 'de4e12af7f28f599-02', label: 'NRSVUE' }
    ] : [
        { id: 'de4e12af7f28f599-02', label: 'NRSVUE' },
        { id: '7b0e2b6e4c0a1d2b-01', label: 'NBV21' }
    ];
    const optionsHtml = translations.map(t =>`<option value="${t.id}" data-translation="${t.label}">${t.label}</option>`).join('');
	const wrapper = document.createElement('div');

	wrapper.innerHTML = `
        <div class="modal fade" id="scriptureModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-book me-2"></i>
                            Holy Scripture
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-muted text-center py-5">
                        Loading…
                    </div>

                    <div class="modal-footer justify-content-between">
                        <span class="small text-muted">Select translation</span>
                        <select class="form-select w-auto js-scripture-translation">
                            ${optionsHtml}
                        </select>
                    </div>
                </div>
            </div>
        </div>
    `;

	document.body.appendChild(wrapper.firstElementChild);

	scriptureModalEl = document.getElementById('scriptureModal');
	translationSelect = scriptureModalEl.querySelector('.js-scripture-translation');

	translationSelect.addEventListener('change', loadScripture);
};
// #endregion

// #region *** Init ***
const initScriptureModal = function () {
	scriptureTrigger = document.querySelector('.js-open-scripture-modal');
	if (!scriptureTrigger) return;

	scriptureTrigger.addEventListener('click', function (e) {
		e.preventDefault();
		showScriptureModal();
	});
};

document.addEventListener('DOMContentLoaded', initScriptureModal);
// #endregion
