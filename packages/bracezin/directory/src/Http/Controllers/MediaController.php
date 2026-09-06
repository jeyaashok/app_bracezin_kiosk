<?php

namespace Directory\Http\Controllers;

use Arr;
use Directory\Traits\DirectoryTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Media;
use Storage;
use UserFcd;

class MediaController extends PackageController
{
    public function getRepository()
    {
        return $this->mediaRepository;
    }

    /**
     * Store a newly created Record in storage.
     *
     * @param  $request
     * @return Response
     */
    public function storeDataInit($input)
    {
        $fileType = Arr::has($input, 'file_type') ? $input['file_type'] : null;
        $disk = Arr::has($input, 'disk') ? $input['disk'] : config('filesystems.default', 'local');
        $input['name'] = Arr::has($input, 'name') ? $input['name'] : $input['file_name'];
        $input['type'] = Arr::has($input, 'type') ? $input['type'] : Media::getTypeByFileType($fileType);
        $urlPath = DirectoryTrait::storeAndGetS3Url($input, true);
        $input['disk'] = $disk;
        $input['location'] = $urlPath;
        $input['url_path'] = $urlPath;
        $input['url'] = in_array($disk, ['public', 's3'], true) ? Storage::disk($disk)->url($urlPath) : null;

        // return $input;
        // $input = UserFcd::appendPersonInfoByUserId($input);
        return $input;
    }

    public function storeResponseInit($item, $input)
    {
        return $this->syncMediaUrl($item);
    }

    public function updateResponseInit($item, $input)
    {
        return $this->syncMediaUrl($item);
    }

    protected function syncMediaUrl($item)
    {
        if (! $item || ! $item->id) {
            return $item;
        }

        $disk = $item->disk ?: config('filesystems.default', 'local');
        $location = $item->location ?: null;

        if (! $location) {
            return $item;
        }

        if ($disk === 'local') {
            $item->url = route('show.media', ['id' => $item->id]);
        } else {
            $item->url = Storage::disk($disk)->url($location);
        }

        $this->repository->update($item->id, ['url' => $item->url]);

        return $item;
    }

    public function showMedia(Request $request, $id)
    {
        $item = ($this->repository) ? $this->repository->findById((int) $id) : null;
        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found.',
            ], 404);
        }

        $disk = $item->disk ?: 's3';
        $location = $item->location ?: null;
        if (! $location || ! Storage::disk($disk)->exists($location)) {
            return response()->json([
                'success' => false,
                'message' => 'Media file not found.',
            ], 404);
        }

        $mime = $item->mime ?: Storage::disk($disk)->mimeType($location) ?: 'application/octet-stream';
        $filename = $item->filename ?: basename($location);
        $stream = Storage::disk($disk)->readStream($location);
        $shouldDownload = filter_var($request->query('download', false), FILTER_VALIDATE_BOOLEAN);

        if (! $stream) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to open media file.',
            ], 404);
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => ($shouldDownload ? 'attachment' : 'inline').'; filename="'.$filename.'"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function deleteMedia(Request $request, $id)
    {
        $item = ($this->repository) ? $this->repository->findById((int) $id) : null;

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found.',
            ], 404);
        }
        if ($item && $item->disk === 'local') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete System media stored on local disk.',
            ], 403);
        }

        $disk = $item->disk ?: config('filesystems.default', 'local');
        $location = $item->location ?: null;
        if ($disk !== 'local') {
            if ($location && Storage::disk($disk)->exists($location)) {
                Storage::disk($disk)->delete($location);
            }

            $this->repository->destroy($item->id, true);
        }

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully.',
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $baseValidation = [
            'file' => 'required|file|max:10240', // Max file size of 10MB for each request payload.
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:50',
            'disk' => 'nullable|string|max:50',
            'upload_id' => 'nullable|string|max:120',
            'chunk_index' => 'nullable|integer|min:0',
            'total_chunks' => 'nullable|integer|min:1',
            'original_file_name' => 'nullable|string|max:255',
            'chunk_checksum' => 'nullable|string|size:64',
            'checksum' => 'nullable|string|size:64',
            'total_size' => 'nullable|integer|min:1',
        ];

        $request->validate($baseValidation);

        $file = $request->file('file');
        $hasChunkMeta = $request->filled('upload_id') || $request->filled('chunk_index') || $request->filled('total_chunks');

        if (! $hasChunkMeta) {
            return $this->storeCompletedMedia($file, $request, $file->getClientOriginalName());
        }

        $request->validate([
            'upload_id' => 'required|string|max:120',
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
        ]);

        $uploadId = $this->sanitizeUploadId($request->input('upload_id'));
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');

        if ($uploadId === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid upload_id.',
            ], 422);
        }

        if ($chunkIndex >= $totalChunks) {
            return response()->json([
                'success' => false,
                'message' => 'chunk_index must be less than total_chunks.',
            ], 422);
        }

        $chunkDir = storage_path('app/chunks/'.$uploadId);
        if (! File::exists($chunkDir)) {
            File::makeDirectory($chunkDir, 0755, true);
        }

        $chunkPath = $chunkDir.'/chunk_'.$chunkIndex.'.part';
        $file->move($chunkDir, 'chunk_'.$chunkIndex.'.part');

        $expectedChunkChecksum = strtolower((string) $request->input('chunk_checksum', ''));
        if ($expectedChunkChecksum !== '') {
            $actualChunkChecksum = hash_file('sha256', $chunkPath);
            if (! hash_equals($expectedChunkChecksum, $actualChunkChecksum)) {
                File::delete($chunkPath);

                return response()->json([
                    'success' => false,
                    'message' => 'Chunk checksum mismatch.',
                    'chunk_index' => $chunkIndex,
                ], 422);
            }
        }

        $completedChunkCount = count(File::glob($chunkDir.'/chunk_*.part') ?: []);
        if ($completedChunkCount < $totalChunks) {
            return response()->json([
                'success' => true,
                'chunk_received' => $chunkIndex,
                'received_chunks' => $completedChunkCount,
                'total_chunks' => $totalChunks,
                'upload_id' => $uploadId,
                'is_complete' => false,
            ], 202);
        }

        $assembledPath = $chunkDir.'/assembled_upload';
        $writeHandle = fopen($assembledPath, 'ab');

        if (! $writeHandle) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to create assembled file.',
            ], 500);
        }

        try {
            ftruncate($writeHandle, 0);
            for ($i = 0; $i < $totalChunks; $i++) {
                $partPath = $chunkDir.'/chunk_'.$i.'.part';
                if (! File::exists($partPath)) {
                    fclose($writeHandle);

                    return response()->json([
                        'success' => false,
                        'message' => 'Missing chunk at index '.$i.'.',
                    ], 422);
                }

                $readHandle = fopen($partPath, 'rb');
                if (! $readHandle) {
                    fclose($writeHandle);

                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to read chunk at index '.$i.'.',
                    ], 500);
                }

                stream_copy_to_stream($readHandle, $writeHandle);
                fclose($readHandle);
            }
        } finally {
            if (is_resource($writeHandle)) {
                fclose($writeHandle);
            }
        }

        $finalFileName = $request->input('original_file_name', $request->input('name', $file->getClientOriginalName()));
        $expectedTotalSize = (int) $request->input('total_size', 0);
        if ($expectedTotalSize > 0) {
            $actualTotalSize = (int) (@filesize($assembledPath) ?: 0);
            if ($actualTotalSize !== $expectedTotalSize) {
                File::delete($assembledPath);

                return response()->json([
                    'success' => false,
                    'message' => 'Final file size mismatch.',
                    'expected_size' => $expectedTotalSize,
                    'actual_size' => $actualTotalSize,
                ], 422);
            }
        }

        $expectedChecksum = strtolower((string) $request->input('checksum', ''));
        if ($expectedChecksum !== '') {
            $actualChecksum = hash_file('sha256', $assembledPath);
            if (! hash_equals($expectedChecksum, $actualChecksum)) {
                File::delete($assembledPath);

                return response()->json([
                    'success' => false,
                    'message' => 'Final checksum mismatch.',
                ], 422);
            }
        }

        $response = $this->storeCompletedMedia($assembledPath, $request, $finalFileName);

        File::deleteDirectory($chunkDir);

        return $response;
    }

    protected function storeCompletedMedia($file, Request $request, $fileName)
    {
        $disk = $request->input('disk', config('filesystems.default', 'local'));
        $mime = is_object($file) && method_exists($file, 'getMimeType') ? $file->getMimeType() : @mime_content_type($file);
        $size = is_object($file) && method_exists($file, 'getSize') ? $file->getSize() : (@filesize($file) ?: null);
        $extension = pathinfo((string) $fileName, PATHINFO_EXTENSION);

        $input = [
            'file_data' => $file,
            'file_name' => $fileName,
            'file_mime' => $mime,
            'file_size' => $size,
            'file_extension' => $extension,
            'name' => $request->input('name', $fileName),
            'type' => $request->input('type', Media::getTypeByFileType($mime)),
            'disk' => $disk,
        ];

        $urlPath = DirectoryTrait::storeAndGetS3Url($input, true);
        $payload = [
            'name' => $input['name'],
            'filename' => $fileName,
            'location' => $urlPath,
            'url' => in_array($disk, ['public', 's3'], true) ? Storage::disk($disk)->url($urlPath) : null,
            'type' => $input['type'],
            'mime' => $mime,
            'disk' => $disk,
            'extension' => $extension,
            'size' => $size,
            'is_local_server' => in_array($disk, ['local', 'public'], true) ? 1 : 0,
        ];

        $mediaItem = $this->repository->store($payload);
        $mediaItem = $this->syncMediaUrl($mediaItem);

        return response()->json([
            'success' => true,
            'data' => $mediaItem,
        ], 201);
    }

    public function uploadProgress(Request $request)
    {
        $request->validate([
            'upload_id' => 'required|string|max:120',
            'total_chunks' => 'nullable|integer|min:1',
        ]);

        $uploadId = $this->sanitizeUploadId($request->input('upload_id'));
        if ($uploadId === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid upload_id.',
            ], 422);
        }

        $chunkDir = storage_path('app/chunks/'.$uploadId);
        $chunkFiles = File::glob($chunkDir.'/chunk_*.part') ?: [];
        $receivedIndices = [];
        foreach ($chunkFiles as $chunkFile) {
            if (preg_match('/chunk_(\d+)\.part$/', (string) $chunkFile, $matches)) {
                $receivedIndices[] = (int) $matches[1];
            }
        }

        sort($receivedIndices);

        $totalChunks = $request->filled('total_chunks') ? (int) $request->input('total_chunks') : null;
        $isComplete = $totalChunks ? (count($receivedIndices) >= $totalChunks) : false;

        return response()->json([
            'success' => true,
            'upload_id' => $uploadId,
            'received_chunks' => count($receivedIndices),
            'total_chunks' => $totalChunks,
            'chunk_indices' => $receivedIndices,
            'is_complete' => $isComplete,
        ]);
    }

    protected function sanitizeUploadId($uploadId)
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $uploadId);
    }
}
