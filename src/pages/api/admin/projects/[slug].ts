import type { APIRoute } from 'astro';
import {
  readProjects,
  writeProjects,
  deleteImageFile,
  slugify,
  sanitizeRichText,
} from '../../../../lib/admin';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const projects = await readProjects();
    const project = projects.find((p) => p.slug === params.slug);

    if (!project) {
      return new Response(JSON.stringify({ error: 'Project niet gevonden' }), {
        status: 404,
      });
    }

    return new Response(JSON.stringify(project));
  } catch {
    return new Response(
      JSON.stringify({ error: 'Project kon niet worden geladen' }),
      { status: 500 },
    );
  }
};

export const PUT: APIRoute = async ({ request, params }) => {
  try {
    const body = await request.json();
    const projects = await readProjects();
    const index = projects.findIndex((p) => p.slug === params.slug);

    if (index === -1) {
      return new Response(
        JSON.stringify({ error: 'Project niet gevonden' }),
        { status: 404 },
      );
    }

    const { name, description, paintType, pictures, review } = body;

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

    const newSlug = slugify(name);

    const duplicate = projects.findIndex(
      (p) => p.slug === newSlug && p.slug !== params.slug,
    );
    if (duplicate !== -1) {
      return new Response(
        JSON.stringify({ error: 'Er bestaat al een project met deze naam' }),
        { status: 409 },
      );
    }

    const oldPictures = projects[index].pictures;
    const newPictures: string[] = Array.isArray(pictures) ? pictures : [];

    const removed = oldPictures.filter((url) => !newPictures.includes(url));
    for (const url of removed) {
      await deleteImageFile(url);
    }

    projects[index] = {
      name,
      slug: newSlug,
      paintType: Array.isArray(paintType) ? paintType : [],
      description: sanitizeRichText(description),
      pictures: newPictures,
      review: review && typeof review.stars === 'number' && review.stars >= 1 && review.stars <= 5
        ? { stars: review.stars, description: String(review.description ?? '') }
        : undefined,
    };

    await writeProjects(projects);
    return new Response(JSON.stringify(projects[index]));
  } catch {
    return new Response(
      JSON.stringify({ error: 'Ongeldig verzoek' }),
      { status: 400 },
    );
  }
};

export const DELETE: APIRoute = async ({ params }) => {
  const projects = await readProjects();
  const index = projects.findIndex((p) => p.slug === params.slug);

  if (index === -1) {
    return new Response(
      JSON.stringify({ error: 'Project niet gevonden' }),
      { status: 404 },
    );
  }

  for (const url of projects[index].pictures) {
    await deleteImageFile(url);
  }

  projects.splice(index, 1);
  await writeProjects(projects);

  return new Response(JSON.stringify({ success: true }));
};


