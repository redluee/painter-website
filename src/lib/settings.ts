import fs from 'fs/promises';
import path from 'path';
import type { ThemeSettings } from '../types';

const DATA_DIR = path.resolve(process.cwd(), 'data');

const DEFAULT_SETTINGS: ThemeSettings = {
  accent1: '#1C2B1E',
  accent2: '#2a3d2c',
  sectionBg: '#F0F5EE',
  navbarBg: '#F0F5EE',
  tokenVersion: 1,
};

export async function readThemeSettings(): Promise<ThemeSettings> {
  try {
    const raw = await fs.readFile(path.join(DATA_DIR, 'settings.json'), 'utf-8');
    return { ...DEFAULT_SETTINGS, ...JSON.parse(raw) };
  } catch {
    return { ...DEFAULT_SETTINGS };
  }
}

export async function writeThemeSettings(settings: ThemeSettings): Promise<void> {
  await fs.writeFile(
    path.join(DATA_DIR, 'settings.json'),
    JSON.stringify(settings, null, 2),
    'utf-8',
  );
}
