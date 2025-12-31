# 1. FaithGuard

![HTML](https://img.shields.io/badge/HTML-E34F26?style=for-the-badge&logo=html5&logoColor=white) ![SCSS](https://img.shields.io/badge/SCSS-CC6699?style=for-the-badge&logo=sass&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black) ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![SQL](https://img.shields.io/badge/SQL-336791?style=for-the-badge&logoColor=white) ![Markdown](https://img.shields.io/badge/Markdown-000000?style=for-the-badge&logo=markdown&logoColor=white)

FaithGuard is a **full-stack, responsive web application** built with PHP, JS, SQL and SCSS. It delivers a clean web presence and a simple development workflow. Customize styles, add assets, and deploy in seconds.

## 1.1. 🎯 Features

- **Responsive layout** optimized for mobile and desktop  
- **SCSS source** with compiled CSS for easy theming  
- **Organized assets** folder for images, fonts, and styles
- **Organized backend** folder for backend logic  

## 1.2. 🛠️ Development

Customize and rebuild styles with your preferred SCSS tool:

1. Edit **`assets/css/main.scss`**  
2. Compile to **`assets/css/main.css`**  
   ```bash
   sass assets/css/main.scss assets/css/main.css --watch
   ```
3. Refresh your browser to see changes  

## 1.3. 📁 Project Structure

### 1.3.1. I. Core Application Files
| Path | File | Description |
| --- | --- | --- |
| **Project Root** | `index.php` | **Main Application Entry Point.** Loads all core requirements, performs session checks, fetches user data, and renders the main HTML structure. |
|  | `policies.php` | Client-side page displaying all three types of policies by slug (Privacy Policy, Cookie Policy and Terms of Service). |
|  | `quiz.php` | Client-side with all core requirements, performs session checks, fetches user data, and renders the quiz HTML structure. |
|  | `resources.php` | Client-side page displaying all the resource neatly. |
|  | `README.md` | Project overview and setup instructions. |
|  | `.htaccess` | Cheatcode security list to make HTTP into HTTPS. |
### 1.3.2. II. Backend PHP Logic (Server Endpoints)
All server-side processing, database interaction, and API endpoints reside here.

| Path | File / Folder | Purpose |
| --- | --- | --- |
| **`/admin/`** | | **Folder with all admin endpoint logic.** |
|  | `moderation.php` | Endpoint for fetching all reported posts and/or resources. |
|  | `policies.php` | Endpoint for fetching and updating all of the policies shown on the Client-side page `/policies.php`. |
|  | `profile.php` | Endpoint for the Admin Dashboard. |
| **`/api/auth/`** | | **Folder with all authentication endpoint logic.** |
|  | `login.php` | **Handles POST requests for user authentication and session creation.** |
|  | `register.php` | **Handles POST requests for new user sign-up and password hashing.** |
|  | `logout.php` | **Destroys the user's PHP session and logs them out.** |
| **`/api/...`** | `/helper/`, `/posts/`, etc. | Contains various other logic endpoints for core application features. |
| **`/db/`** | | **Folder with all database endpoint logic.** |
|  | `database.php` | **PDO Connection Class.** Defines the static `getConnection()` method and error handling. |
|  | `FaithGuardRepository.php` | **Data Access Layer (DAL).** Contains static methods (e.g., `getUserByEmail`) that execute queries using the `Database` class. |
|  | `faithguard.sql` | **Complete Database Schema** (Tables, Indexes, Initial Data). Used for initial setup in phpMyAdmin. |
### 1.3.3. III. Client-Side Assets
| Path | File | Purpose |
| --- | --- | --- |
| **`/assets/`** |  | **Main directory for client assets.** |
| **`/assets/css/`** |  | **Folder with client-side styling** |
|  | `/css/main.scss` | Original SASS/SCSS file for styling (The source file). |
|  | `/css/main.css` | **Compiled CSS** (The file linked in the HTML `<head>`). |
| **`/assets/js/`** |  | **Main directory for client assets.** |
|  | `/js/auth.js` ||
|  | `/js/community.js` ||
|  | `/js/cookie-banner.js` ||
|  | `/js/journal.js` ||
|  | `/js/messaging.js` ||
|  | `/js/profile-timeline-modal.js` ||
|  | `/js/progress.js` ||
|  | `/js/quiz.js` ||
|  | `/js/resources.js` ||
| **`/assets/uploads/`** |  | **Folder for client visual assets such as logos or images.** |
|  | `FaithGuard_Primary_Logo` | Project logo featured on the navigation bar. |
|  | `FaithGuard_Secondary_Logo` | Project logo featured on the footer bar. |
|  | `favicon.ico` | Project logo featured on browser tabs next to the site name. |
|  | `Wordmark_Logo` | Project logo featured on the cookie-banner. |

## 1.4. 🚧 Future Plans

The project is now in the dynamic backend stage, integrating PHP and SQL into the stack.

- **Dynamic backend** with PHP and SQL  
- **Interactive features** via JavaScript  
- **Accessibility improvements** and content updates  

## 1.5. 🗺️ Roadmap

This roadmap highlights key versions from the initial commit to the current dynamic backend alpha. It shows how the project evolved over time.

- **v0.0.0** – Initial commit.  
- **v0.0.1** – Basic codes and initial layout.  
- **v0.0.2** – Further building on v0.0.1.  
- **v0.0.3** – Refinements to structure and styles.  
- **v0.0.4** – Additional components and layout tweaks.  
- **v0.0.5** – Improved responsiveness and asset organization.  
- **v0.0.6** – SCSS cleanup and small UI enhancements.  
- **v0.0.7** – Preparation for backend integration.  
- **v0.0.8** – Initial JavaScript utilities and interactions.  
- **v0.0.9** – Stability improvements before minor version bump.  
- **v0.1.0** – Foundation for dynamic backend work.  
- **v0.1.1** – Early PHP and SQL integration.  
- **v0.1.2** – Extended backend features and data handling.  
- **v0.1.3** – Polishing dynamic behavior and fixing issues.  
- **v0.1.4-alpha** – Current dynamic backend alpha stage.  

## 1.6. 🗺️ TO DO

This TODO list is to keep track with certain things/ideas the website needs in the near future:
- [x] Quiz integration
- [ ] Query URL's
- [ ] Resource Content
- [ ] Recommend resources from Quiz Answers
- [ ] Progress
- [ ] Prayers (Personal and whenever I post on TikTok).
---