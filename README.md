# 1. FaithGuard
![HTML](https://img.shields.io/badge/HTML-E34F26?style=for-the-badge&logo=html5&logoColor=white) ![SCSS](https://img.shields.io/badge/SCSS-CC6699?style=for-the-badge&logo=sass&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black) ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![SQL](https://img.shields.io/badge/SQL-336791?style=for-the-badge&logoColor=white) ![Markdown](https://img.shields.io/badge/Markdown-000000?style=for-the-badge&logo=markdown&logoColor=white)

FaithGuard is a ***full-stack, responsive web application*** designed to support individuals seeking freedom from digital-influenced addictions. It is a ministry initiative of the TikTok account **[wwtw.be](https://www.tiktok.com/@wwtw.be) [WWTW | Christian Content]**.

WWTW stands for **Walk With The Word**. Our mission is rooted in the truth revealed in the Gospel according to John: that Jesus Christ is the Word (John 1:1), the eternal "I Am" (John 8:58) who provides the light and strength for true restoration.

## 1.1. 🎯 Features

- **Confidential Self-Assessment**: A weighted 25+ question quiz evaluating spiritual and behavioral risk using Likert scales.
- **Interactive Daily Journal**: A private reflection tool with keyword detection that triggers "Battle-Ready" scripture modals for moments of struggle.
- **Relational Resource Library**: Dynamic filtering of faith-based content tagged by addiction type and spiritual focus.
- **Accountability Dashboard**: Includes a "Victory Counter" for tracking check-in streaks and a historical activity timeline.
- **Responsive & Discreet UI**: Optimized for all devices with a "Dark Academia" aesthetic that prioritizes legibility and privacy.  

## 1.2. 🛠️ Development

Customize and rebuild styles with your preferred SCSS tool:

1. Edit **`assets/css/main.scss`**  
2. Compile to **`assets/css/main.css`**  
   ```bash
   sass assets/css/main.scss assets/css/main.css --watch
   ``` 

## 1.3. 📁 Project Structure
### 1.3.1. I. Core Application Files
| Path | File | Description |
| --- | --- | --- |
| **Project Root** | `index.php` | **Main Entry Point**. Handles session hydration, user context, and landing page rendering. |
|  | `policies.php` | Renders legal documentation (ToS, Privacy, Cookies) as defined by **wwtw.be**. |
|  | `quiz.php` | Drives the interactive assessment experience, pulling dynamic questions from the DB. |
|  | `resources.php` | Searchable interface for the relational resource and scripture mapping system. |
|  | `README.md` | Project overview and setup instructions. |
|  | `.htaccess` | Enforces HTTPS and protects sensitive backend/database directories. |
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
|  | `/js/auth.js` | Manages login, registration modals, and session authentication logic. |
|  | `/js/community.js` | Handles community forum interactions, including creating posts, replies, and reports. |
|  | `/js/cookie-banner.js` | Manages the display and storage of user cookie consent preferences. |
|  | `/js/journal.js` | Powers the interactive daily journal, addiction tagging, and battle-ready scripture modals. |
|  | `/js/messaging.js` | Facilitates peer-to-peer messaging, inbox rendering, and message deletion. |
|  | `/js/profile-timeline-modal.js` | Manages the UI for viewing historical progress and activity timelines on the dashboard. |
|  | `/js/profile-pastoral-modal.js` | Handles interactions for spiritual guidance requests and pastoral support modals. |
|  | `/js/progress.js` | Handles progress log submissions, victory tracking, and data visualisation. |
|  | `/js/quiz.js` | Drives the multi-step addiction self-assessment quiz logic and dynamic UI rendering. |
|  | `/js/resources.js` | Controls the fetching, searching, and dynamic filtering of faith-based resources. |
| **`/assets/uploads/`** |  | **Folder for client visual assets such as logos or images.** |
|  | `FaithGuard_Primary_Logo` | Project logo featured on the navigation bar. |
|  | `FaithGuard_Secondary_Logo` | Project logo featured on the footer bar. |
|  | `favicon.ico` | Project logo featured on browser tabs next to the site name. |
|  | `Wordmark_Logo` | Project logo featured on the cookie-banner. |

## 1.4. 🚧 Future Plans
### V0.1.4-alpha:
- Pastoral Support: Direct integration for users to request one-on-one spiritual guidance.
- Enhanced Achievement System: Unlocking "Word Warrior" badges for long-term streaks.
- Prayer Wall: A community space for sharing and answering prayer requests.

## 1.5. 🗺️ Roadmap

This roadmap highlights key versions from the initial commit to the current dynamic backend alpha. It shows how the project evolved over time.

- **v0.0.0** -> **v0.1.0** – Foundation for dynamic backend work.  
- **v0.1.1** – Early PHP and SQL integration.  
- **v0.1.2** – Extended backend features and data handling.  
- **v0.1.3** – Polishing dynamic behavior and fixing issues.  
- **v0.1.4-alpha** – Current dynamic backend alpha stage.  

## 1.6. 🗺️ TO DO

This TODO list is to keep track with certain things/ideas the website needs in the near future:
- [X] Quiz integration
- [X] Relational Resource Seed Data
- [X] Interactive Journal & Scripture Modals
- [X] Victory Counter & Progress Tracking
- [ ] Query URL navigation improvements
- [ ] Resource content
- [ ] TikTok-integrated Prayer log intergration
- [ ] Admin moderation dashboard completion
---