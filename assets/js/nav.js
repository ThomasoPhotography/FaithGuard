// #region ***  DOM references                           ***********
const mobileNav = document.querySelector('.js-nav-mobile');
const openBtn = document.querySelector('[data-action="open-menu"]');
const closeBtn = document.querySelector('[data-action="close-menu"]');
// #endregion


// #region ***  Callback-Visualisation - show___         ***********
const showNav = function() {
    mobileNav?.classList.add('active');
    document.body.style.overflow = 'hidden';
};

const hideNav = function() {
    mobileNav?.classList.remove('active');
    document.body.style.overflow = '';
};
// #endregion


// #region ***  Callback-No Visualisation - callback___  ***********
// none
// #endregion


// #region ***  Data Access - get___                     ***********
// none
// #endregion


// #region ***  Event Listeners - listenTo___            ***********
const listenToNav = function() {

    openBtn?.addEventListener('click', showNav);
    closeBtn?.addEventListener('click', hideNav);

    mobileNav?.addEventListener('click', function(e) {
        if (e.target === mobileNav) hideNav();
    });
};
// #endregion


// #region ***  Init / DOMContentLoaded                  ***********
const initNav = function(){
    listenToNav();
};
document.addEventListener('DOMContentLoaded', initNav);
// #endregion
