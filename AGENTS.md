# SebastiaanPeters — Agent Guide

## Stack
- PHP 8.4+, Slim 4 framework (PSR-7/PSR-15), raw PHP templates (`ob_start`/`ob_get_clean`)
- Apache via `.htaccess` (or PHP built-in server for dev)
- Tailwind CSS v4 (compiled, no build pipeline needed)

## Commands
| Command | What |
|---|---|
| `php -S localhost:8080 -t public` | PHP built-in dev server |
| `composer run hash-password -- <pw>` | Generate bcrypt hash for `.env` |
| `composer install` | Install PHP dependencies |

No lint/test/format scripts exist.

## Data layer
- **No database.** Content is JSON files: `data/projects.json`, `data/content.json`, `data/settings.json`
- `data/*` is gitignored **except** `data/*.json`
- Non-JSON files in `data/` will be ignored by git
- `App\Services\DataService` reads/writes JSON at runtime using atomic writes (`.tmp` + `rename()`)
- Rate limiter logs stored in `data/rate-logs/` (gitignored)

## Auth
- JWT via `firebase/php-jwt`, bcrypt via `password_hash()`/`password_verify()`
- Required env vars: `ADMIN_EMAIL`, `ADMIN_PASSWORD_HASH`, `JWT_SECRET`
- Cookie `auth`, httpOnly, 8h expiry, SameSite=Lax; `Secure` flag added in production
- Token invalidation via `tokenVersion` in `data/settings.json`
- `App\Middleware\AuthMiddleware` (PSR-15) protects `/admin/*` and `/api/admin/*` (except `/admin/login`)

## Contact form & email
- `POST /api/contact` saves submission to `data/contact-submissions.json` and sends email notification via PHPMailer
- Email notification is best-effort (failure doesn't block the response)
- Rate limited: 3 requests per minute per IP (file-based)
- Required env vars for email: `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`
- Optional: `NOTIFICATION_EMAIL` (defaults to `info@sebastiaanpeters.nl`)

## Routing
- **All routes** defined in `src/routes.php` using Slim's `$app->get()`, `$app->post()`, `$app->group()`
- **Public pages** (`/`, `/projecten`, `/over-mij`, `/tarieven`, `/partners`, `/contact`, `/privacy`, `/algemene-voorwaarden`, `/404`) — server-rendered PHP templates
- **Project detail** `/projecten/{slug}` — reads disk at runtime
- **API routes** (`/api/*`) — JSON responses with error handling
- **Admin pages** (`/admin/*`) — server-rendered, behind `AuthMiddleware`
- **Admin API** (`/api/admin/*`) — grouped under Slim group, behind `AuthMiddleware`
- Front controller: `public/index.php` (all requests routed through it via `.htaccess`)

## Images
- Upload endpoint: `POST /api/admin/upload` (multipart, field `image`)
- All images converted to WebP, max 1920px wide, quality 80, via `Intervention Image` (GD driver)
- Stored in `public/images/` as `<sanitized-name>-<timestamp>.webp`
- Max upload 10 MB. Allowed types: `image/jpeg`, `image/png`, `image/webp`, `image/avif`, `image/heic`, `image/heif`
- Client-side HEIC/HEIF conversion in `/public/assets/client-image-utils.js`
- Profile image management: old images cleaned up on change

## Security headers
Applied as PSR-15 middleware in `public/index.php`:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy` (camera, microphone, geolocation disabled)
- `Strict-Transport-Security` (1 year, includeSubDomains, preload)
- `Content-Security-Policy` (allows `'unsafe-inline'` for script/style due to Quill editor)
- `.htaccess` also sets headers for static files
- CSRF protection: Origin/Host header check in `AuthMiddleware` for state-changing admin API requests

## Rate limiting
- Login: 5 attempts per 15 minutes per IP
- Contact form: 3 requests per minute per IP
- File-based storage in `data/rate-logs/`
- `App\Services\RateLimiter` with `check()` and `clear()` methods

## Project structure

```
painter-website/
├── data/                        # Runtime JSON data (gitignored except *.json)
│   ├── content.json             #   Site content (business info, about me, etc.)
│   ├── projects.json            #   Projects array
│   ├── settings.json            #   Theme settings (accent colours, token version)
│   ├── contact-submissions.json #   Contact form submissions (created at runtime)
│   └── rate-logs/               #   Rate limiter logs (gitignored)
├── public/                      # Document root
│   ├── index.php                #   Slim front controller
│   ├── .htaccess                #   Apache rewrite + security headers for static files
│   ├── assets/                  #   Compiled CSS (styles.css), Quill.js, client-image-utils.js
│   ├── images/                  #   Uploaded images (WebP)
│   ├── brushstroke.svg          #   Decorative brushstroke SVG
│   ├── favicon.ico / favicon.svg
│   ├── robots.txt
│   └── sebastiaan-profiel.jpg   #   Default profile image
├── scripts/
│   └── hash-password.php        # CLI: php scripts/hash-password.php <password>
├── src/
│   ├── Controllers/
│   │   ├── Public/              # Public page controllers
│   │   │   ├── HomeController.php
│   │   │   ├── ProjectsController.php
│   │   │   ├── AboutController.php
│   │   │   ├── ContactController.php
│   │   │   ├── TarievenController.php
│   │   │   ├── PartnersController.php
│   │   │   ├── StaticController.php    # privacy, voorwaarden, 404, sitemap
│   │   │   └── SitemapController.php
│   │   ├── Admin/               # Admin page controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── LoginController.php
│   │   │   ├── ContentController.php
│   │   │   ├── SettingsController.php
│   │   │   └── ProjectsController.php
│   │   └── Api/                 # JSON API controllers
│   │       ├── AuthController.php      # login, logout
│   │       ├── ContentController.php   # get, update
│   │       ├── SettingsController.php  # get, update
│   │       ├── ProjectsController.php  # list, create, get, update, delete
│   │       ├── UploadController.php    # upload
│   │       ├── ContactController.php   # submit
│   │       └── ThemeCssController.php  # serve
│   ├── Services/
│   │   ├── DataService.php      # JSON file read/write (atomic via .tmp + rename)
│   │   ├── AuthService.php      # JWT encode/decode/verify, token invalidation
│   │   ├── EmailService.php     # PHPMailer SMTP (lazy-init)
│   │   ├── ImageService.php     # Intervention Image GD — WebP, max 1920px, delete
│   │   └── RateLimiter.php      # File-based IP rate limiting
│   ├── Middleware/
│   │   └── AuthMiddleware.php   # PSR-15: auth check + CSRF origin/host validation
│   ├── routes.php               # All Slim route definitions
│   └── helpers.php              # slugify(), sanitizeRichText(), getClientIp(), escapeHtml()
├── templates/                   # Raw PHP templates
│   ├── layouts/
│   │   ├── layout.php           #   Public layout (SEO meta, OG tags, JSON-LD, nav, footer)
│   │   └── admin-layout.php     #   Admin layout (nav slider, logout)
│   ├── public/
│   │   ├── home.php             #   Hero, recent projects, expertise, CTA
│   │   ├── projecten.php        #   Project grid overview
│   │   ├── project-detail.php   #   Single project detail
│   │   ├── over-mij.php         #   About page
│   │   ├── tarieven.php         #   Pricing page
│   │   ├── partners.php         #   Partners page
│   │   ├── contact.php          #   Contact form
│   │   ├── privacy.php          #   Privacy policy
│   │   ├── voorwaarden.php      #   Terms & conditions
│   │   └── 404.php              #   Not found
│   ├── admin/
│   │   ├── login.php            #   Login form
│   │   ├── dashboard.php        #   Dashboard cards
│   │   ├── content.php          #   Content editor (Quill.js)
│   │   ├── instellingen.php     #   Theme colour settings
│   │   └── projects/
│   │       ├── index.php        #   Project list table
│   │       ├── new.php          #   Create project form
│   │       └── edit.php         #   Edit project form
│   └── components/
│       ├── star-rating.php      #   Star rating renderer
│       └── project-gallery.php  #   Project gallery renderer
├── composer.json
├── .env                         # Env vars (gitignored)
├── .env.example
└── .gitignore
```

## Notable details
- Template rendering uses `ob_start()` / `ob_get_clean()` — no template engine
- Public pages pass `$title`, `$description`, `$currentPath`, `$businessInfo`, `$ogImage` variables to layout
- Admin pages use Quill.js (v2) for rich text editing (loaded from `/assets/quill/`)
- Image compression happens client-side before upload via canvas (`client-image-utils.js`)
- DataService writes atomically: write to `.tmp` file, then `rename()` for crash safety
- Cookie-based auth, not header-based (no `Authorization: Bearer`)
