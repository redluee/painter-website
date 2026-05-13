import type { APIRoute } from 'astro';
import { SignJWT } from 'jose';
import bcrypt from 'bcryptjs';
import { readThemeSettings } from '../../../lib/settings';

export const prerender = false;

const RATE_LIMIT_MAX = 5;
const RATE_LIMIT_WINDOW_MS = 15 * 60 * 1000;

const rateLimitMap = new Map<string, { count: number; resetAt: number }>();

function getClientIp(request: Request): string {
  return (
    request.headers.get('x-forwarded-for')?.split(',')[0]?.trim() ||
    request.headers.get('x-real-ip') ||
    'unknown'
  );
}

function checkRateLimit(ip: string): { allowed: boolean; retryAfter?: number } {
  const now = Date.now();
  const entry = rateLimitMap.get(ip);

  if (!entry || now > entry.resetAt) {
    rateLimitMap.set(ip, { count: 1, resetAt: now + RATE_LIMIT_WINDOW_MS });
    return { allowed: true };
  }

  if (entry.count >= RATE_LIMIT_MAX) {
    const retryAfter = Math.ceil((entry.resetAt - now) / 1000);
    return { allowed: false, retryAfter };
  }

  entry.count++;
  return { allowed: true };
}

function clearRateLimit(ip: string): void {
  rateLimitMap.delete(ip);
}

export const POST: APIRoute = async ({ request, cookies }) => {
  const ADMIN_EMAIL = import.meta.env.ADMIN_EMAIL;
  const ADMIN_PASSWORD_HASH = import.meta.env.ADMIN_PASSWORD_HASH;
  const JWT_SECRET = import.meta.env.JWT_SECRET;

  if (!ADMIN_EMAIL || !ADMIN_PASSWORD_HASH || !JWT_SECRET) {
    return new Response(JSON.stringify({ error: 'Server configuratie fout' }), {
      status: 500,
    });
  }

  const ip = getClientIp(request);
  const { allowed, retryAfter } = checkRateLimit(ip);

  if (!allowed) {
    return new Response(
      JSON.stringify({
        error: 'Te veel inlogpogingen. Probeer het later opnieuw.',
      }),
      {
        status: 429,
        headers: {
          'Content-Type': 'application/json',
          'Retry-After': String(retryAfter),
        },
      },
    );
  }

  let body: { email?: string; password?: string };
  try {
    body = await request.json();
  } catch {
    return new Response(JSON.stringify({ error: 'Ongeldig verzoek' }), {
      status: 400,
    });
  }

  const { email, password } = body;

  if (!email || !password) {
    return new Response(
      JSON.stringify({ error: 'E-mail en wachtwoord zijn verplicht' }),
      { status: 400 },
    );
  }

  const passwordValid = await bcrypt.compare(password, ADMIN_PASSWORD_HASH);
  if (email !== ADMIN_EMAIL || !passwordValid) {
    return new Response(
      JSON.stringify({ error: 'Ongeldige e-mail of wachtwoord' }),
      { status: 401 },
    );
  }

  clearRateLimit(ip);

  const { tokenVersion } = await readThemeSettings();

  const token = await new SignJWT({ email, tokenVersion })
    .setProtectedHeader({ alg: 'HS256' })
    .setIssuedAt()
    .setExpirationTime('8h')
    .sign(new TextEncoder().encode(JWT_SECRET));

  cookies.set('auth', token, {
    path: '/',
    httpOnly: true,
    secure: import.meta.env.PROD,
    sameSite: 'lax',
    maxAge: 60 * 60 * 8,
  });

  return new Response(JSON.stringify({ success: true }));
};
