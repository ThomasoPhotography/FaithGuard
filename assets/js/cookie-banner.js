// #region ***  DOM references                           ***********
let cookieBanner;
// #endregion

// #region ***  Callback-Visualisation - show___         ***********
const showCookieBanner = () => {
	if (!cookieBanner) {
		const previousFocus = document.activeElement;
		cookieBanner = document.createElement('div');
		cookieBanner.id = 'cookie-banner';
		cookieBanner.className = 'alert alert-info alert-dismissible fade show position-fixed bottom-0 start-0 w-100 mb-0 border-0 rounded-0 c-cookie';
		cookieBanner.setAttribute('role', 'dialog');
		cookieBanner.setAttribute('aria-label', 'Cookie consent');
		cookieBanner.setAttribute('tabindex', '-1');
		cookieBanner.innerHTML = `
			<div class="container-fluid">
				<div class="row align-items-center">
					<div class="col-md-8 col-12">
						<div class="c-cookie__header d-flex align-items-center mb-2">
						<img src="/assets/uploads/FaithGuard_Primary_Logo.svg" alt="Cookie Icon" class="c-cookie__icon">
						<h4 class="c-cookie__title mb-0">FaithGuard</h4>
						</div>
						<p class="mb-0" aria-live="polite">
							We use cookies to enhance your experience on FaithGuard. By continuing, you agree to our
							<a href="/policies.php?slug=cookie" class="c-cookie__link">Cookie Policy</a>,
							<a href="/policies.php?slug=privacy" class="c-cookie__link">Privacy Policy</a>, and
							<a href="/policies.php?slug=terms" class="c-cookie__link">Terms of Service</a>.
						</p>
					</div>
					<div class="col-md-4 col-12 text-md-end mt-2 mt-md-0">
						<button id="accept-cookies" class="btn c-btn c-cookie__btn btn-sm me-2">Accept</button>
						<button id="decline-cookies" class="btn c-btn c-cookie__btn c-cookie__btn--outline btn-sm">Decline</button>
					</div>
				</div>
			</div>
		`;
		document.body.appendChild(cookieBanner);
		// Focus management
		const panel = cookieBanner;
		panel.focus();
		// Keyboard handlers: Escape to close, trap Tab on buttons
		const acceptBtn = document.getElementById('accept-cookies');
		const declineBtn = document.getElementById('decline-cookies');
		const trapFocus = (e) => {
			if (e.key === 'Tab') {
				const focusable = [...panel.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')];
				if (focusable.length === 0) return;
				const idx = focusable.indexOf(document.activeElement);
				if (e.shiftKey) {
					// move backwards
					const next = idx <= 0 ? focusable[focusable.length - 1] : focusable[idx - 1];
					e.preventDefault();
					next.focus();
				} else {
					const next = idx === focusable.length - 1 ? focusable[0] : focusable[idx + 1];
					e.preventDefault();
					next.focus();
				}
			} else if (e.key === 'Escape') {
				hideCookieBanner();
				previousFocus?.focus?.();
			}
		};
		panel.addEventListener('keydown', trapFocus);
		// store previous focus for restore
		cookieBanner._previousFocus = previousFocus;
		listenToCookieButtons();
	}
};
// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
const callbackAcceptCookies = () => {
	try {
		localStorage.setItem('cookieConsent', 'accepted');
	} catch (err) {
		console.warn('localStorage unavailable, falling back to cookie:', err);
		document.cookie = 'cookieConsent=accepted; path=/; max-age=' + 60 * 60 * 24 * 365;
	}
	hideCookieBanner();
};

const callbackDeclineCookies = () => {
	try {
		localStorage.setItem('cookieConsent', 'declined');
	} catch (err) {
		console.warn('localStorage unavailable, falling back to cookie:', err);
		document.cookie = 'cookieConsent=declined; path=/; max-age=' + 60 * 60 * 24 * 365;
	}
	hideCookieBanner();
	// Optionally, disable non-essential cookies or features here
};
// #endregion

// #region ***  Data Access - get___                     ***********
const getCookieConsent = () => {
	try {
		return localStorage.getItem('cookieConsent');
	} catch (err) {
		// fallback to cookie
		const m = document.cookie.match(/(?:^|; )cookieConsent=([^;]+)/);
		return m ? decodeURIComponent(m[1]) : null;
	}
};
// #endregion

// #region ***  Event Listeners - listenTo___            ***********
const listenToCookieButtons = () => {
	const accept = document.getElementById('accept-cookies');
	const decline = document.getElementById('decline-cookies');
	if (accept) accept.addEventListener('click', callbackAcceptCookies);
	if (decline) decline.addEventListener('click', callbackDeclineCookies);
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
