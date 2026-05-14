import type { APIRoute } from 'astro';
import { readProjects, writeProjects, sanitizeRichText } from '../../../../lib/admin';
import { slugify } from '../../../../lib/shared';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const projects = await readProjects();
    return new Response(JSON.stringify(projects));
  } catch {
    return new Response(
      JSON.stringify({ error: 'Projecten konden niet worden geladen' }),
      { status: 500 },
    );
  }
};

export const POST: APIRoute = async ({ request }) => {
  try {
    const body = await request.json();
    const { name, paintType, description, pictures, review } = body;

    if (!name || !description) {
      return new Response(
        JSON.stringify({ error: 'Naam en beschrijving zijn verplicht' }),
        { status: 400 },
      );
    }

    if (name.length > 200) {
      return new Response(
        JSON.stringify({ error: 'Projectnaam is te lang (max 200 karakters)' }),
        { status: 400 },
      );
    }

    if (description.length > 20000) {
      return new Response(
        JSON.stringify({ error: 'Beschrijving is te lang (max 20000 karakters)' }),
        { status: 400 },
      );
    }

    const projects = await readProjects();
    const slug = slugify(name);

    if (projects.some((p) => p.slug === slug)) {
      return new Response(
        JSON.stringify({ error: 'Er bestaat al een project met deze naam' }),
        { status: 409 },
      );
    }

    const project = {
      name,
      slug,
      paintType: Array.isArray(paintType) ? paintType : [],
      description: sanitizeRichText(description),
      pictures: Array.isArray(pictures) ? pictures : [],
      review: review && typeof review.stars === 'number' && review.stars >= 1 && review.stars <= 5
        ? { stars: review.stars, description: String(review.description ?? '') }
        : undefined,
    };

    projects.push(project);
    await writeProjects(projects);

    return new Response(JSON.stringify(project), { status: 201 });
  } catch {
    return new Response(
      JSON.stringify({ error: 'Ongeldig verzoek' }),
      { status: 400 },
    );
  }
};
