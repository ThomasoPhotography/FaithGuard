// #region ***  DOM references                           ***********
let cookieBanner;
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showCookieBanner = () => {
	if (!cookieBanner) {
		cookieBanner = document.createElement('div');
		cookieBanner.id = 'cookie-banner';
		cookieBanner.className = 'alert alert-info alert-dismissible fade show position-fixed bottom-0 start-0 w-100 mb-0 border-0 rounded-0';
		cookieBanner.innerHTML = `
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8 col-12">
                        <p class="mb-0">
                            We use cookies to enhance your experience on FaithGuard. By continuing, you agree to our 
                            <a href="#" target="_blank" class="alert-link">Cookie Policy</a>, 
                            <a href="#" target="_blank" class="alert-link">Privacy Policy</a>, and 
                            <a href="#" target="_blank" class="alert-link">Terms of Service</a>.
                        </p>
                    </div>
                    <div class="col-md-4 col-12 text-md-end mt-2 mt-md-0">
                        <button id="accept-cookies" class="btn c-btn c-btn__dashboard btn-sm me-2">Accept</button>
                        <button id="decline-cookies" class="btn c-btn c-btn__dashboard c-btn__dashboard--outline btn-sm">Decline</button>
                    </div>
                </div>
            </div>
        `;
		document.body.appendChild(cookieBanner);
		listenToCookieButtons();
	}
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackAcceptCookies = () => {
	localStorage.setItem('cookieConsent', 'accepted');
	hideCookieBanner();
};

const callbackDeclineCookies = () => {
	localStorage.setItem('cookieConsent', 'declined');
	hideCookieBanner();
	// Optionally, disable non-essential cookies or features here
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getCookieConsent = () => {
	return localStorage.getItem('cookieConsent');
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToCookieButtons = () => {
	document.getElementById('accept-cookies').addEventListener('click', callbackAcceptCookies);
	document.getElementById('decline-cookies').addEventListener('click', callbackDeclineCookies);
};
// #endregion

// #region ***  Init / DOMContentLoaded                  ***********
const initCookies = function () {
	if (getCookieConsent() !== 'accepted' && getCookieConsent() !== 'declined') {
		showCookieBanner();
	}
};

const hideCookieBanner = () => {
	if (cookieBanner) {
		cookieBanner.remove();
		cookieBanner = null;
	}
};

document.addEventListener('DOMContentLoaded', initCookies);
// #endregion
