<?php

namespace App\Constants;

class Constants
{
    // Common
    public const ID = 'id';
    public const RESPONSE_MESSAGE = 'message';
    public const RESPONSE_DATA = 'data';
    public const RESPONSE_CODE = 'code';
    public const RESPONSE_MESSAGE_SUCCESS = 'SUCCESS';
    public const RESPONSE_MESSAGE_FAIL = 'Fail';
    public const RESPONSE_NOT_FOUND = 'Not Found';
    public const RESPONSE_REQUEST_TIMEOUT = 'Request timeout';
    public const RESPONSE_SERVER_ERROR = 'Server error';
    public const RESPONSE_UNAUTHENTICATED = 'Unauthenticated';
    public const RESPONSE_UNAUTHORIZED = 'Unauthorized';
    public const MESSAGE_ERROR = 'Error: ';
    public const GOAL_PERFORMANCE_SET_CELL = '3';
    public const GOAL_PERFORMANCE_FIRST_COLUMN = 'A';
    public const GOAL_PERFORMANCE_LAST_COLUMN = 'AJ';
    public const ERROR_COLUMN = 4;
    public const TYPE_SUCCESS = 0;
    public const TYPE_ERROR = 1;
    public const TYPE_EXCEPTION = 2;

    public const ERROR_MESSAGE = '[Error] ';
    public const PAGINATE = 20;
    public const LIMIT = 20;
    public const OFFSET = 0;
    public const CURRENT_PAGE = 1;
    public const CONDITIONS = ['+', '-', '*', '/', '>=', '<='];
    public const DEFAULT_STATUS = 2;
    public const STATUS_NOACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const ACCEPT_LANGUAGE = 'Accept-Language';

    // Time start and time end in a day
    public const TIME_START = ' 00:00:00';
    public const TIME_END = ' 23:59:59';
    public const MAX_DATE_EXPORT_SLA = 31;
    public const DEFAULT_ADD_DAY = 1;
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 10;
    public const EXPIRE_DEFAULT = 0;
    public const NO_RESULT = 0;
    public const XLSX = 'Xlsx';
    public const FOLDER_FILE_SUCCESS = 'success';
    public const FOLDER_FILE_FAILED = 'backlog';
    public const TEMP = 'temp';
    public const PERMISSION = 0755;

    public const PRODUCT_IMPORT = [
        "Name",
        "Price",
        "Price_Sale",
        "Active",
        "Description",
        "Content",
        "File",
        "Menu"
    ];

    public const CONTENT_ERROR = 'Nội dung lỗi';
}
