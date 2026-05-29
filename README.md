# sebastiaanpeters.nl

Portfolio website for Sebastiaan Peters (schilder), built with [Astro](https://astro.build) 6 (SSR via `@astrojs/vercel`), [Tailwind CSS](https://tailwindcss.com) v4, and vanilla TypeScript.

## Prerequisites

- Node.js >= 22.12.0
- npm

## Getting started

```bash
npm install
cp .env.example .env    # then fill in your env vars
npm run dev
```

Open [http://localhost:4321](http://localhost:4321). To expose the dev server to your local network:

```bash
npm run dev -- --host
```

## Environment variables

See `.env.example` for the full list. Required:

| Variable | Description |
|---|---|
| `ADMIN_EMAIL` | Admin login email |
| `ADMIN_PASSWORD_HASH` | bcrypt hash of the admin password (generate via `node scripts/hash-password.mjs <pw>`) |
| `JWT_SECRET` | Random string ≥ 32 chars for JWT signing |
| `SMTP_HOST` / `SMTP_PORT` / `SMTP_USER` / `SMTP_PASS` | SMTP credentials for contact form email notifications |
| `NOTIFICATION_EMAIL` | Where contact form submissions are emailed (defaults to `info@sebastiaanpeters.nl`) |

## Commands

| Command | Description |
|---|---|
| `npm run dev` | Start dev server on `localhost:4321` |
| `npm run dev -- --host` | Start dev server exposed on the local network |
| `npm run build` | Production build |
| `npm run preview` | Preview production build locally |
| `node scripts/hash-password.mjs <pw>` | Generate a bcrypt hash for `ADMIN_PASSWORD_HASH` |

## Data

No database. All content is stored in JSON files under `data/`:

- `data/content.json` — site content (business info, about me, pricing, partners)
- `data/projects.json` — portfolio projects
- `data/settings.json` — theme settings (accent colours, etc.)
- `data/contact-submissions.json` — contact form submissions (created at runtime)

Static pages read these files at build time. Admin API routes read/write them at runtime.

## Auth

The admin panel (`/admin/*`) and admin API (`/api/admin/*`) are protected by JWT-based authentication stored in an `auth` cookie (httpOnly, 8h expiry). Token invalidation is handled via a `tokenVersion` field in the theme settings.

## Project structure

```
src/
├── components/       # Astro components (ProjectGallery, StarRating)
├── layouts/          # Layouts (Layout, AdminLayout)
├── lib/              # Business logic & data access
│   ├── data.ts       #   Read JSON at build time
│   ├── admin.ts      #   Read/write JSON at runtime
│   ├── settings.ts   #   Theme settings helpers
│   ├── email.ts      #   Nodemailer email sender
│   └── shared.ts     #   Shared utilities
├── pages/            # File-based routing
│   ├── index.astro   #   Homepage
│   ├── projecten/    #   Project pages
│   ├── api/          #   API routes
│   └── admin/        #   Admin UI
├── middleware.ts     # Auth & security headers
├── styles/
│   └── global.css    # Tailwind entrypoint
└── types.ts          # TypeScript interfaces
```

## API routes

| Method | Path | Description |
|---|---|---|
| POST | `/api/auth/login` | Admin login |
| POST | `/api/auth/logout` | Admin logout |
| POST | `/api/contact` | Submit contact form (saves + emails) |
| GET | `/api/theme.css` | Dynamic CSS based on theme settings |
| GET/POST | `/api/admin/projects` | List / create projects |
| PUT/DELETE | `/api/admin/projects/:slug` | Update / delete project |
| GET/PUT | `/api/admin/content` | Read / update site content |
| GET/PUT | `/api/admin/settings` | Read / update theme settings |
| POST | `/api/admin/upload` | Upload image (WebP, max 1920px) |

## Image uploads

- Upload via `POST /api/admin/upload` (multipart, field `image`)
- All images are converted to WebP (max 1920px wide, quality 80) via `sharp`
- Stored in `public/images/` as `<sanitized-name>-<timestamp>.webp`
- Max file size: 10 MB. Allowed types: `jpeg`, `png`, `webp`, `avif`

## Styling

- Tailwind CSS v4 via the Vite plugin — only `@import "tailwindcss"` in `global.css`
- Mobile-first, no ochre, Dutch UI text
- No lint/test/format scripts
