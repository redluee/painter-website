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
| `node scripts/hash-password.mjs <pw>` | Generate bcrypt hash with `\$`-escaped `$` for `.env` |

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
- **Contact form** in `src/pages/contact.astro` posts to `/api/contact` — **no handler exists yet**

## Styling
- Tailwind CSS v4: only `@import "tailwindcss"` in `src/styles/global.css` (no `@tailwind` directives)
- Mobile-first. No ochre. Dutch UI text.

## Notable gaps
- `tsconfig.json` sets `jsx: "react-jsx"` but the project uses zero React — likely irrelevant config leftover
- `src/pages/api/admin/projects/index.ts` POST slug-collision check reads `projects` array each time — could be a race under concurrent writes
