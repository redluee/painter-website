# sebastiaanpeters.nl

Portfolio website for Sebastiaan Peters (schilder), built with [Slim 4](https://www.slimframework.com/) PHP framework.

## Prerequisites

- PHP >= 8.4
- Composer
- Extensions: `gd`, `mbstring`, `fileinfo`, `json`

## Getting started

```bash
cp .env.example .env    # then fill in your env vars
composer install
php -S localhost:8080 -t public
```

Open [http://localhost:8080](http://localhost:8080).

For Apache/Nginx, point the document root to `public/` — the included `.htaccess` handles URL rewriting.

## Environment variables

See `.env.example` for the full list. Required:

| Variable | Description |
|---|---|
| `ADMIN_EMAIL` | Admin login email |
| `ADMIN_PASSWORD_HASH` | bcrypt hash of the admin password (generate via `composer run hash-password -- <pw>`) |
| `JWT_SECRET` | Random string ≥ 32 chars for JWT signing |
| `SMTP_HOST` / `SMTP_PORT` / `SMTP_USER` / `SMTP_PASS` | SMTP credentials for contact form email notifications |
| `NOTIFICATION_EMAIL` | Where contact form submissions are emailed (defaults to `info@sebastiaanpeters.nl`) |

## Commands

| Command | Description |
|---|---|
| `php -S localhost:8080 -t public` | Start PHP built-in dev server |
| `composer run hash-password -- <pw>` | Generate a bcrypt hash for `ADMIN_PASSWORD_HASH` |

## Data

No database. All content is stored in JSON files under `data/`:

- `data/content.json` — site content (business info, about me, pricing, partners)
- `data/projects.json` — portfolio projects
- `data/settings.json` — theme settings (accent colours, etc.)
- `data/contact-submissions.json` — contact form submissions (created at runtime)

All API routes read/write these files at runtime.

## Auth

The admin panel (`/admin/*`) and admin API (`/api/admin/*`) are protected by JWT-based authentication stored in an `auth` cookie (httpOnly, 8h expiry). Token invalidation is handled via a `tokenVersion` field in `data/settings.json`.

Login has rate limiting: 5 attempts per 15 minutes per IP (file-based).

## Project structure

```
painter-website/
├── data/                        # Runtime JSON data (gitignored except *.json)
│   ├── content.json             #   Site content
│   ├── projects.json            #   Projects array
│   ├── settings.json            #   Theme settings (accent colours, token version)
│   ├── contact-submissions.json #   Contact form submissions (created at runtime)
│   └── rate-logs/               #   Rate limiter logs (gitignored)
├── public/                      # Document root
│   ├── index.php                #   Front controller (Slim app entry point)
│   ├── .htaccess                #   Apache rewrite rules + security headers
│   ├── assets/                  #   Compiled CSS, Quill.js, client-side JS
│   ├── images/                  #   Uploaded images (WebP)
│   └── ...                      #   Static files (favicon, robots.txt, etc.)
├── scripts/
│   └── hash-password.php        # CLI tool: generate bcrypt hash for .env
├── src/
│   ├── Controllers/             # Request handlers
│   │   ├── Public/              #   Public page controllers
│   │   ├── Admin/               #   Admin page controllers
│   │   └── Api/                 #   JSON API controllers
│   ├── Services/                # Business logic
│   │   ├── DataService.php      #   JSON file read/write (atomic writes)
│   │   ├── AuthService.php      #   JWT creation, verification, invalidation
│   │   ├── EmailService.php     #   PHPMailer SMTP sender (lazy init)
│   │   ├── ImageService.php     #   Image upload, WebP conversion (GD driver)
│   │   └── RateLimiter.php      #   File-based IP rate limiting
│   ├── Middleware/
│   │   └── AuthMiddleware.php   #   Auth check + CSRF origin/host validation
│   ├── routes.php               # All Slim route definitions
│   └── helpers.php              # Utility functions (slugify, sanitize, escape)
├── templates/                   # Raw PHP templates (ob_start/ob_get_clean)
│   ├── layouts/                 #   layout.php, admin-layout.php
│   ├── public/                  #   Public page templates
│   ├── admin/                   #   Admin page templates
│   └── components/              #   Reusable components (star-rating, project-gallery)
├── composer.json
├── .env                         # Env vars (gitignored)
├── .env.example
└── .gitignore
```

## API routes

| Method | Path | Description |
|---|---|---|
| POST | `/api/auth/login` | Admin login (rate limited) |
| POST | `/api/auth/logout` | Admin logout (invalidates all tokens) |
| POST | `/api/contact` | Submit contact form (saves + emails, rate limited) |
| GET | `/api/theme.css` | Dynamic CSS based on theme settings |
| GET/POST | `/api/admin/projects` | List / create projects |
| GET/PUT/DELETE | `/api/admin/projects/:slug` | Get / update / delete project |
| GET/PUT | `/api/admin/content` | Read / update site content |
| GET/PUT | `/api/admin/settings` | Read / update theme settings |
| POST | `/api/admin/upload` | Upload image (WebP, max 1920px, 10 MB limit) |

## Image uploads

- Upload via `POST /api/admin/upload` (multipart, field `image`)
- All images are converted to WebP (max 1920px wide, quality 80) via Intervention Image (GD driver)
- Stored in `public/images/` as `<sanitized-name>-<timestamp>.webp`
- Max file size: 10 MB. Allowed types: `jpeg`, `png`, `webp`, `avif`, `heic`, `heif`
- Client-side HEIC/HEIF conversion via canvas (`/assets/client-image-utils.js`)

## Styling

- Tailwind CSS v4 compiled to `/assets/styles.css` (utility classes)
- Custom CSS variables served dynamically via `/api/theme.css`
- Mobile-first, no ochre, Dutch UI text
