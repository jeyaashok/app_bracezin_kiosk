async function uploadFileInChunks(file, options = {}) {
  const {
    url = '/api/upload-media',
    uploadId = crypto.randomUUID ? crypto.randomUUID() : `upload-${Date.now()}-${Math.random().toString(16).slice(2)}`,
    chunkSize = 1024 * 1024,
    disk = 'local',
    fileName = file.name,
    mime = file.type || 'application/octet-stream',
    totalSize = file.size,
    onProgress = () => {},
    token = null,
  } = options;

  const totalChunks = Math.ceil(file.size / chunkSize);
  const finalChecksum = await computeSha256(file);

  for (let index = 0; index < totalChunks; index++) {
    const start = index * chunkSize;
    const end = Math.min(start + chunkSize, file.size);
    const chunk = file.slice(start, end);

    const formData = new FormData();
    formData.append('file', chunk, fileName);
    formData.append('upload_id', uploadId);
    formData.append('chunk_index', String(index));
    formData.append('total_chunks', String(totalChunks));
    formData.append('original_file_name', fileName);
    formData.append('name', fileName);
    formData.append('type', mime);
    formData.append('disk', disk);
    formData.append('total_size', String(totalSize));
    formData.append('checksum', finalChecksum);
    formData.append('chunk_checksum', await computeSha256(chunk));

    const headers = {};
    if (token) {
      headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(url, {
      method: 'POST',
      headers,
      body: formData,
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || 'Chunk upload failed');
    }

    const progress = ((index + 1) / totalChunks) * 100;
    onProgress(progress, result);
  }

  return {
    uploadId,
    totalChunks,
    fileName,
    disk,
  };
}

async function computeSha256(fileOrBlob) {
  const buffer = await fileOrBlob.arrayBuffer();
  const hashBuffer = await crypto.subtle.digest('SHA-256', buffer);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  return hashArray.map((b) => b.toString(16).padStart(2, '0')).join('');
}

// Example usage:
// const file = document.getElementById('fileInput').files[0];
// uploadFileInChunks(file, {
//   disk: 'local',
//   chunkSize: 1024 * 1024,
//   onProgress: (percent) => console.log('Upload progress:', percent + '%'),
// }).then((result) => console.log(result)).catch((err) => console.error(err));
