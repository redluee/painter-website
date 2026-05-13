import type { APIRoute } from 'astro';
import { readSiteContent, writeSiteContent } from '../../../lib/admin';

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

    const content = { businessInfo, aboutMe };
    await writeSiteContent(content);

    return new Response(JSON.stringify({ success: true }));
  } catch {
    return new Response(
      JSON.stringify({ error: 'Ongeldig verzoek' }),
      { status: 400 },
    );
  }
};
