import fs from 'fs/promises';
import path from 'path';
import type { Project, SiteContent } from '../types';
import rawProjects from '../../data/projects.json' with { type: 'json' };
import rawContent from '../../data/content.json' with { type: 'json' };

type RawProject = {
  name: string;
  paintType: string[];
  description: string;
  pictures: string[];
  review?: { stars: number; description: string };
};

function slugify(name: string): string {
  return name
    .toLowerCase()
    .replace(/[^\w\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim();
}

const projects: Project[] = (rawProjects as RawProject[]).map((p) => ({
  ...p,
  slug: slugify(p.name),
}));

const siteContent: SiteContent = rawContent as SiteContent;

export const DEFAULT_IMAGE = '/images/default-image.webp';

export function getAllProjects(): Project[] {
  return projects;
}

export function getProjectBySlug(slug: string): Project | undefined {
  return projects.find((p) => p.slug === slug);
}

export async function getProjectBySlugRuntime(slug: string): Promise<Project | null> {
  const raw = await fs.readFile(path.resolve(process.cwd(), 'data/projects.json'), 'utf-8');
  const items: RawProject[] = JSON.parse(raw);
  const project = items.find((p) => slugify(p.name) === slug);
  return project ? { ...project, slug: slugify(project.name) } : null;
}

export function getSiteContent(): SiteContent {
  return siteContent;
}
