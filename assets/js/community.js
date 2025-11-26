// #region ***  DOM references                           ***********
const postsList = document.querySelector('.c-posts__list');
const newPostTextarea = document.getElementById('newPost');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showPosts = (posts) => {
	postsList.innerHTML = posts.map((p) => `<div class="card c-card">${p.content}</div>`).join('');
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackPostNew = (data) => {
	if (data.success) getPosts();
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getPosts = () => {
	fetch('api/posts/list.php')
		.then((response) => response.json())
		.then(showPosts);
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToPostNew = () => {
	document.querySelector('button[onclick="postNew()"]').addEventListener('click', () => {
		const content = newPostTextarea.value;
		fetch('api/posts/create.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ content }),
		})
			.then((response) => response.json())
			.then(callbackPostNew);
	});
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initCommunity = () => {
	console.log('Community Loaded');
	getPosts();
	listenToPostNew();
};
document.addEventListener('DOMContentLoaded', initCommunity);
// #endregion
