// #region ***  DOM references                           ***********
let pastoralTrigger = null;
let pastoralModalEl = null;
// #endregion


// #region ***  Callback-Visualisation - showPastoralModal ***********
const showPastoralModal = async function () {
    if (!pastoralModalEl) {
        buildPastoralModal();
    }

    const modalBody = pastoralModalEl.querySelector('.modal-body');
    modalBody.innerHTML =
        '<div class="text-center py-5 text-muted">Loading pastoral insights…</div>';

    try {
        const endpoint = pastoralTrigger.dataset.endpoint;
        const response = await fetch(endpoint);

        if (!response.ok) {
            throw new Error('Modal fetch failed');
        }

        modalBody.innerHTML = await response.text();
    } catch (error) {
        modalBody.innerHTML = `
            <div class="alert alert-warning">
                Unable to load pastoral insights at this time.
            </div>
        `;
    }

    const modalInstance = new bootstrap.Modal(pastoralModalEl);
    modalInstance.show();
};
// #endregion


// #region ***  Callback-No Visualisation - buildPastoralModal ***********
const buildPastoralModal = function () {
    const wrapper = document.createElement('div');

    wrapper.innerHTML = `
        <div class="modal fade" id="pastoralSummaryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content c-pastoralModal">
                    <div class="modal-header c-pastoralModal__header">
                        <h5 class="modal-title c-pastoralModal__title">
                            <i class="bi bi-journal-heart me-2"></i>
                            Pastoral Insight Summary
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body c-pastoralModal__body text-muted text-center py-5">
                        Loading pastoral insights…
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(wrapper.firstElementChild);
    pastoralModalEl = document.getElementById('pastoralSummaryModal');
};
// #endregion


// #region ***  Event Listeners - listenToPastoralModal   ***********
const listenToPastoralModal = function () {
    if (!pastoralTrigger) return;

    pastoralTrigger.addEventListener('click', function (event) {
        event.preventDefault();
        showPastoralModal();
    });
};
// #endregion


// #region ***  Init / DOMContentLoaded                  ***********
const initPastoralModal = function () {
    pastoralTrigger = document.querySelector('.js-open-pastoral-modal');
    if (!pastoralTrigger) return;

    listenToPastoralModal();
};

document.addEventListener('DOMContentLoaded', initPastoralModal);
// #endregion
