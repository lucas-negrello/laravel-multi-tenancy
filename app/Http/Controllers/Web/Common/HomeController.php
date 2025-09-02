<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.common.home');
    }
}
