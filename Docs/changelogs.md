# Changelog

All notable changes to this project will be documented in this file.
## [0.1.2] - Current

### Added
- N/A

### Changed
- Update require statements to use relative paths for database connections.
- Correct require statement path in `login.php` for database connection.

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A


## [0.1.1] - 2025-11-28

### Added
- Progress files on path `/api/progress`: `checkin.php`, `get.php` and `export.php`.
- One quiz file on path `/api/quiz`: `submit.php`.
- Index template on path `/templates/index.html`.
- Nav template on path `/templates/nav.html`.
- Community template on path `/templates/community.html`.
- Progress template on path `/templates/progress.html`.
- Resources template on path `/templates/resources.html`.
- Resources Javascript on path `/assets/js/resources.js`.
- Quiz template on path: `/templates/quiz.html`.
- Admin Templates on path: `/templates/admin`.
- Footer Template and Footer function on `/templates/footer.html` and `/assets/js/footer.js`.

### Changed
- File path in file with path: `/database/FaithGuardRepository.php`, changes made on line 2.
- DOM preferences in file with path: `/assets/js/nav.js`.
- Class name in `progress.html`.
- Database schema and SQL file minor changes.
- Turned the main `index.html` into `index.php`.
- Update require_once paths in `index.php`.
- Comment out `debug.php` helper in `index.php`.
- Update database connection for hosting.
- Update database name in `database.php` for correct connection.
- Correct path formatting for require_once statements in `index.php`.
- Update require statements to use absolute paths for database connections.
- Correct require statement path in login.php for database connection.

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- Favicon path to `<link rel="icon" href="/assets/uploads/favicon.ico" type="image/x-icon">` in all templates.

### Security
- N/A

## [0.1.0] - 2025-11-26

### Added
- Four JavaScript files: `nav.js`, `quiz.js`, `progress.js`, `community.js`.
- Database Folder.
- Template folder with admin folder inside.
- Six template HTML files inside the `template` folder: `community.html`, `index.html`, `nav.html`, `progress.html`, `quiz.html` and `resources.html`.
- Three template HTML files in the following path `/templates/admin`: `legal.html`, `manage-resources.html` and `moderation.html`.
- Database files inside the folder with path `/database`: `database.php` and `FaithGuardRepository.php`.
- Users file on path `/api/users`: `profile.php`.
- Auth files on path `/api/auth`: `register.php`, `login.php` and `logout.php`.
- Post files on path `/api/posts`: `list.php`, `create.php`, `reply.php` and `report.php`.
- Messages folder on path `/api/messages`.
- Messages files on path `/api/messages`: `send.php`, `inbox.php` and `delete.php`.
- Resources files on path `/api/resources`: `create.php`, `delete.php`, `list.php` and `update.php`.

### Changed
- SCSS file `style.scss` and CSS compiled from SCSS renamed to `main.scss`.
- Renamed the Javascript file from `resources.js` to `messaging.js`.
- Renamed folder `img` to `uploads`.
- Renamed folder `src` to `api`.
- Renamed file on path `/database` from `database.php` to `Database.php`.

### Deprecated
- N/A

### Removed
- JavaScript file `main.js`.
- SQL file.

### Fixed
- N/A

### Security
- N/A

## [0.0.6-alpha] - 2025-11-20

### Added
- SQL database `faithguard_db.sql`.
- Database connection in php `database.php`.
- Different PHP files: `database.php`, `auth.php`, `repository.php`, `debug.php`, `user.php`.

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- Local Backend to V7

### Security
- N/A

## [0.0.5] - 2025-11-19

### Added
- `img`-folder inside the folder `assets`.
- Favicon `favicon.ico`, primary logo, secondary logo and Wordmark added.

### Changed
- Tagline: `Protecting your digital faith with hope and redemption.` changed to `Overcoming addiction through Christ &amp; Protecting your digital faith with hope and redemption.`

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

## [0.0.5-Alpha] - 2025-11-16

### Added
- Page added `resources.html`.
- JavaScript adds redirection to `resources.html` after quiz is submitted.

### Changed
- SCSS changes to clarify which page you are on.
- HTML links to pages, logo will direct to `index.html`.
- Simple JS colour changes for the log.
- Buttons on the Quiz Modal are changed to add more cohesion.
- Colour changes to Warm Ember pallette (`#E9B48A` = Primary-bg, `#956959` = Secondary-bg, `#5C352C` = Accent, `#2A1717` = Highlight and `#3C3C34` = Text-dark).

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- Modal innerHTML inside `main.js` and `SCSS`-classes.
- Footer spacing and style in `style.scss`.

### Security
- N/A


## [0.0.4] - 2025-11-15

### Added
- `<script src="./assets/js/main.js"></script>` to `index.html`
- JavaScript Template using dataregions for `DOM references`, `Callback-Visualisations - show`, `Callback-No Visualisation - callback`, `Data Access - get`, `Event Listeners - listenTo` & `Init / DOMContentLoaded`

### Changed
- SCSS to accommodate the Javascript Modal styling.
- class names in `index.html` to accomodate a better responsiveness towards smaller screens.

### Deprecated
- N/A

### Removed
- Injected JS inside HTML.

### Fixed
- N/A

### Security
- N/A


## [0.0.3] - 2025-11-15

### Added
- Static js inside `index.html`.

### Changed
- Testimonial section no longer in `main`-tag but in an `article`-tag
- SCSS for `.c-impact`, `.c-card` & `.c-footer` little tweaked.

### Deprecated
- N/A

### Removed
- `assests/js/main.js`
- Line 225 in `index.js` (`<script src="./assets/js/main.js"></script>`)

### Fixed
- N/A

### Security
- N/A


## [0.0.2] - 2025-11-15

### Added
- Javascript basics added in `assets/js/main.js`
- CTA section added to `index.html`
- Testimonial section added to `index.html`
- Footer added to `index.html`
- SCSS added for CTA & Testimonial section and footer.

### Changed
- Hero section changed from container to container-fluid.

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

## [0.0.1] - 2025-11-14

### Added
- basic js

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

## [0.0.0] - 2025-11-14

### Added
- Initial release of the website.

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A
