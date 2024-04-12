<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRole;

class UserRoleController extends Controller
{
    protected $userRole;
    public function __construct(UserRole $userRole) {
        $this->userRole = $userRole;
    }
    public function index() {
        $data = $this->userRole->all();
        return $data;
    }
}
