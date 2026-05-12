import fs from 'fs/promises';
import path from 'path';
import type { Project, SiteContent } from '../types';

const DATA_DIR = path.resolve(process.cwd(), 'data');
const IMAGES_DIR = path.resolve(process.cwd(), 'public/images');

export function slugify(name: string): string {
  return name
    .toLowerCase()
    .replace(/[^\w\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim();
}

type RawProject = {
  name: string;
  paintType: string[];
  description: string;
  pictures: string[];
};

export async function readProjects(): Promise<Project[]> {
  const raw = await fs.readFile(path.join(DATA_DIR, 'projects.json'), 'utf-8');
  const items: RawProject[] = JSON.parse(raw);
  return items.map((p) => ({
    ...p,
    slug: slugify(p.name),
  }));
}

export async function writeProjects(projects: Project[]): Promise<void> {
  const raw = projects.map(({ slug: _slug, ...rest }) => rest);
  await fs.writeFile(
    path.join(DATA_DIR, 'projects.json'),
    JSON.stringify(raw, null, 2),
    'utf-8',
  );
}

export async function readSiteContent(): Promise<SiteContent> {
  const raw = await fs.readFile(path.join(DATA_DIR, 'content.json'), 'utf-8');
  return JSON.parse(raw) as SiteContent;
}

export async function writeSiteContent(content: SiteContent): Promise<void> {
  await fs.writeFile(
    path.join(DATA_DIR, 'content.json'),
    JSON.stringify(content, null, 2),
    'utf-8',
  );
}

export async function ensureImagesDir(): Promise<void> {
  try {
    await fs.mkdir(IMAGES_DIR, { recursive: true });
  } catch {
    // dir exists
  }
}

export async function saveImage(
  buffer: Buffer,
  filename: string,
): Promise<string> {
  await ensureImagesDir();

  const ext = path.extname(filename).toLowerCase();
  const name = path.basename(filename, ext);
  const safeName = name.replace(/[^a-zA-Z0-9_-]/g, '_').slice(0, 50);
  const unique = `${safeName}-${Date.now()}.webp`;
  const outputPath = path.join(IMAGES_DIR, unique);

  const sharp = (await import('sharp')).default;
  await sharp(buffer)
    .resize({ width: 1920, withoutEnlargement: true })
    .webp({ quality: 80 })
    .toFile(outputPath);

  return `/images/${unique}`;
}

export async function deleteImageFile(url: string): Promise<void> {
  if (!url.startsWith('/images/')) return;
  const filename = path.basename(url);
  const filePath = path.join(IMAGES_DIR, filename);
  try {
    await fs.unlink(filePath);
  } catch {
    // file may not exist
  }
}
