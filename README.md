# FaithGuard

![HTML](https://img.shields.io/badge/HTML-E34F26?style=for-the-badge&logo=html5&logoColor=white) ![SCSS](https://img.shields.io/badge/SCSS-CC6699?style=for-the-badge&logo=sass&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black) ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![SQL](https://img.shields.io/badge/SQL-336791?style=for-the-badge&logoColor=white) ![Markdown](https://img.shields.io/badge/Markdown-000000?style=for-the-badge&logo=markdown&logoColor=white)

FaithGuard is a **full-stack, responsive web application** built with PHP, JS, SQL and SCSS. It delivers a clean web presence and a simple development workflow. Customize styles, add assets, and deploy in seconds.

## 🎯 Features

- **Responsive layout** optimized for mobile and desktop  
- **SCSS source** with compiled CSS for easy theming  
- **Organized assets** folder for images, fonts, and styles
- **Organized backend** folder for backend logic  

## 🛠️ Development

Customize and rebuild styles with your preferred SCSS tool:

1. Edit **`assets/css/main.scss`**  
2. Compile to **`assets/css/main.css`**  
   ```bash
   sass assets/css/main.scss assets/css/main.css --watch
   ```
3. Refresh your browser to see changes  

## 📁 Project Structure

### I. Core Application Files
| Path | File | Description |
| --- | --- | --- |
| **Project Root** | `index.php` | **Main Application Entry Point.** Loads all core requirements, performs session checks, fetches user data, and renders the main HTML structure. |
|  | `resources.html` | Client-side page displaying resources. |
|  | `about.html` | Client-side static page placeholder. |
|  | `dashboard.html` | Client-side placeholder for the logged-in user dashboard. |
|  | `faithguard.sql` | **Complete Database Schema** (Tables, Indexes, Initial Data). Used for initial setup in phpMyAdmin. |
|  | `.env` | Local environment variables (e.g., local DB credentials, **ignored by Git**). |
|  | `.gitignore` | Defines files/folders to exclude from version control. |
|  | `README.md` | Project overview and setup instructions. |
### II. Backend PHP Logic (Server Endpoints)
All server-side processing, database interaction, and API endpoints reside here.

| Path | File / Folder | Purpose |
| --- | --- | --- |
| **`/api/`** | `resources_fetch.php` | Endpoint for fetching and filtering resources (GET requests from the client). |
|  | `testimonies_fetch.php` | Optional endpoint for fetching dynamic quotes (e.g., for the home page). |
| **`/api/auth/`** | `login.php` | **Handles POST requests for user authentication and session creation.** |
|  | `register.php` | Handles POST requests for new user sign-up and password hashing. |
|  | `logout.php` | Destroys the user's PHP session and logs them out. |
| **`/api/...`** | `/admin/`, `/posts/`, etc. | Contains various other logic endpoints for core application features. |
| **`/db/`** | `database.php` | **PDO Connection Class.** Defines the static `getConnection()` method and error handling. |
|  | `FaithGuardRepository.php` | **Data Access Layer (DAL).** Contains static methods (e.g., `getUserByEmail`) that execute queries using the `Database` class. |
### III. Client-Side Assets
| Path | File | Purpose |
| --- | --- | --- |
| **`/assets/`** |  | Main directory for client assets. |
|  | `/css/style.scss` | Original SASS/SCSS file for styling (The source file). |
|  | `/css/style.css` | **Compiled CSS** (The file linked in the HTML `<head>`). |
|  | `/js/main.js` | Core JavaScript logic, UI initialization, and general event listeners. |
|  | `/js/resources.js` | JavaScript specific to resources filtering and display. |
|  | `/img/` | Contains project logos, favicon, and background images. |

## 🚧 Future Plans

The project is now in the dynamic backend stage, integrating PHP and SQL into the stack.

- **Dynamic backend** with PHP and SQL  
- **Interactive features** via JavaScript  
- **Accessibility improvements** and content updates  

## 🗺️ Roadmap

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

---