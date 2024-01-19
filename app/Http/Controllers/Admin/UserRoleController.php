<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRole;

class UserRoleController extends Controller
{
    public function index() {
        $data = UserRole::all();
        return $data;
    }
}
