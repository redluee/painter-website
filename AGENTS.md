# SebastiaanPeters — Agent Guide

## Stack
- Astro 6 (SSR via `@astrojs/vercel`), Tailwind CSS v4 (Vite plugin), vanilla TS, **no** JS framework
- Node >=22.12.0

## Commands
| Command | What |
|---|---|
| `npm run dev` | Astro dev server |
| `npm run build` | Production build |
| `npm run preview` | Preview production build |
| `node scripts/hash-password.mjs <pw>` | Generate bcrypt hash for `.env` |

No lint/test/format scripts exist.

## Data layer
- **No database.** Content is JSON files: `data/projects.json`, `data/content.json`
- `data/*` is gitignored **except** `data/*.json`
- Non-JSON files in `data/` will be ignored by git
- Admin API (`src/lib/admin.ts`) reads/writes JSON at runtime
- Static pages import data at build time via `src/lib/data.ts`

## Auth
- JWT via `jose`, bcrypt via `bcryptjs`
- Required env vars: `ADMIN_EMAIL`, `ADMIN_PASSWORD_HASH`, `JWT_SECRET`

## Contact form & email
- `POST /api/contact` saves submission to `data/contact-submissions.json` and sends email notification via Nodemailer
- Email notification is best-effort (failure doesn't block the response)
- Required env vars for email: `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`
- Optional: `NOTIFICATION_EMAIL` (defaults to `info@sebastiaanpeters.nl`)
- Cookie `auth`, httpOnly, 8h expiry
- Middleware (`src/middleware.ts`) protects `/admin/*` and `/api/admin/*` (except `/admin/login`)

## Routing
- **Public pages** (`/`, `/projecten`, `/over-mij`, `/contact`): `export const prerender = true` — statically rendered at build
- **API routes** (`/api/*`): `export const prerender = false`
- **Project detail** `/projecten/[slug]`: uses `getProjectBySlugRuntime` (reads disk at runtime), does NOT prerender
- **Admin pages** (`/admin/*`): server-rendered, behind auth middleware

## Images
- Upload endpoint: `POST /api/admin/upload` (multipart, field `image`)
- All images converted to WebP, max 1920px wide, quality 80, via `sharp`
- Stored in `public/images/` as `<sanitized-name>-<timestamp>.webp`
- Max upload 10 MB. Allowed types: `image/jpeg`, `image/png`, `image/webp`, `image/avif`
- **Contact form** in `src/pages/contact.astro` posts to `/api/contact` — saves submission + sends email notification

## Project structure

```
painter-website/
├── data/                        # Runtime JSON data (gitignored except *.json)
│   ├── content.json             #   Site content (business info, about me, etc.)
│   ├── projects.json            #   Projects array
│   ├── settings.json            #   Theme settings (accent colours, etc.)
│   └── contact-submissions.json #   Contact form submissions (created at runtime)
├── public/                      # Static assets (served as-is, not processed)
│   ├── images/                  #   Uploaded + default images (WebP)
│   ├── favicon.ico
│   ├── favicon.svg
│   ├── brushstroke.svg
│   ├── robots.txt
│   └── sebastiaan-profiel.jpg
├── scripts/
│   └── hash-password.mjs        # CLI tool: generate bcrypt hash for .env
├── src/
│   ├── components/              # Reusable Astro components
│   │   ├── ProjectGallery.astro
│   │   └── StarRating.astro
│   ├── layouts/                 # Page layouts
│   │   ├── Layout.astro         #   Public pages layout
│   │   └── AdminLayout.astro    #   Admin pages layout
│   ├── lib/                     # Business logic & data access
│   │   ├── data.ts              #   Read JSON at build time (static pages)
│   │   ├── admin.ts             #   Read/write JSON at runtime (admin API)
│   │   ├── settings.ts          #   Theme settings helpers
│   │   ├── email.ts             #   Nodemailer email sender
│   │   └── shared.ts            #   Shared utilities
│   ├── pages/                   # Astro routes (file-based)
│   │   ├── index.astro          #   Homepage (/)
│   │   ├── contact.astro        #   Contact page
│   │   ├── over-mij.astro       #   About me page
│   │   ├── tarieven.astro       #   Pricing page
│   │   ├── partners.astro       #   Partners page
│   │   ├── privacy.astro        #   Privacy policy
│   │   ├── algemene-voorwaarden.astro  # Terms & conditions
│   │   ├── 404.astro            #   Custom 404
│   │   ├── projecten/           #   Project pages
│   │   │   ├── index.astro      #     Project overview (/projecten)
│   │   │   └── [slug].astro     #     Project detail (server-rendered)
│   │   ├── api/                 #   API routes (all server-rendered)
│   │   │   ├── contact.ts       #     POST /api/contact
│   │   │   ├── theme.css.ts     #     GET /api/theme.css (dynamic CSS)
│   │   │   ├── auth/            #     Auth API
│   │   │   │   ├── login.ts     #       POST /api/auth/login
│   │   │   │   └── logout.ts    #       POST /api/auth/logout
│   │   │   └── admin/           #     Admin API (behind auth middleware)
│   │   │       ├── content.ts   #       CRUD /api/admin/content
│   │   │       ├── settings.ts  #       CRUD /api/admin/settings
│   │   │       ├── upload.ts    #       POST /api/admin/upload (images)
│   │   │       └── projects/    #       CRUD /api/admin/projects
│   │   │           ├── index.ts #         GET/POST /api/admin/projects
│   │   │           └── [slug].ts#         PUT/DELETE /api/admin/projects/:slug
│   │   └── admin/               #   Admin UI pages (behind auth middleware)
│   │       ├── login.astro      #     /admin/login (no auth required)
│   │       ├── dashboard.astro  #     /admin
│   │       ├── content.astro    #     /admin/content
│   │       ├── instellingen.astro #   /admin/instellingen
│   │       └── projects/        #     /admin/projects/*
│   │           ├── index.astro  #       Project list
│   │           ├── new.astro    #       Create project
│   │           └── edit/        #       Edit project
│   │               └── [slug].astro
│   ├── middleware.ts            # Auth middleware (protects /admin/*, /api/admin/*)
│   ├── styles/
│   │   └── global.css           # Tailwind entrypoint + global styles
│   └── types.ts                 # TypeScript interfaces (Project, SiteContent, etc.)
├── astro.config.mjs
├── tsconfig.json
├── package.json
├── .env                         # Env vars (gitignored)
├── .env.example
└── .gitignore
```

## Styling
- Tailwind CSS v4: only `@import "tailwindcss"` in `src/styles/global.css` (no `@tailwind` directives)
- Mobile-first. No ochre. Dutch UI text.

## Notable gaps
- `tsconfig.json` sets `jsx: "react-jsx"` but the project uses zero React — likely irrelevant config leftover
- `src/pages/api/admin/projects/index.ts` POST slug-collision check reads `projects` array each time — could be a race under concurrent writes
