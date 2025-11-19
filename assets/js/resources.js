/**
 * FaithGuard - Resources Page JavaScript
 * Version: 0.0.5
 * Release: 2025-11-19
 */

// #region ***  DOM references                           ***********
let categoryFilter;
let typeFilter;
let difficultyFilter;
let resetFiltersBtn;
let resourceCountSpan;
let resourcesGrid;
let noResultsDiv;
let searchInput;
// #endregion

// #region ***  Data - Resources Database                ***********
const resourcesData = [
	{
		id: 1,
		title: 'Devotional: Overcoming Temptation',
		description: 'Daily readings with Bible verses on purity and strength. A 30-day journey to spiritual renewal.',
		category: 'devotional',
		type: 'guide',
		difficulty: 'beginner',
		tags: ['daily', 'scripture', 'purity'],
	},
	{
		id: 2,
		title: 'Prayer Guide for Accountability',
		description: 'Sample prayers for forgiveness and spiritual warfare. Learn to pray with power and purpose.',
		category: 'prayer',
		type: 'guide',
		difficulty: 'intermediate',
		tags: ['prayer', 'forgiveness', 'warfare'],
	},
	{
		id: 3,
		title: 'Bible Verses on Purity',
		description: "Curated scriptures for encouragement and reflection. Memorize and meditate on God's Word.",
		category: 'scripture',
		type: 'article',
		difficulty: 'beginner',
		tags: ['scripture', 'purity', 'meditation'],
	},
	{
		id: 4,
		title: 'Accountability Partner Tips',
		description: 'Guidance on building supportive relationships for recovery. Find and maintain accountability.',
		category: 'accountability',
		type: 'guide',
		difficulty: 'intermediate',
		tags: ['accountability', 'relationships', 'support'],
	},
	{
		id: 5,
		title: 'Digital Detox Strategies',
		description: 'Practical steps to reduce screen time and focus on faith. Reclaim your time and attention.',
		category: 'digital-detox',
		type: 'tool',
		difficulty: 'beginner',
		tags: ['digital', 'detox', 'practical'],
	},
	{
		id: 6,
		title: 'Testimonies of Hope',
		description: 'Stories of redemption and strength from the community. Be inspired by real transformation.',
		category: 'testimony',
		type: 'article',
		difficulty: 'beginner',
		tags: ['testimony', 'hope', 'redemption'],
	},
	{
		id: 7,
		title: 'Advanced Prayer Techniques',
		description: 'Deep dive into intercessory prayer and spiritual warfare. For those seeking deeper prayer life.',
		category: 'prayer',
		type: 'video',
		difficulty: 'advanced',
		tags: ['prayer', 'intercession', 'advanced'],
	},
	{
		id: 8,
		title: 'Scripture Memory System',
		description: "Proven methods to memorize and retain Bible verses. Build a strong foundation in God's Word.",
		category: 'scripture',
		type: 'tool',
		difficulty: 'intermediate',
		tags: ['scripture', 'memory', 'system'],
	},
	{
		id: 9,
		title: 'Breaking Free: A 90-Day Journey',
		description: 'Comprehensive devotional program for lasting freedom. Structured path to transformation.',
		category: 'devotional',
		type: 'guide',
		difficulty: 'advanced',
		tags: ['devotional', 'journey', 'freedom'],
	},
	{
		id: 10,
		title: 'Accountability Group Starter Kit',
		description: 'Everything you need to start and lead an accountability group. Includes discussion guides.',
		category: 'accountability',
		type: 'tool',
		difficulty: 'intermediate',
		tags: ['accountability', 'group', 'leadership'],
	},
	{
		id: 11,
		title: 'Digital Boundaries Workbook',
		description: 'Interactive workbook to establish healthy digital habits. Set and maintain boundaries.',
		category: 'digital-detox',
		type: 'guide',
		difficulty: 'beginner',
		tags: ['digital', 'boundaries', 'workbook'],
	},
	{
		id: 12,
		title: 'Victory Stories Podcast',
		description: 'Weekly audio testimonies of transformation and hope. Listen to real stories of freedom.',
		category: 'testimony',
		type: 'audio',
		difficulty: 'beginner',
		tags: ['testimony', 'podcast', 'audio'],
	},
	{
		id: 13,
		title: 'Morning Prayer Routine',
		description: 'Start your day with powerful prayers and declarations. Establish a consistent prayer habit.',
		category: 'prayer',
		type: 'article',
		difficulty: 'beginner',
		tags: ['prayer', 'morning', 'routine'],
	},
	{
		id: 14,
		title: 'Purity Verses Video Series',
		description: 'Visual study of key Bible passages on purity. Engaging video teachings and applications.',
		category: 'scripture',
		type: 'video',
		difficulty: 'intermediate',
		tags: ['scripture', 'video', 'purity'],
	},
	{
		id: 15,
		title: 'Tech-Free Challenge',
		description: '30-day challenge to reduce digital dependency. Includes daily activities and reflections.',
		category: 'digital-detox',
		type: 'tool',
		difficulty: 'advanced',
		tags: ['digital', 'challenge', '30-day'],
	},
];
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showResources = (resources) => {
	resourcesGrid.innerHTML = '';

	if (resources.length === 0) {
		noResultsDiv.classList.remove('d-none');
		resourceCountSpan.textContent = 'No resources found';
		return;
	}

	noResultsDiv.classList.add('d-none');
	resourceCountSpan.textContent = `Showing ${resources.length} resource${resources.length !== 1 ? 's' : ''}`;

	resources.forEach((resource) => {
		const card = createResourceCard(resource);
		resourcesGrid.appendChild(card);
	});
};

const createResourceCard = (resource) => {
	const col = document.createElement('div');
	col.className = 'col-md-6 col-lg-4 mb-4';

	const difficultyBadge = getDifficultyBadge(resource.difficulty);
	const typeIcon = getTypeIcon(resource.type);

	col.innerHTML = `
        <div class="card c-card c-resource-card" data-resource-id="${resource.id}">
            <div class="card-body c-card__body">
                <div class="c-resource-card__header">
                    <span class="c-resource-card__type">
                        <i class="bi ${typeIcon}"></i> ${capitalizeFirst(resource.type)}
                    </span>
                    <span class="badge c-card__badge c-card__badge--${difficultyBadge.text}">${difficultyBadge.text}</span>
                </div>
                <h5 class="card-title c-card__title">${resource.title}</h5>
                <p class="card-text c-card__text">${resource.description}</p>
                <div class="c-resource-card__tags">
                    ${resource.tags.map((tag) => `<span class="c-resource-card__tag">#${tag}</span>`).join('')}
                </div>
                <button class="btn c-btn c-card__btn mt-3">Access Resource</button>
            </div>
        </div>
    `;

	return col;
};

const getDifficultyBadge = (difficulty) => {
	const badges = {
		beginner: { class: 'bg-success', text: 'Beginner' },
		intermediate: { class: 'bg-warning', text: 'Intermediate' },
		advanced: { class: 'bg-danger', text: 'Advanced' },
	};
	return badges[difficulty] || badges.beginner;
};

const getTypeIcon = (type) => {
	const icons = {
		article: 'bi-file-text',
		guide: 'bi-book',
		video: 'bi-play-circle',
		audio: 'bi-headphones',
		tool: 'bi-tools',
	};
	return icons[type] || 'bi-file-text';
};

const capitalizeFirst = (str) => {
	return str.charAt(0).toUpperCase() + str.slice(1);
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackFilterChange = () => {
	const filteredResources = getFilteredResources();
	showResources(filteredResources);
};

const callbackResetFilters = () => {
	categoryFilter.value = 'all';
	typeFilter.value = 'all';
	difficultyFilter.value = 'all';
	if (searchInput) {
		searchInput.value = '';
	}
	showResources(resourcesData);
};

const callbackResourceClick = (e) => {
	const card = e.target.closest('.c-resource-card');
	if (card) {
		const resourceId = parseInt(card.dataset.resourceId);
		const resource = resourcesData.find((r) => r.id === resourceId);
		if (resource) {
			alert(`Accessing: ${resource.title}\n\nThis would open the full resource in a production environment.`);
		}
	}
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getFilteredResources = () => {
	const category = categoryFilter.value;
	const type = typeFilter.value;
	const difficulty = difficultyFilter.value;
	const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

	return resourcesData.filter((resource) => {
		const matchesCategory = category === 'all' || resource.category === category;
		const matchesType = type === 'all' || resource.type === type;
		const matchesDifficulty = difficulty === 'all' || resource.difficulty === difficulty;

		const matchesSearch = !searchTerm || resource.title.toLowerCase().includes(searchTerm) || resource.description.toLowerCase().includes(searchTerm) || resource.tags.some((tag) => tag.toLowerCase().includes(searchTerm));

		return matchesCategory && matchesType && matchesDifficulty && matchesSearch;
	});
};

const getSearchTermFromSession = () => {
	const searchTerm = sessionStorage.getItem('faithguard_search');
	if (searchTerm) {
		sessionStorage.removeItem('faithguard_search');
		return searchTerm;
	}
	return '';
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToFilters = () => {
	categoryFilter.addEventListener('change', callbackFilterChange);
	typeFilter.addEventListener('change', callbackFilterChange);
	difficultyFilter.addEventListener('change', callbackFilterChange);
};

const listenToResetButton = () => {
	resetFiltersBtn.addEventListener('click', callbackResetFilters);
};

const listenToResourceCards = () => {
	resourcesGrid.addEventListener('click', callbackResourceClick);
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initResources = () => {
	// Initialize DOM references
	categoryFilter = document.getElementById('categoryFilter');
	typeFilter = document.getElementById('typeFilter');
	difficultyFilter = document.getElementById('difficultyFilter');
	resetFiltersBtn = document.getElementById('resetFilters');
	resourceCountSpan = document.getElementById('resourceCount');
	resourcesGrid = document.getElementById('resourcesGrid');
	noResultsDiv = document.getElementById('noResults');
	searchInput = document.querySelector('.js-search-form');

	// Check for search term from navigation
	const searchTerm = getSearchTermFromSession();
	if (searchTerm && searchInput) {
		searchInput.value = searchTerm;
	}

	// Setup event listeners
	listenToFilters();
	listenToResetButton();
	listenToResourceCards();

	// Initial display
	const initialResources = searchTerm ? getFilteredResources() : resourcesData;
	showResources(initialResources);

	console.log('%cResources Page Loaded', 'color: #4a90e2; font-size: 16px; font-weight: bold;');
	console.log(`Total resources: ${resourcesData.length}`);
};

document.addEventListener('DOMContentLoaded', initResources);
// #endregion
