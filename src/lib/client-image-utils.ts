const MAX_DIMENSION = 1920;
const WEBP_QUALITY = 0.8;

export function formatFileSize(bytes: number): string {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function isHeicOrHeif(file: File): boolean {
  return /\.heic$/i.test(file.name) || /\.heif$/i.test(file.name) || file.type === 'image/heic' || file.type === 'image/heif';
}

async function tryDecodeImage(imageBlob: Blob): Promise<ImageBitmap | HTMLImageElement> {
  try {
    const img = await new Promise<HTMLImageElement>((resolve, reject) => {
      const img = new Image();
      img.onload = () => resolve(img);
      img.onerror = () => reject(new Error());
      img.src = URL.createObjectURL(imageBlob);
    });
    URL.revokeObjectURL(img.src);
    return img;
  } catch {
    return createImageBitmap(imageBlob);
  }
}

export async function compressImage(file: File): Promise<{
  blob: Blob;
  originalSize: number;
  compressedSize: number;
  originalName: string;
}> {
  let imageBlob: Blob;

  if (isHeicOrHeif(file)) {
    try {
      const heic2any = (await import('heic2any')).default;
      const result = await heic2any({ blob: file, toType: 'image/jpeg' });
      imageBlob = result instanceof Blob ? result : result[0];
    } catch {
      imageBlob = file;
    }
  } else {
    imageBlob = file;
  }

  let img: ImageBitmap | HTMLImageElement;
  try {
    img = await tryDecodeImage(imageBlob);
  } catch {
    return {
      blob: file,
      originalSize: file.size,
      compressedSize: file.size,
      originalName: file.name,
    };
  }

  let { width, height } = img;
  if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
    const ratio = Math.min(MAX_DIMENSION / width, MAX_DIMENSION / height);
    width = Math.round(width * ratio);
    height = Math.round(height * ratio);
  }

  const canvas = document.createElement('canvas');
  canvas.width = width;
  canvas.height = height;
  const ctx = canvas.getContext('2d')!;
  ctx.drawImage(img, 0, 0, width, height);

  if (img instanceof ImageBitmap) {
    img.close();
  }

  const blob = await new Promise<Blob>((resolve, reject) => {
    canvas.toBlob((b) => {
      if (b) resolve(b);
      else reject(new Error('Canvas toBlob mislukt'));
    }, 'image/webp', WEBP_QUALITY);
  });

  return {
    blob,
    originalSize: file.size,
    compressedSize: blob.size,
    originalName: file.name,
  };
}
