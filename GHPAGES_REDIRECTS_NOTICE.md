# GitHub Pages - PHP redirect layer

This branch (gh-pages) serves a static demo of the TechShop site.

To make the site safe and clickable for reviewers, all tracked `.php` files were replaced with small HTML redirect pages that forward to the equivalent `.html` static pages (or to `index.html` if no direct mapping exists).

Backups:
- Original PHP contents were saved as `.orig` files next to each replaced `.php` file (for example `auth/login.php.orig`). These backups were created from the previous commit and committed here.

Why:
- GitHub Pages does not run PHP or MySQL. Replacing the public `.php` files with redirects preserves links and prevents 404s while keeping the PHP source available in backups.

Restore or deploy PHP:
- If you need to restore the original PHP content for a PHP-capable host or the main branch, use the `.orig` files or recover from the main branch in the repo.
- For a production PHP site, deploy the original code to hosting that supports PHP + MySQL.

If you want any of the following, pick one and the assistant will perform it:
- Restore selected .php files from backups to the gh-pages branch (not recommended),
- Move all .php originals into a separate folder `php-source/` and remove them from gh-pages,
- Create a README entry in the main branch describing how to run locally on XAMPP.


