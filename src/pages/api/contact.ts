import type { APIRoute } from 'astro';
import { appendContactSubmission } from '../../lib/admin';
import { sendContactNotification } from '../../lib/email';
import { createRateLimiter, getClientIp } from '../../lib/shared';

export const prerender = false;

const contactLimiter = createRateLimiter(3, 60 * 1000);

export const POST: APIRoute = async ({ request }) => {
  const ip = getClientIp(request);
  const { allowed, retryAfter } = contactLimiter.check(ip);
  if (!allowed) {
    return new Response(
      JSON.stringify({ error: 'Te veel verzoeken. Probeer het later opnieuw.' }),
      {
        status: 429,
        headers: {
          'Content-Type': 'application/json',
          'Retry-After': String(retryAfter),
        },
      },
    );
  }
  try {
    const body = await request.json();
    const { name, email, subject, message } = body;

    if (!name || !name.trim()) {
      return new Response(
        JSON.stringify({ error: 'Naam is verplicht' }),
        { status: 400 },
      );
    }

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      return new Response(
        JSON.stringify({ error: 'Ongeldig e-mailadres' }),
        { status: 400 },
      );
    }

    if (!message || !message.trim()) {
      return new Response(
        JSON.stringify({ error: 'Bericht is verplicht' }),
        { status: 400 },
      );
    }

    if (name.length > 200 || email.length > 254 || message.length > 10000) {
      return new Response(
        JSON.stringify({ error: 'Een of meer velden zijn te lang' }),
        { status: 400 },
      );
    }

    const submission = {
      name: name.trim(),
      email: email.trim(),
      subject: subject || 'offerte',
      message: message.trim(),
      createdAt: new Date().toISOString(),
    };

    await appendContactSubmission(submission);

    try {
      await sendContactNotification(submission);
    } catch {
      // Email notification is best-effort; don't break the response
    }

    return new Response(
      JSON.stringify({ success: true, message: 'Bedankt! Uw bericht is ontvangen.' }),
    );
  } catch {
    return new Response(
      JSON.stringify({ error: 'Ongeldig verzoek' }),
      { status: 400 },
    );
  }
};
