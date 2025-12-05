// #region ***  DOM references                           ***********
const footerPlaceholder = document.querySelector('.c-footer--placeholder');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showFooter = () => {
	if (!footerPlaceholder) {
		console.error('c-footer--placeholder element not found in DOM');
		return;
	}
	fetch('/templates/footer.html') // Note: Use relative path if running from root; adjust if needed (e.g., '/templates/nav.html' for absolute)
		.then((response) => {
			if (!response.ok) {
				throw new Error('Failed to load footer.html: ' + response.status);
			}
			return response.text();
		})
		.then((data) => {
			footerPlaceholder.innerHTML = data;
		})
		.catch((error) => {
			console.error('Error loading footer:', error);
		});
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
// #endregion

// #region ***  Data Access - get___                     ***********
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToFooterToggles = () => {
	// Added when I want customised
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initFooter = function () {
	console.log('Page Loaded');
	showFooter();
	listenToFooterToggles();
};

document.addEventListener('DOMContentLoaded', initFooter);
// #endregion
