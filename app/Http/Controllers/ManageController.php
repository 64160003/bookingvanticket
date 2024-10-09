<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManageController extends Controller
{
    public function show()
    {
        return view('admin.manage');
    } 
}
