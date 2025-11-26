// #region ***  DOM references                           ***********
const inboxDiv = document.querySelector('.c-inbox'); // Assume in a messaging page
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showInbox = (messages) => {
    inboxDiv.innerHTML = messages.map(m => `<p>${m.content}</p>`).join('');
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackSendMessage = (data) => {
    if (data.success) getInbox();
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getInbox = () => {
    fetch('api/messages/inbox.php')
        .then(response => response.json())
        .then(showInbox);
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToSend = () => {
    // Assume a send button
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