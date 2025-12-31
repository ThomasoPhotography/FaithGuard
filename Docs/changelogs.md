# Changelog

All notable changes to this project will be documented in this file.
## [0.1.4-alpha] - Current

### Added
- Profile layouts for admins and users.
- Admin and User features, for admins: 'Reported post section, Resource management section, Legal & Policy Updates and Messaging', for users: 'Personal Profile and Stats, Progress Log, Latest Quiz Results and Recommendation and Messaging'.
- Resource listing.
- Implement user registration with input validation and modal display.
- Enhance login session management and error handling; update register.php to remove unnecessary PHP tag.
- Implement admin and user profile pages with dynamic data fetching
- Added admin profile page with functionality to manage flagged reports, resources, and legal updates.
- Created user profile page displaying account summary, progress logs, quiz results, and recent messages.
- Introduced API endpoints for creating, deleting, retrieving, listing, and updating policies.
- Integrated cookie consent banner for enhanced user experience.
- `faithguard_db.sql` V2 as certain properties changed for `faithguard_db.sql` V1.
- Implement grid layout for admin profile page and add new sections for legal updates and upcoming features.
- Moderation and policies pages.
- Enhance registration process with error logging and simplify API call in `auth.js`.
- Refactor admin profile layout to improve responsiveness and structure in `main.scss` and `main.css`.
- Restructure admin profile layout for improved organization and responsiveness in `admin/profile.php`.
- Method to retrieve policy content by type.
- Policy updates handled by slug.
- Public policies.
- Registering logic.
- Implement dedicated quiz page and update repository logic.
- Implement relational resource tagging system and enhance quiz integration.
- Introduce ranked pastoral journeys per addiction:
  - Build staged pastoral journeys (hope, discipline, repentance, grace).
  - Rank resources by relevance instead of randomness.
  - Infer pastoral tags from quiz answers and severity.
  - Deliver spiritually guided progression rather than static resource lists.
- Implement journal creation API with situational scripture encouragement.
- Creating User-Facing Summaries and Visuals:
  - `/db/faithguard_db.sql` gained new tables.
  - `/db/FaithGuardRepository.php` changed to accomondate the new tables in `/db/faithguard_db.sql`.
  - `/api/services` gained a new service file to compare former and the latest quizzes in `api/services/quizComparisonService.php`.
- Add quiz timeline preparation and conditional display on user profile.
- C2-I-1: Render a chronological visual timeline of quiz attempts.
- C2-I-2: Last vs Previous Attempt Comparison Cards.
- C2-I-3: Pastoral Summaries: add reusable modal system for timeline details, pastoral summaries with repository-backed rendering which can be built upon.

### Changed
- `README.md` file got a professional make-over.
- Updated `README.md` file to include a TODO list.

### Deprecated
- replace lighten/darken functions with color.adjust for consistent color manipulation in `main.scss`.

### Removed
- `/templates/nav.php`.
- Quiz link in nav.
- Settings link for logged-in state.
- cURL in `api/admin/profile.php`.
- Info stacking and styling for info stack.
- `faithguard_db.sql` V1 as part of database restructuring.
- Placeholder background image from hero section in `main.css`.
- Both `/templates/footer.html` and `/assets/js/footer.js`.
- Unneccesary file in `/api/resources`, namely `/api/resources/list.php`.

### Fixed
- Logout function of php.
- Logout pathing.
- Logging out function.
- Linking `logout.php`.
- Linking the `auth.js` instead of the deleted `nav.js`.
- Profile linking to other important files.
- Profile changes.
- Testing new code lines.
- Moving required files to top.
- Profile-linking.
- Profile-linking logic.
- Resources list in `index.php` and `/assets/js/resources.js`.
- Timeout reset `database.php`.
- Initializing variables in `/api/admin/profile.php`.
- Initializing variables in `/api/users/profile.php`.
- Grid layout in `/api/admin/profile.php`.
- Grid layout SCSS + CSS.
- Grid layout HTML.
- Grid styling.
- Grid styling V2.
- Layout mobile, tablet and desktop.
- Layout styling V2.
- Profile styling.
- Profile styling V2.
- Profile styling V3.
- Profile styling V4.
- Profile styling V5.
- Profile styling V6.
- Review buttons.
- User dashboard layout.
- Defining variable `$userId` in `api/users/profile.php`.
- Update profile layout and enhance styling for progress, quiz, and message sections.
- Enhance profile layout and add styling for user dashboard, progress, quiz, and message sections with enchancing profile layout for admin dashboard.
- Update profile layout and styling for resource management section, including new resource count display.
- Enhance reporting functionality by adding user input for report reasons and improving session validation.
- Update reports handling by fetching reported posts dynamically and adding reports table structure to the database.
- Improve user greeting display and handle potential undefined user data.
- Redirect to registration page if email does not exist during login and clarify default user role in registration.
- Renaming of Admin Resource Management item.
- Enhance resource management display with improved styling and structure.
- Update resource management styling for improved layout and consistency.
- Update resource management styling with importance.
- Improve spacing and formatting in user profile dropdown and dashboard.
- Enhance quiz functionality with scoring weights and improved HTML structure.
- Correct redirect path for user registration in login process.
- Refactor registration process; move modal handling to auth.js and update registration function to include name.
- Standardize formatting and improve error handling in authentication flow.
- Standardize formatting and improve user registration handling in authentication flow.
- Improve code readability by standardizing comments and updating section titles in `index.php`.
- Updated navigation and dropdown menus for better user interaction.
- Refactored resource links in user and admin profiles for consistency.
- Standardize formatting and improve readability in `cookie-banner.js`.
- Update hover color for footer links to improve accessibility and consistency.
- Enhance logout functionality and improve user feedback in `auth.js`.
- Update navigation links and dropdowns for user profile and admin dashboard.
- Update README to reflect application type and improve resource rendering in `resources.js`.
- Enhance user dropdown display in `index.php`.
- Improve user profile layout and add null user variable for consistency.
- Adjust badge styles for better visibility.
- Update image source path for logo in user and admin profile pages for consistency.
- Update logo link in user and admin profile pages for consistent navigation.
- Standardize formatting in admin profile styles for improved readability.
- Refactor admin profile layout for improved organization and readability; update class names for consistency.
- Add cookie policy text variable and update related form.
- Improve spacing for better readability.
- Update policy text retrieval to use new database structure; adjust policy slugs for consistency.
- Improve spacing for better readability in report and message display sections.
- Update policy text retrieval to use new method; ensure content availability check.
- Streamline policy creation and update queries.
- Update generation time and timestamps for policy entries in database.
- Update database connections.
- User types.
- Role linking and logging in.
- Updated the policy handling methods.
- Cookie Policy Handling.
- Policy cards.
- Footer template.
- Footer includes the links to all policies.
- Static coded Footer.
- Static coded Footer V2.
- Static coded Footer V3.
- Footer styling.
- Footer styling V2.
- Footer styling V3.
- Footer styling V4.
- Footer styling V5.
- Footer styling V6.
- Footer styling V7.
- Footer styling V8.
- Policy footer.
- Resource list.
- Implement user dashboard grid layout and dynamic data display.
- Implement authentication flow, dynamic dashboards, and responsive layouts:
  - Auth: Update login, register, and logout APIs to handle JSON/Sessions correctly.
  - Auth: Implement dynamic registration modal in JS for 'User not found' flow.
  - UI: Refactor Admin and User dashboards to use responsive Bootstrap grids.
  - Perf: Replace cURL with direct Repository calls in Admin profile to fix 504 errors.
  - Fix: Resolve undefined variables and pathing issues in profile pages.
  - Style: Update SCSS for profile grids, card tags, and navigation states.
- Login button styling.
- Register link redirection.
- Registering link.
- Enchance quiz UI with selectable answer cards and refine progress styling.
- Quiz generation logic.
- Resolve quiz rendering issues and improve JSON data parsing.
- Refactor harden quiz submit logic with validation, normalization, and multi-addiction weighting.
- Policies & SQL.
- Refactor (user-dashboard) consolidate auth handling, secure data loading, and dashboard rendering.
- Policy pages to look more professional.
- Policy data handling.
- Register modal styling.
- Database SQL.
- User logic.
- Correct user context handling and dashboard data access:
  - Ensure $user remains an associative array, not a boolean.
  - Define and reuse $userId consistently across dashboard queries.
  - Prevent null values passed to htmlspecialchars() and strtotime().
  - Fix role-based profile routing and session guard logic.
  - Stabilize dashboard data loading for progress, quiz results, inbox, and posts.
- Features and auth.
- Profile URLs.
- Quiz section backend linking.
- Database SQL V2.
- Quiz code.
- Quiz logic in `/assets/js/quiz.js` and `/quiz.php`.
- Quiz logic in `/quiz.php`.
- User login logic in `/quiz.php`.
- Quiz data directly imported from database.
- Quiz content logic.
- Database, repository & quiz logic.
- Guard around user_id.
- Improve quiz progress bar with refined easing animation.
- Grid Dashboard Layout.

### Security
- Changing contact link from mailto: to page so the email won't get spammed.
- Update session cookie parameters for improved security and consistency across profile pages.
- Enforce HTTPS and harden server configuration via `.htaccess`.


## [0.1.3-beta] - 2025-12-06

### Added
- Temporary profile, in `/api/admin/profile.php`.
- `/assets/js/aut.js`.

### Changed
- Changed `/templates/` from `nav.html` to `nav.php`.

### Deprecated
- `/templates/nav.php`.

### Removed
- `/assets/js/nav.js`.

### Fixed
- Commented the command line in `index.php`: `require_once __DIR__ . "/api/auth/login.php";`.
- Full update of the file `nav.js` on path: `/assets/js/`.
- Updated the `nav.js` file and `index.php` file.
- Updated the `nav.php` template.
- Added the `/helper/debug.php` file to `index.php`.
- Updated logging in function.
- Updated the `login.php` so logging in works.
- Updated several files on path: `/api/auth` and `index.php`.
- Updated the database files on path: `/db/` and `login.php`.
- Login shows to user in nav.
- Updated listenTo function in `nav.js`.
- Database stablizing.
- Nav inside `index.php`.
- Update on user variables for nav in `index.php`.
- Logging in and quiz.
- Updated `index.php`, `login.php` and `logout.php`.
- SCSS + CSS.
- SCSS + CSS V2.
- SCSS + CSS V3.
- Nav inside `index.php`.
- Updated db.
- Nav if logged in.
- Nav V2.
- Nav V3.
- Nav V4.
- Nav V5.
- Nav V6.
- Nav styling.
- Nav styling V2.
- Login menu.
- Nav styling V3.
- Logged-out button styling.
- Nav styling V4.
- Nav styling V5.
- Nav styling V6.
- Nav styling V7.
- Nav styling V8.
- Nav styling V9.
- Login button styling.
- Login button styling V2.
- Login button styling V3.
- Nav formatting.

### Security
- N/A


## [0.1.3-alpha] - 2025-11-30

### Added
- N/A

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- Update require statements in `login.php` and `register.php` for correct path resolution.
- Update require statements to use relative paths in message handling files.
- Update require statements to use relative paths in post handling files.
- Update require statements to use relative paths in progress handling files.
- Update require statement to use relative path in `submit.php`.
- Update require statements to use relative paths in resource handling files.
- Update require statement to use relative path in `profile.php`.
- Add padding to `.col-md-4` and `.col-md-6` classes in `main.scss`, compiled to `main.css`.
- Enhance visualisation in community, messaging, resources, and quiz scripts.
- Expand FaithGuardRepository with comprehensive CRUD methods for users, analytics, donations, messages, posts, replies, prayers, progress logs, quiz questions, quiz results, resources, resource tags, roles, and sessions.
- Update classnames, version and relese date in `index.php` and linking to `resources.js`.

### Security
- N/A

## [0.1.2] - 2025-11-29

### Added
- Add newline at the end of `login.php` to comply with coding standards.
- Expand `FaithGuardRepository.php` with additional methods for users, analytics, donations, messages, posts, prayers, progress logs, quiz questions, quiz results, resources, resource tags, roles, and sessions.

### Changed
- Update require statements to use relative paths for database connections.
- Correct require statement path in `login.php` for database connection.
- Enhance debugging information and input validation in `login.php`.

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