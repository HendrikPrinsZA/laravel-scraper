<?php

namespace App\Http\Controllers;

use App\Models\Bicycle;
use Illuminate\Http\Request;

class BicycleController extends Controller
{
    public function index(Request $request)
    {
        return Bicycle::paginate();
    }
}
