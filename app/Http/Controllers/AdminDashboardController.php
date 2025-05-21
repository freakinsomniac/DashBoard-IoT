<?php

// AdminDashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard'); // buat view-nya juga nanti
    }
}
