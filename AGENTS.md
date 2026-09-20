# AGENTS.md

## Project overview
This repository is a small PHP + MySQL portfolio website for showcasing projects and admin management.

## Key files
- `index.php` — public homepage
- `projects/index.php` and `projects/detail.php` — public project listing/detail pages
- `admin/*.php` — admin dashboard, login, project management
- `includes/*.php` — shared layout and auth helpers
- `config/database.php` — database connection; kept local and ignored by Git
- `uploads/` — uploaded project thumbnails/images

## Working conventions
- Use plain PHP with PDO for database access.
- Keep database configuration in `config/database.php` and do not hardcode credentials in source files.
- Use `require` to include shared PHP files and keep paths relative to the file location.
- Sanitize user-facing output with `htmlspecialchars()` before echoing values.
- Preserve the existing project structure and naming patterns.
- Prefer small, focused changes that match the current code style.

## Security expectations
- Never commit real database credentials or production secrets.
- Avoid exposing raw SQL errors to end users.
- Validate input for IDs, filenames, and uploaded content before using them.
- Keep admin login flow protected through the auth helper in `includes/auth.php`.

## Verification
Before finishing work, run a PHP syntax check on the files you changed:

```bash
php -l path/to/file.php
```

For broader validation, run:

```bash
find . -name "*.php" -print0 | xargs -0 -n1 php -l
```

## Notes
The app is intentionally simple and uses server-side rendering with PHP templates rather than a framework.
