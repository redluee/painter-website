export type RawProject = {
  name: string;
  paintType: string[];
  description: string;
  pictures: string[];
  review?: { stars: number; description: string };
};

export function slugify(name: string): string {
  return name
    .toLowerCase()
    .replace(/[^\w\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim();
}

export function createRateLimiter(max: number, windowMs: number) {
  const map = new Map<string, { count: number; resetAt: number }>();

  return {
    check(ip: string): { allowed: boolean; retryAfter?: number } {
      const now = Date.now();
      const entry = map.get(ip);
      if (!entry || now > entry.resetAt) {
        map.set(ip, { count: 1, resetAt: now + windowMs });
        return { allowed: true };
      }
      if (entry.count >= max) {
        const retryAfter = Math.ceil((entry.resetAt - now) / 1000);
        return { allowed: false, retryAfter };
      }
      entry.count++;
      return { allowed: true };
    },
    clear(ip: string): void {
      map.delete(ip);
    },
  };
}

export function getClientIp(request: Request): string {
  return (
    request.headers.get('x-forwarded-for')?.split(',')[0]?.trim() ||
    request.headers.get('x-real-ip') ||
    'unknown'
  );
}
