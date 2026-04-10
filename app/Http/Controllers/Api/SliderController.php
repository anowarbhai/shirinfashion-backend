<?php

namespace App\Http\Controllers\Api;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SliderController extends BaseController
{
    /**
     * Display a listing of active sliders
     */
    public function index()
    {
        $sliders = Slider::getActiveSliders();
        
        return $this->success($sliders);
    }
}
