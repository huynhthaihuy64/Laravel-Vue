<?php
namespace App\Enum;

enum HTTPStatus: int {
    case OK = 200;
    case CREATE = 201;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case ACCESS_DENIED = 403;
    case NOT_FOUND = 404;
    case UNPROCESSABLE_ENTITY = 422;
    case INTERNAL_SERVER_ERROR = 500;
}