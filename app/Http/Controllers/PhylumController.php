<?php

namespace App\Http\Controllers;

use App\Phylum;

class PhylumController extends Controller
{
    public function index()
    {
        $phyla = Phylum::all();
        return response()->json([
            'total' => $phyla->count(),
            'data' => $phyla,
        ]);
    }
}
