import type { Project, SiteContent } from '../types';
import rawProjects from '../../data/projects.json' with { type: 'json' };
import rawContent from '../../data/content.json' with { type: 'json' };

type RawProject = {
  name: string;
  paintType: string[];
  description: string;
  pictures: string[];
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

export function getAllProjects(): Project[] {
  return projects;
}

export function getProjectBySlug(slug: string): Project | undefined {
  return projects.find((p) => p.slug === slug);
}

export function getSiteContent(): SiteContent {
  return siteContent;
}
