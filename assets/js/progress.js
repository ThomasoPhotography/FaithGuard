// #region ***  DOM references                           ***********
const progressChart = document.querySelector('.c-progress__chart');
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showProgressChart = (data) => {
	const ctx = progressChart.getContext('2d');
	// Simple bar chart example
	data.forEach((point, i) => {
		ctx.fillRect(i * 50, 200 - point.value, 40, point.value);
	});
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackCheckIn = (data) => {
	if (data.success) getProgress();
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getProgress = () => {
	fetch('/api/progress/get.php')
		.then((response) => response.json())
		.then(showProgressChart);
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToCheckIn = () => {
	document.querySelector('button[onclick="checkIn()"]').addEventListener('click', () => {
		fetch('/api/progress/checkin.php', { method: 'POST' })
			.then((response) => response.json())
			.then(callbackCheckIn);
	});
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initProgress = () => {
	getProgress();
	listenToCheckIn();
};
document.addEventListener('DOMContentLoaded', initProgress);
// #endregion
