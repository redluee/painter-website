import type { APIRoute } from 'astro';
import { readThemeSettings } from '../../lib/settings';

export const GET: APIRoute = async () => {
  const settings = await readThemeSettings();

  const css = `:root {
  --theme-accent-1: ${settings.accent1};
  --theme-accent-2: ${settings.accent2};
  --theme-section-bg: ${settings.sectionBg};
  --theme-navbar-bg: ${settings.navbarBg};
}`;

  return new Response(css, {
    headers: {
      'Content-Type': 'text/css',
      'Cache-Control': 'no-cache, no-store, must-revalidate',
    },
  });
};
