<?php

namespace Directory\Imports;

use Directory\Models\Media;
use File;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Storage;
use Str;

class MediaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $medias)
    {
        foreach ($medias as $media) {
            // Media Store Method
            $fileExtn = Str::slug(trim($media['extn']), '');
            $filename = Str::slug(trim($media['name']), '_').'.'.$fileExtn;
            $filelocation = storage_path('app/private/'.$filename);
            if (File::exists($filelocation)) {
                $extension = '.'.$fileExtn;
                $mime = File::mimeType($filelocation) ?: 'application/octet-stream';
                $file_data = [
                    'name' => $filename,
                    'filename' => $filename,
                    'type' => 'image',
                    'location' => $filename,
                    'url' => null,
                    'mime' => $mime,
                    'size' => File::size($filelocation),
                    'disk' => 'local',
                    'extension' => $extension,
                ];
                $file = Media::query()->updateOrCreate(
                    ['filename' => $filename],
                    $file_data
                );

                if ($file && $file->id) {
                    $file->url = route('show.media', ['id' => $file->id]);
                    $file->save();
                }
            }
        }
    }
}
