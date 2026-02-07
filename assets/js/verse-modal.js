// #region ***  DOM references                           ***********
const verseModal = document.querySelector('.js-verse-modal');
const verseContent = document.querySelector('.js-verse-content');
const heroVerse = document.querySelector('.js-hero-verse');
// #endregion


// #region ***  Callback-Visualisation - show___         ***********
const showLoading = function() {
    verseContent.innerHTML = `
        <div class="verse-modal__loading">
            <span class="spinner"></span>
        </div>
    `;
};

const showVerse = function(data) {
    if (!data.success || !data.verse) {
        verseContent.innerHTML = `
            <div class="verse-modal__error">
                <p>Unable to load verse.</p>
            </div>
        `;
        return;
    }

    verseContent.innerHTML = `
        <div class="verse-modal__content">
            <div class="verse-modal__reference">${escapeHtml(data.verse.reference)}</div>
            <p class="verse-modal__text">"${escapeHtml(data.verse.text)}"</p>
            <div class="verse-modal__version">${escapeHtml(data.verse.version)}</div>
        </div>
    `;
};

const openModal = function() {
    verseModal?.classList.add('active');
    document.body.style.overflow = 'hidden';
};

const closeModal = function() {
    verseModal?.classList.remove('active');
    document.body.style.overflow = '';
};
// #endregion


// #region ***  Callback-No Visualisation - callback___  ***********
const updateLanguageButtons = function(lang) {
    document.querySelectorAll('.verse-modal__lang-btn')
        .forEach(btn => {
            btn.classList.toggle('active', btn.dataset.lang === lang);
        });
};
// #endregion


// #region ***  Data Access - get___                     ***********
const getVerse = async function(reference, lang) {
    const url = `/api/helper/bible.php?reference=${encodeURIComponent(reference)}&lang=${lang}`;
    const response = await fetch(url);
    return await response.json();
};
// #endregion


// #region ***  Event Listeners - listenTo___            ***********
const listenToVerseModal = function() {

    heroVerse?.addEventListener('click', async function() {
        const reference = heroVerse.dataset.reference || '2CO.5.7';
        openModal();
        showLoading();
        const data = await getVerse(reference, 'en');
        showVerse(data);
    });

    document.querySelector('[data-action="close-verse-modal"]')
        ?.addEventListener('click', closeModal);

    verseModal?.addEventListener('click', function(e) {
        if (e.target === verseModal) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && verseModal?.classList.contains('active')) {
            closeModal();
        }
    });

    document.querySelectorAll('.verse-modal__lang-btn')
        .forEach(btn => {
            btn.addEventListener('click', async function() {
                const { lang } = btn.dataset;
                updateLanguageButtons(lang);

                const reference = heroVerse?.dataset.reference || '2CO.5.7';
                showLoading();
                const data = await getVerse(reference, lang);
                showVerse(data);
            });
        });
};
// #endregion


// #region ***  Init / DOMContentLoaded                  ***********
const initModal = function(){
    listenToVerseModal();
};
document.addEventListener('DOMContentLoaded', initModal);
// #endregion
