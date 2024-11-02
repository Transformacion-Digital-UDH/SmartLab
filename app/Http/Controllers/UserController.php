<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getResponsables()
    {
        $responsables = User::where('rol', 'Responsable')->get();
        return response()->json($responsables);
    }

}
