import type { APIRoute } from 'astro';
import sharp from 'sharp';
import { saveImage } from '../../../lib/admin';

export const prerender = false;

const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/heic', 'image/heif'];

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
          error: 'Ongeldig bestandstype.',
        }),
        { status: 400 },
      );
    }

    const buffer = Buffer.from(await file.arrayBuffer());

    try {
      const metadata = await sharp(buffer).metadata();
      if (!metadata.format) throw new Error();
    } catch {
      return new Response(
        JSON.stringify({ error: 'Ongeldig afbeeldingsbestand' }),
        { status: 400 },
      );
    }

    const url = await saveImage(buffer, file.name);

    return new Response(JSON.stringify({ url }));
  } catch (e) {
    console.error('Upload handler error:', e);
    return new Response(
      JSON.stringify({ error: `Upload mislukt: ${e instanceof Error ? e.message : 'Onbekende fout'}` }),
      { status: 500 },
    );
  }
};
