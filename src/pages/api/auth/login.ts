import type { APIRoute } from 'astro';
import { SignJWT } from 'jose';
import bcrypt from 'bcryptjs';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  const ADMIN_EMAIL = import.meta.env.ADMIN_EMAIL;
  const ADMIN_PASSWORD_HASH = import.meta.env.ADMIN_PASSWORD_HASH;
  const JWT_SECRET = import.meta.env.JWT_SECRET;

  if (!ADMIN_EMAIL || !ADMIN_PASSWORD_HASH || !JWT_SECRET) {
    return new Response(JSON.stringify({ error: 'Server configuratie fout' }), {
      status: 500,
    });
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

  const token = await new SignJWT({ email })
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
