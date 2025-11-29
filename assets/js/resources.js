// #region ***  DOM references                           ***********
const resourcesList = document.querySelector('.c-resources__list');
const searchInput = document.querySelector('.c-resources__search');
const filterSelect = document.querySelector('.c-resources__filter');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showResources = (resources) => {
	resourcesList.innerHTML = resources
		.map((r) => (
			<div class='card c-card'>
				<h5>${r.title}</h5>
				<p>${r.content}</p>
			</div>
		))
		.join('');
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
// #endregion

// #region ***  Data Access - get___                     ***********
const getResources = (query = '', tag = '') => {
	const url = `/api/resources/list.php?search=${encodeURIComponent(query)}&tag=${encodeURIComponent(tag)}`;
	fetch(url)
		.then((response) => response.json())
		.then(showResources);
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToSearch = () => {
	searchInput.addEventListener('input', () => getResources(searchInput.value, filterSelect.value));
	filterSelect.addEventListener('change', () => getResources(searchInput.value, filterSelect.value));
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initResources = () => {
	getResources();
	listenToSearch();
};
document.addEventListener('DOMContentLoaded', initResources);
// #endregion
