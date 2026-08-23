Running TechShop (PHP) locally

This repository's gh-pages branch serves a static demo for GitHub Pages. The original PHP source has been moved to `php-source/` in this branch so reviewers can access the server-side files if needed.

To run the original PHP project locally with XAMPP (Windows):

1. Install XAMPP: https://www.apachefriends.org/index.html
2. Copy the `php-source/` folder into XAMPP's htdocs directory, e.g. `C:\xampp\htdocs\TechShop\` (ensure the folder structure matches the original root)
3. Start Apache and MySQL from the XAMPP Control Panel
4. Create a MySQL database and import any provided SQL if available (check php-source/config/ for DB settings)
5. Update config files if needed (php-source/config/config.php or php-source/config/database.php) with DB credentials
6. Visit http://localhost/TechShop/ in your browser

Notes:
- Do NOT commit real credentials into the repo. Use local config changes.
- For production hosting, deploy php-source/ to a hosting provider that supports PHP + MySQL.
