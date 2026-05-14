import { defineMiddleware } from 'astro/middleware';
import { jwtVerify } from 'jose';
import { readThemeSettings } from './lib/settings';

const secret = import.meta.env.JWT_SECRET;
if (!secret) {
  throw new Error('JWT_SECRET environment variable is required');
}
const JWT_SECRET = new TextEncoder().encode(secret);

const protectedPaths = ['/admin', '/api/admin'];
const loginPath = '/admin/login';

// 'unsafe-inline' is required for script and style because the Quill editor
// injects inline styles and the mobile menu toggle uses inline event handlers.
// A nonce-based approach would be ideal but isn't practical with Quill's dynamic DOM.
const CSP = "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'";

function addSecurityHeaders(response: Response): Response {
  const headers = new Headers(response.headers);
  headers.set('X-Content-Type-Options', 'nosniff');
  headers.set('X-Frame-Options', 'DENY');
  headers.set('Referrer-Policy', 'strict-origin-when-cross-origin');
  headers.set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), interest-cohort=()');
  headers.set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
  headers.set('Content-Security-Policy', CSP);
  return new Response(response.body, {
    status: response.status,
    statusText: response.statusText,
    headers,
  });
}

export const onRequest = defineMiddleware(async (context, next) => {
  const url = new URL(context.request.url);
  const pathname = url.pathname;

  // CSRF: Origin check for state-changing admin API requests
  if (
    pathname.startsWith('/api/admin') &&
    !['GET', 'HEAD'].includes(context.request.method)
  ) {
    const origin = context.request.headers.get('origin');
    const host = context.request.headers.get('host');
    if (origin && host) {
      try {
        const originUrl = new URL(origin);
        if (originUrl.host !== host) {
          return new Response(
            JSON.stringify({ error: 'Ongeldige aanvraag' }),
            { status: 403, headers: { 'Content-Type': 'application/json' } },
          );
        }
      } catch {
        return new Response(
          JSON.stringify({ error: 'Ongeldige aanvraag' }),
          { status: 400, headers: { 'Content-Type': 'application/json' } },
        );
      }
    }
  }

  // Auth check for protected paths
  const isProtected = protectedPaths.some((p) => pathname.startsWith(p));
  if (isProtected && pathname !== loginPath) {
    const token = context.cookies.get('auth')?.value;
    if (!token) {
      return addSecurityHeaders(context.redirect(loginPath));
    }
    try {
      const result = await jwtVerify(token, JWT_SECRET, { algorithms: ['HS256'] });
      const { tokenVersion } = await readThemeSettings();
      if (result.payload.tokenVersion !== tokenVersion) {
        context.cookies.delete('auth', { path: '/' });
        return addSecurityHeaders(context.redirect(loginPath));
      }
    } catch {
      context.cookies.delete('auth', { path: '/' });
      return addSecurityHeaders(context.redirect(loginPath));
    }
  }

  return addSecurityHeaders(await next());
});
