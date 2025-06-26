<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardHeadbuyer extends Controller
{
         public function index()
    {        
        return view('pages.dashboardHeadbuyer.dashboardHeadbuyer');
    }
}
