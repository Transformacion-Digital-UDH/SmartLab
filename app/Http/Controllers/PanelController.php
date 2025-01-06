<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PanelController extends Controller
{
    public function index()
    {
        return Inertia::render('Panel/Index');
    }
}
