import type { APIRoute } from 'astro';
import type { ThemeSettings } from '../../../types';
import { readThemeSettings, writeThemeSettings } from '../../../lib/settings';

const HEX_REGEX = /^#[0-9a-fA-F]{6}$/;

export const GET: APIRoute = async () => {
  const settings = await readThemeSettings();
  return new Response(JSON.stringify(settings), {
    headers: { 'Content-Type': 'application/json' },
  });
};

export const POST: APIRoute = async ({ request }) => {
  try {
    const body = await request.json();
    const { accent1, accent2, sectionBg, navbarBg } = body;

    if (!accent1 || !accent2 || !sectionBg || !navbarBg) {
      return new Response(JSON.stringify({ error: 'Alle kleuren zijn verplicht.' }), {
        status: 400,
        headers: { 'Content-Type': 'application/json' },
      });
    }

    for (const [key, value] of Object.entries({ accent1, accent2, sectionBg, navbarBg }) as [string, string][]) {
      if (!HEX_REGEX.test(value)) {
        return new Response(JSON.stringify({ error: `'${key}' is geen geldige hex-kleur (bijv. #1C2B1E).` }), {
          status: 400,
          headers: { 'Content-Type': 'application/json' },
        });
      }
    }

    await writeThemeSettings({ accent1, accent2, sectionBg, navbarBg } as ThemeSettings);

    return new Response(JSON.stringify({ success: true }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' },
    });
  } catch {
    return new Response(JSON.stringify({ error: 'Ongeldige JSON.' }), {
      status: 400,
      headers: { 'Content-Type': 'application/json' },
    });
  }
};
