import { defineMiddleware } from 'astro:middleware';

export const onRequest = defineMiddleware(async (context, next) => {
  if (context.url.pathname.startsWith('/keystatic')) {
    const session = context.cookies.get('admin-session');
    if (!session || session.value !== 'active') {
      return context.redirect('/admin/login');
    }
  }
  return next();
});
