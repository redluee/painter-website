import type { APIRoute } from 'astro';
import { readSiteContent, writeSiteContent, sanitizeRichText } from '../../../lib/admin';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const content = await readSiteContent();
    return new Response(JSON.stringify(content));
  } catch {
    return new Response(
      JSON.stringify({ error: 'Inhoud kon niet worden geladen' }),
      { status: 500 },
    );
  }
};

export const POST: APIRoute = async ({ request }) => {
  try {
    const body = await request.json();
    const { businessInfo, aboutMe } = body;

    if (!businessInfo || !aboutMe || !aboutMe.trim()) {
      return new Response(
        JSON.stringify({ error: 'Alle velden zijn verplicht' }),
        { status: 400 },
      );
    }

    const { name, intro, phone, email, location, kvk } = businessInfo;

    if (!name || !intro || !phone || !email || !location || !kvk) {
      return new Response(
        JSON.stringify({ error: 'Alle bedrijfsgegevens zijn verplicht' }),
        { status: 400 },
      );
    }

    if (name.length > 200 || location.length > 200 || intro.length > 1000) {
      return new Response(
        JSON.stringify({ error: 'Een of meer velden zijn te lang' }),
        { status: 400 },
      );
    }

    if (phone.length > 20) {
      return new Response(
        JSON.stringify({ error: 'Telefoonnummer is te lang' }),
        { status: 400 },
      );
    }

    if (email.length > 254 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      return new Response(
        JSON.stringify({ error: 'Ongeldig e-mailadres' }),
        { status: 400 },
      );
    }

    if (kvk.length > 10 || !/^\d+$/.test(kvk)) {
      return new Response(
        JSON.stringify({ error: 'Ongeldig KvK-nummer' }),
        { status: 400 },
      );
    }

    if (aboutMe.length > 20000) {
      return new Response(
        JSON.stringify({ error: '"Over mij" is te lang (max 20000 karakters)' }),
        { status: 400 },
      );
    }

    const content = { businessInfo, aboutMe: sanitizeRichText(aboutMe) };
    await writeSiteContent(content);

    return new Response(JSON.stringify({ success: true }));
  } catch {
    return new Response(
      JSON.stringify({ error: 'Ongeldig verzoek' }),
      { status: 400 },
    );
  }
};
