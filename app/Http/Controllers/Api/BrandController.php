<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    public function index()
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        return $this->success($brands);
    }
}
