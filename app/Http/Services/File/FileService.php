<?php

namespace App\Http\Services\File;

use App\Models\File;

class FileService
{
    public function __construct()
    {
    }

    public function getList($params)
    {
        $files = File::with('user')->paginate($params['limit'] ?? 10);
        return $files;
    }
}
