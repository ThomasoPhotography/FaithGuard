// #region ***  DOM references                           ***********
const journalForm = document.getElementById('journalForm');
const journalContent = document.getElementById('journalContent');
const relCheckbox = document.getElementById('isAddictionRelated');
const selectionDiv = document.getElementById('addictionTypeSelection');
const encouragementModalEl = document.getElementById('encouragementModal');
const verseTextEl = document.getElementById('verseText');
const verseRefEl = document.getElementById('verseRef');
let encouragementModal = null;
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showEncouragement = (scripture) => {
	if (!encouragementModal || !scripture) return;
	if (verseTextEl) verseTextEl.textContent = `"${scripture.text}"`;
	if (verseRefEl) verseRefEl.textContent = scripture.verse;
	encouragementModal.show();
};
const showAddictionSelection = (isVisible) => {
	if (selectionDiv) {
		selectionDiv.style.display = isVisible ? 'block' : 'none';
	}
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackResetForm = () => {
	if (journalForm) {
		journalForm.reset();
		showAddictionSelection(false);
	}
};
// #endregion

// #region ***  Data Access - get___                     ***********
const postJournalEntry = async (payload) => {
	const response = await fetch('../../api/journal/create.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(payload),
	});
	if (!response.ok) {
		throw new Error('Server responded with an error');
	}
	return await response.json();
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToAddictionToggle = () => {
	if (relCheckbox) {
		relCheckbox.addEventListener('change', (e) => {
			showAddictionSelection(e.target.checked);
		});
	}
};
const listenToJournalSubmit = () => {
	if (!journalForm) return;
	journalForm.addEventListener('submit', async (e) => {
		e.preventDefault();
		const isRelated = relCheckbox ? relCheckbox.checked : false;
		const selectedTypes = [];
		const checkboxes = journalForm.querySelectorAll('input[name="addiction_types[]"]:checked');
		checkboxes.forEach((cb) => selectedTypes.push(cb.value));
		const payload = {
			content: journalContent.value,
			isRelated: isRelated,
			addictionTypes: selectedTypes,
		};
		try {
			const result = await postJournalEntry(payload);
			if (result.success) {
				callbackResetForm();
				if (isRelated && result.scripture) {
					showEncouragement(result.scripture);
				} else {
					console.log('Journal entry saved successfully.');
				}
			} else {
				alert('Error: ' + (result.error || 'Failed to save entry.'));
			}
		} catch (err) {
			console.error('Journal submission error:', err);
			alert('A network error occurred. Please try again.');
		}
	});
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initJournal = () => {
	if (encouragementModalEl && typeof bootstrap !== 'undefined') {
		encouragementModal = new bootstrap.Modal(encouragementModalEl);
	}
	listenToAddictionToggle();
	listenToJournalSubmit();
};
// #endregion
