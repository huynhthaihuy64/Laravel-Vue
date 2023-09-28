<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UploadService
{
    public function uploadFile($file,$path)
    {
        if ($file) {
            $fileName = $file->getClientOriginalName(); 
            $fileNameUid = time() . "-" . $fileName;
            Storage::disk('public')->put('uploads/' . $path . '/' . $fileNameUid, File::get($file));
            $filePath   = '/storage/' . 'uploads/' . $path . '/' . $fileNameUid;

            return [
                'file_name' => $fileName,
                'file_nameUid' => $fileNameUid,
                'file_path' => $filePath
            ];
        }
    }
    public function getAvatar($request)
    {
        if ($request) {
            try {
                $name = $request->getClientOriginalName();

                $pathFull = 'public/';
                $path = $request->storeAs(
                    $pathFull,
                    $name
                );

                $url = '/storage/' . $name;
                return $url;
            } catch (\Exception $error) {
                return false;
            }
        }
    }

    public function getMultipleFiles($request,$folder) {
        $files = $request->file('file');
        if ($request->hasFile('file')) {
            foreach ($files as $file) {
                try {
                    $name = $file->getClientOriginalName();

                    $pathFull = 'public/'.$folder;
                    $path = $file->storeAs(
                        $pathFull,
                        $name
                    );

                    $url[] = '/storage/' . $path;
                } catch (\Exception $error) {
                    return false;
                }
            }
        }
        return $url;
    }
}
