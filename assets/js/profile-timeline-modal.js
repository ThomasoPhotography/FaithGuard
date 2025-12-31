// #region ***  DOM references                           ***********
let timelineTrigger = null;
let timelineModalEl = null;
// #endregion


// #region ***  Callback-Visualisation - showTimelineModal ***********
const showTimelineModal = async function () {
    if (!timelineModalEl) {
        buildTimelineModal();
    }

    const modalBody = timelineModalEl.querySelector('.modal-body');
    modalBody.innerHTML =
        '<div class="text-center py-5 text-muted">Loading…</div>';

    try {
        const endpoint = timelineTrigger.dataset.endpoint;
        const response = await fetch(endpoint);

        if (!response.ok) {
            throw new Error('Modal fetch failed');
        }

        modalBody.innerHTML = await response.text();
    } catch (error) {
        modalBody.innerHTML = `
            <div class="alert alert-warning">
                Unable to load detailed history at this time.
            </div>
        `;
    }

    const modalInstance = new bootstrap.Modal(timelineModalEl);
    modalInstance.show();
};
// #endregion


// #region ***  Callback-No Visualisation - buildTimelineModal ***********
const buildTimelineModal = function () {
    const wrapper = document.createElement('div');

    wrapper.innerHTML = `
        <div class="modal fade" id="timelineModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-clock-history me-2"></i>
                            Assessment History
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-muted text-center py-5">
                        Loading…
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(wrapper.firstElementChild);
    timelineModalEl = document.getElementById('timelineModal');
};
// #endregion


// #region ***  Data Access - getTimelineEndpoint         ***********
const getTimelineEndpoint = function () {
    return timelineTrigger?.dataset?.endpoint || null;
};
// #endregion


// #region ***  Event Listeners - listenToTimelineModal   ***********
const listenToTimelineModal = function () {
    if (!timelineTrigger) return;

    timelineTrigger.addEventListener('click', function (event) {
        event.preventDefault();
        showTimelineModal();
    });
};
// #endregion


// #region ***  Init / DOMContentLoaded                  ***********
const initTimelineModal = function () {
    timelineTrigger = document.querySelector('.js-open-timeline-modal');
    if (!timelineTrigger) return;

    listenToTimelineModal();
};

document.addEventListener('DOMContentLoaded', initTimelineModal);
// #endregion
