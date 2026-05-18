// @ts-check
import { defineConfig } from 'astro/config';

import tailwindcss from '@tailwindcss/vite';

import vercel from '@astrojs/vercel';

import sitemap from '@astrojs/sitemap';

// https://astro.build/config
export default defineConfig({
  site: 'https://sebastiaanpeters.nl',
  vite: {
    plugins: [tailwindcss()]
  },

  output: 'server',
  adapter: vercel(),
  integrations: [sitemap({
    filter: (page) => !page.includes('/admin/'),
  })],
});
