import type { APIRoute } from 'astro';
import { saveImage } from '../../../lib/admin';

export const prerender = false;

const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

export const POST: APIRoute = async ({ request }) => {
  try {
    const formData = await request.formData();
    const file = formData.get('image') as File | null;

    if (!file) {
      return new Response(
        JSON.stringify({ error: 'Geen afbeelding geüpload' }),
        { status: 400 },
      );
    }

    if (!ALLOWED_TYPES.includes(file.type)) {
      return new Response(
        JSON.stringify({
          error: 'Ongeldig bestandstype. Alleen JPG, PNG, WebP en AVIF zijn toegestaan.',
        }),
        { status: 400 },
      );
    }

    if (file.size > 10 * 1024 * 1024) {
      return new Response(
        JSON.stringify({ error: 'Bestand is te groot (max 10 MB)' }),
        { status: 400 },
      );
    }

    const buffer = Buffer.from(await file.arrayBuffer());
    const url = await saveImage(buffer, file.name);

    return new Response(JSON.stringify({ url }));
  } catch {
    return new Response(
      JSON.stringify({ error: 'Upload mislukt' }),
      { status: 500 },
    );
  }
};
