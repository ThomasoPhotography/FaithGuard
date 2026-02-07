// #region ***  DOM references                           ***********
// #endregion


// #region ***  Callback-Visualisation - show___         ***********
const showAlert = function(container, message, type = 'error') {
    if (!container) return;
    container.innerHTML = `<div class="alert alert--${type}">${message}</div>`;
};
// #endregion


// #region ***  Callback-No Visualisation - callback___  ***********
const debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

const escapeHtml = function(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
};
// #endregion


// #region ***  Data Access - get___                     ***********
// none
// #endregion


// #region ***  Event Listeners - listenTo___            ***********
// none
// #endregion


// #region ***  Init / DOMContentLoaded                  ***********
const initUtils = function(){};
document.addEventListener('DOMContentLoaded', initUtils);
// #endregion
