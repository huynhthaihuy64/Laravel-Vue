<?php

namespace App\Http\Services\File;

use App\Models\File;

class FileService
{
    protected $file;
    
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getList($params)
    {
        $files = $this->file->with('user')->paginate($params['limit'] ?? 10);
        return $files;
    }
}
