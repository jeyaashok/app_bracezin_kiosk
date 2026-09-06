<div class="directory-chunk-upload">
    <div class="box">
        <input id="directory-chunk-file" type="file" />
        <select id="directory-chunk-disk">
            <option value="local">local</option>
            <option value="public">public</option>
            <option value="s3">s3</option>
        </select>
        <button id="directory-chunk-upload-btn" type="button">Upload</button>

        <div class="progress">
            <div id="directory-chunk-progress" class="progress-bar"></div>
        </div>

        <p id="directory-chunk-status">Waiting for file...</p>
        <pre id="directory-chunk-output">{}</pre>
    </div>
</div>

<script>
    async function directoryComputeSha256(fileOrBlob) {
        const buffer = await fileOrBlob.arrayBuffer();
        const digest = await crypto.subtle.digest('SHA-256', buffer);
        const bytes = Array.from(new Uint8Array(digest));
        return bytes.map((b) => b.toString(16).padStart(2, '0')).join('');
    }

    async function directoryUploadFileInChunks(file, options = {}) {
        const {
            url = '/api/upload-media',
            uploadId = crypto.randomUUID ? crypto.randomUUID() : `upload-${Date.now()}-${Math.random().toString(16).slice(2)}`,
            chunkSize = 1024 * 1024,
            disk = 'local',
            fileName = file.name,
            mime = file.type || 'application/octet-stream',
            totalSize = file.size,
            token = null,
            onProgress = () => {},
        } = options;

        const totalChunks = Math.ceil(file.size / chunkSize);
        const finalChecksum = await directoryComputeSha256(file);

        for (let index = 0; index < totalChunks; index++) {
            const start = index * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const chunk = file.slice(start, end);
            const chunkChecksum = await directoryComputeSha256(chunk);

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
            formData.append('chunk_checksum', chunkChecksum);

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

    document.addEventListener('DOMContentLoaded', function () {
        const uploadButton = document.getElementById('directory-chunk-upload-btn');
        if (!uploadButton) {
            return;
        }

        uploadButton.addEventListener('click', async function () {
            const fileInput = document.getElementById('directory-chunk-file');
            const file = fileInput.files[0];
            const disk = document.getElementById('directory-chunk-disk').value;
            const statusText = document.getElementById('directory-chunk-status');
            const outputBox = document.getElementById('directory-chunk-output');
            const progressBar = document.getElementById('directory-chunk-progress');

            if (!file) {
                statusText.textContent = 'Please choose a file first.';
                return;
            }

            try {
                statusText.textContent = 'Uploading...';

                const result = await directoryUploadFileInChunks(file, {
                    disk,
                    chunkSize: 1024 * 1024,
                    onProgress: (percent, payload) => {
                        progressBar.style.width = percent + '%';
                        statusText.textContent = 'Uploading: ' + percent.toFixed(1) + '%';
                        outputBox.textContent = JSON.stringify(payload, null, 2);
                    },
                });

                progressBar.style.width = '100%';
                statusText.textContent = 'Upload complete.';
                outputBox.textContent = JSON.stringify(result, null, 2);
            } catch (error) {
                statusText.textContent = 'Upload failed.';
                outputBox.textContent = JSON.stringify({ error: error.message }, null, 2);
            }
        });
    });
</script>

<style>
    .directory-chunk-upload {
        max-width: 700px;
        margin: 20px auto;
    }

    .directory-chunk-upload .box {
        border: 1px solid #d8dee4;
        border-radius: 10px;
        padding: 20px;
        background: #fff;
    }

    .directory-chunk-upload input,
    .directory-chunk-upload select,
    .directory-chunk-upload button {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        margin-top: 12px;
    }

    .directory-chunk-upload button {
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .directory-chunk-upload .progress {
        width: 100%;
        background: #eef2f7;
        border-radius: 999px;
        overflow: hidden;
        height: 18px;
        margin-top: 14px;
    }

    .directory-chunk-upload .progress-bar {
        width: 0%;
        height: 100%;
        background: #22c55e;
        transition: width 0.2s ease;
    }

    .directory-chunk-upload pre {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 12px;
        overflow: auto;
        margin-top: 18px;
    }
</style>
