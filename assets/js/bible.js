// #region ***  DOM references                           ***********
const bibleRef = document.querySelector('.js-bible__ref');
const bibleModal = document.getElementById('bible-modal');
const languageSelect = document.getElementById('language-select');
const fetchVerseBtn = document.getElementById('fetch-verse');
const verseDisplay = document.getElementById('verse-display');
const closeModalBtn = document.getElementById('close-modal');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
function showModal() {
    bibleModal.style.display = 'block';
}

function hideModal() {
    bibleModal.style.display = 'none';
    verseDisplay.textContent = ''; // Clear on close
}
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
function callbackFetchVerse() {
    const language = languageSelect.value;
    getBibleVerse(language).then(verse => {
        verseDisplay.textContent = verse;
    }).catch(err => {
        verseDisplay.textContent = 'Error fetching verse.';
    });
}
// #endregion

// #region ***  Data Access - get___                     ***********
async function getBibleVerse(language) {
    const response = await fetch(`/api/resources/bible?book=2%20Corinthians&chapter=5&verse=7&language=${language}`);
    const data = await response.json();
    if (data.error) throw new Error(data.error);
    return data.verse;
}
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
function listenToBibleRef() {
    bibleRef.addEventListener('click', showModal);
}

function listenToFetchVerse() {
    fetchVerseBtn.addEventListener('click', callbackFetchVerse);
}

function listenToCloseModal() {
    closeModalBtn.addEventListener('click', hideModal);
}
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initBible = function(){
    listenToBibleRef();
    listenToFetchVerse();
    listenToCloseModal();
}

document.addEventListener('DOMContentLoaded', initBible);
// #endregion