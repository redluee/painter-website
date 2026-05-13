import type { APIRoute } from 'astro';
import { readThemeSettings, writeThemeSettings } from '../../../lib/settings';

export const prerender = false;

export const POST: APIRoute = async ({ cookies }) => {
  const settings = await readThemeSettings();
  settings.tokenVersion++;
  await writeThemeSettings(settings);

  cookies.delete('auth', { path: '/' });

  return new Response(JSON.stringify({ success: true }));
};
