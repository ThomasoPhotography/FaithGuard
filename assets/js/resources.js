// #region ***  DOM references                           ***********
const resourcesList = document.querySelector('.c-resources__list');
const searchInput = document.querySelector('.c-resources__search');
const filterSelect = document.querySelector('.c-resources__filter');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showResources = (resources) => {
	// Check if the container exists
	if (!resourcesList) {
		console.error('Resource list container not found.');
		return;
	}

	// Render resources as cards
	resourcesList.innerHTML = resources
		.map(
			(r) => `
				<div class="card c-card mb-3">
					<div class="card-body">
						<h5 class="card-title">${r.title}</h5>
						<p class="card-text">${r.content}</p>
						<span class="badge c-card__badge c-card__badge--tag">${r.tags}</span>
					</div>
				</div>
			`
		)
		.join('');
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
// #endregion

// #region ***  Data Access - get___                     ***********
const getResources = (query = '', tag = '') => {
	const url = `/api/resources/list.php?search=${encodeURIComponent(query)}&tag=${encodeURIComponent(tag)}`;
	fetch(url)
		.then((response) => {
			if (!response.ok) {
				throw new Error('API failed with status: ' + response.status);
			}
			return response.json();
		})
		.then(showResources)
		.catch((error) => {
			console.error('Error fetching resources:', error);
			if (resourcesList) {
				resourcesList.innerHTML = '<p class="text-danger">Failed to load resources. Check console for details.</p>';
			}
		});
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToSearch = () => {
	// Added null checks for safety since elements might not exist on all pages
	if (searchInput) {
		searchInput.addEventListener('input', () => getResources(searchInput.value, filterSelect ? filterSelect.value : ''));
	}
	if (filterSelect) {
		filterSelect.addEventListener('change', () => getResources(searchInput ? searchInput.value : '', filterSelect.value));
	}
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initResources = () => {
	if (resourcesList) {
		getResources();
		listenToSearch();
	}
};
document.addEventListener('DOMContentLoaded', initResources);
// #endregion
