window.ImageUtils = {
  formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  },

  async compressImage(file) {
    let imageBlob = file;

    // HEIC/HEIF conversion requires heic2any library to be loaded separately
    if (/\.heic$/i.test(file.name) || /\.heif$/i.test(file.name)) {
      if (typeof window.heic2any !== 'undefined') {
        try {
          const result = await window.heic2any({ blob: file, toType: 'image/jpeg' });
          imageBlob = result instanceof Blob ? result : result[0];
        } catch {}
      }
    }

    let img;
    try {
      img = await this._decodeImage(imageBlob);
    } catch {
      return { blob: file, originalSize: file.size, compressedSize: file.size, originalName: file.name };
    }

    let { width, height } = img;
    const MAX = 1920;
    if (width > MAX || height > MAX) {
      const ratio = Math.min(MAX / width, MAX / height);
      width = Math.round(width * ratio);
      height = Math.round(height * ratio);
    }

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0, width, height);

    if (typeof img.close === 'function') img.close();

    const blob = await new Promise((resolve, reject) => {
      canvas.toBlob(b => {
        if (b) resolve(b);
        else reject(new Error('Canvas toBlob mislukt'));
      }, 'image/webp', 0.8);
    });

    return { blob, originalSize: file.size, compressedSize: blob.size, originalName: file.name };
  },

  _decodeImage(imageBlob) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => {
        URL.revokeObjectURL(img.src);
        resolve(img);
      };
      img.onerror = () => reject(new Error());
      img.src = URL.createObjectURL(imageBlob);
    });
  }
};
