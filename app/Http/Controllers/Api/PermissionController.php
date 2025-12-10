<?php

namespace App\Http\Controllers\Api;

use App\Models\Permission;

class PermissionController extends ApiController
{
    public function index()
    {
        return $this->success(Permission::all());
    }
}
