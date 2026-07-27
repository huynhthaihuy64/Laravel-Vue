<?php

namespace App\Constants;

class FileConstants
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const MODIFIED_BY = 'modified_by';
    public const MODIFIED_FILENAME = 'modified_file_name';
    public const FILE_NAME = 'file_name';
    public const FILE_PATH = 'file_path';
    public const FILE_SIZE = 'file_size';
    public const FILE_TYPE = 'file_type';
    public const INPUT_FILE = 'file';
    public const INPUT_MULTIPLE_FILE = 'files';
    public const INPUT_DESTINATION_FOLDER = 'folder';
    public const IDS = 'ids';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const CHARACTERS  = [
        "(", ")", "<", ">", "@", ",", ";", ":", "<", ">", ",", "[", "]", "?", "=", " ", "/", "\\"
    ];
    public const INPUT_FILE_EXTENSION = 'extension';
    public const TYPE = 'type';
    public const FILE_STATUS = 'status';
    public const TEMPLATE_DIR = 'app' . DIRECTORY_SEPARATOR . 'templates';
    public const SIZE_FILE_MAX = 5120;
    public const MAX_FILE_UPLOADS = 10;
    public const FOLDER_TEMP = 'temp';
    public const UPLOADS_FOLDER = 'upload';
    public const UPLOADS_IMAGE = 'uploads';
    public const PUBLIC_FOLDER = 'public';
}
