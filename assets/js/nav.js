// #region ***  DOM references                           ***********
const navPlaceholder = document.querySelector('.c-nav .c-nav--placeholder');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showNav = () => {
	fetch('templates/nav.html')
		.then((response) => response.text())
		.then((data) => (navPlaceholder.innerHTML = data));
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
// #endregion

// #region ***  Data Access - get___                     ***********
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToNavToggles = () => {
	// Added when I want customised
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const init = function () {
	console.log('Page Loaded');
	showNav();
	listenToNavToggles();
};

document.addEventListener('DOMContentLoaded', init);
// #endregion
