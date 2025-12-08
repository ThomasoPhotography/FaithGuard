// #region ***  DOM references                           ***********
const postsList = document.querySelector('.c-posts__list');
const newPostTextarea = document.getElementById('newPost');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showPosts = (posts) => {
	postsList.innerHTML = posts.map((p) => `<div class="card c-card"><div class="c-card__body"><h5 class="c-card__title">${p.content}</h5><button onclick="replyToPost(${p.id})">Reply</button><button onclick="reportPost(${p.id})">Report</button></div></div>`).join('');
};

const reportPost = async (postId) => {
	// Prompt user for reason (optional, but improves UX)
	const reason = prompt('Please provide a reason for reporting this post (optional):') || 'No reason provided';
	
	const response = await fetch('/api/posts/report.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams({ post_id: postId, reason: reason }),
	});
	const result = await response.json();
	if (result.success) {
		alert('Report submitted!');
	} else {
		alert(result.error);
	}
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackPostNew = (data) => {
	if (data.success) getPosts();
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getPosts = () => {
	fetch('/api/posts/list.php')
		.then((response) => response.json())
		.then(showPosts);
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToPostNew = () => {
	document.querySelector('button[onclick="postNew()"]').addEventListener('click', () => {
		const content = newPostTextarea.value;
		fetch('/api/posts/create.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ content }) })
			.then((response) => response.json())
			.then(callbackPostNew);
	});
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initPosts = () => {
	getPosts();
	listenToPostNew();
};
document.addEventListener('DOMContentLoaded', initPosts);
// #endregion
