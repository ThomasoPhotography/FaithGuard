// #region ***  DOM references                           ***********
const inboxDiv = document.querySelector('.c-inbox'); // Assume in a messaging page
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showInbox = (messages) => {
	inboxDiv.innerHTML = messages
		.map((m) => (
			<p>
				${m.content} <button onclick='deleteMessage(${m.id})'>Delete</button>
			</p>
		))
		.join('');
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackSendMessage = (data) => {
	if (data.success) getInbox();
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getInbox = () => {
	fetch('/api/messages/inbox.php')
		.then((response) => response.json())
		.then(showInbox);
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToSend = () => {
	document.querySelector('.c-btn__send').addEventListener('click', () => {
		const receiverId = document.querySelector('#receiverId').value;
		const content = document.querySelector('#messageContent').value;
		fetch('/api/messages/send.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ receiver_id: receiverId, content }) })
			.then((response) => response.json())
			.then(callbackSendMessage);
	});
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initMessaging = () => {
	console.log('Inbox Loaded');
	getInbox();
	listenToSend();
};
document.addEventListener('DOMContentLoaded', initMessaging);
// #endregion
