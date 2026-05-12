import { defineMiddleware } from 'astro/middleware';
import { jwtVerify } from 'jose';

const secret = import.meta.env.JWT_SECRET;
if (!secret) {
  throw new Error('JWT_SECRET environment variable is required');
}
const JWT_SECRET = new TextEncoder().encode(secret);

const protectedPaths = ['/admin', '/api/admin'];
const loginPath = '/admin/login';

export const onRequest = defineMiddleware(async (context, next) => {
  const url = new URL(context.request.url);
  const pathname = url.pathname;

  const isProtected = protectedPaths.some((p) => pathname.startsWith(p));
  if (!isProtected) return next();

  if (pathname === loginPath) return next();

  const token = context.cookies.get('auth')?.value;

  if (!token) {
    return context.redirect(loginPath);
  }

  try {
    await jwtVerify(token, JWT_SECRET, { algorithms: ['HS256'] });
    return next();
  } catch {
    context.cookies.delete('auth', { path: '/' });
    return context.redirect(loginPath);
  }
});
